<?php
/* ============================================================
   CONEXIÓN A LA BASE DE DATOS — almacen_calzado
   ------------------------------------------------------------
   Esta capa simula la API de MongoDB sobre SQLite (PDO) para
   que las consultas se sientan y se vean como las consultas de
   Mongo (find, insert, update, sort, etc.). Los documentos se
   guardan como JSON dentro de cada "colección".
   ============================================================ */

if (!defined('ALMACEN_DB_LOADED')) {
    define('ALMACEN_DB_LOADED', true);
}

class MongoLikeDB {
    public  PDO    $pdo;
    public  string $dbName = 'almacen_calzado';
    public  string $dbFile;
    public  string $host   = 'localhost (PDO+SQLite emulando MongoDB Atlas)';
    public  string $driver = 'mongo-emulator/1.0';
    private array  $colecciones = ['productos', 'clientes', 'ventas'];
    public  bool   $connected = false;
    public  ?string $error = null;
    public  float  $latency_ms = 0.0;

    public function __construct() {
        $this->dbFile = __DIR__ . '/../db/almacen_calzado.sqlite';
        $t0 = microtime(true);
        try {
            $dir = dirname($this->dbFile);
            if (!is_dir($dir)) @mkdir($dir, 0777, true);

            $this->pdo = new PDO('sqlite:' . $this->dbFile);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            $this->pdo->exec('PRAGMA foreign_keys = ON;');

            $this->createCollections();
            $this->seedIfEmpty();

            // Ping de prueba
            $this->pdo->query('SELECT 1')->fetch();
            $this->connected = true;
        } catch (Throwable $e) {
            $this->connected = false;
            $this->error = $e->getMessage();
        }
        $this->latency_ms = round((microtime(true) - $t0) * 1000, 2);
    }

    /* ---------- BAJO NIVEL ---------- */
    private function createCollections(): void {
        foreach ($this->colecciones as $col) {
            $this->pdo->exec("
              CREATE TABLE IF NOT EXISTS $col (
                _id        INTEGER PRIMARY KEY AUTOINCREMENT,
                oid        TEXT UNIQUE,
                document   TEXT NOT NULL,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP
              );
            ");
        }
    }

    private function seedIfEmpty(): void {
        $countProd = (int)$this->pdo->query('SELECT COUNT(*) FROM productos')->fetchColumn();
        if ($countProd > 0) return;

        $base = __DIR__ . '/..';
        $prods = json_decode(file_get_contents($base . '/productos.json'), true) ?: [];
        $clis  = json_decode(file_get_contents($base . '/clientes.json'),   true) ?: [];
        $vents = json_decode(file_get_contents($base . '/ventas.json'),     true) ?: [];

        foreach ($prods as $d) $this->insertOne('productos', $d);
        foreach ($clis  as $d) $this->insertOne('clientes',  $d);
        foreach ($vents as $d) {
            if (isset($d['fecha']) && is_array($d['fecha']) && isset($d['fecha']['$date'])) {
                $d['fecha'] = substr($d['fecha']['$date'], 0, 10);
            }
            $this->insertOne('ventas', $d);
        }
    }

    /* ---------- API ESTILO MONGO ---------- */
    public function generateObjectId(): string {
        // Imita un ObjectId de MongoDB (24 chars hex)
        return bin2hex(random_bytes(12));
    }

    public function insertOne(string $coleccion, array $doc): string {
        $oid = $this->generateObjectId();
        $stmt = $this->pdo->prepare("INSERT INTO $coleccion(oid, document) VALUES (?, ?)");
        $stmt->execute([$oid, json_encode($doc, JSON_UNESCAPED_UNICODE)]);
        return $oid;
    }

    public function find(string $coleccion, array $filter = [], array $opts = []): array {
        $rows = $this->pdo->query("SELECT oid, document FROM $coleccion")->fetchAll();
        $docs = [];
        foreach ($rows as $r) {
            $d = json_decode($r['document'], true);
            $d['_id'] = $r['oid'];
            $docs[] = $d;
        }
        // filtros simples ({campo: valor} y {campo: {$gt|$lt|$gte|$lte|$eq|$ne: x}})
        if (!empty($filter)) {
            $docs = array_values(array_filter($docs, function($d) use ($filter) {
                foreach ($filter as $k => $v) {
                    if (!array_key_exists($k, $d)) return false;
                    if (is_array($v)) {
                        foreach ($v as $op => $val) {
                            switch ($op) {
                                case '$gt':  if (!($d[$k] >  $val)) return false; break;
                                case '$gte': if (!($d[$k] >= $val)) return false; break;
                                case '$lt':  if (!($d[$k] <  $val)) return false; break;
                                case '$lte': if (!($d[$k] <= $val)) return false; break;
                                case '$eq':  if (!($d[$k] == $val)) return false; break;
                                case '$ne':  if (!($d[$k] != $val)) return false; break;
                                case '$in':  if (!in_array($d[$k], (array)$val)) return false; break;
                            }
                        }
                    } else {
                        if ($d[$k] != $v) return false;
                    }
                }
                return true;
            }));
        }
        // sort
        if (!empty($opts['sort'])) {
            foreach (array_reverse($opts['sort'], true) as $campo => $dir) {
                usort($docs, function($a, $b) use ($campo, $dir) {
                    $x = $a[$campo] ?? null; $y = $b[$campo] ?? null;
                    if ($x == $y) return 0;
                    $cmp = ($x < $y) ? -1 : 1;
                    return ($dir == 1) ? $cmp : -$cmp;
                });
            }
        }
        if (!empty($opts['limit'])) $docs = array_slice($docs, 0, (int)$opts['limit']);
        return $docs;
    }

    public function findOne(string $coleccion, array $filter): ?array {
        $r = $this->find($coleccion, $filter, ['limit' => 1]);
        return $r[0] ?? null;
    }

    public function count(string $coleccion, array $filter = []): int {
        return count($this->find($coleccion, $filter));
    }

    public function listCollections(): array {
        return $this->colecciones;
    }

    /* ---------- STATS para el panel ---------- */
    public function stats(): array {
        $out = [];
        foreach ($this->colecciones as $c) {
            $cnt = $this->count($c);
            $size = 0;
            $rows = $this->pdo->query("SELECT document FROM $c")->fetchAll();
            foreach ($rows as $r) $size += strlen($r['document']);
            $out[$c] = [
                'documentos' => $cnt,
                'tamano_kb'  => round($size / 1024, 2),
            ];
        }
        return $out;
    }
}

/** Instancia global */
function db(): MongoLikeDB {
    static $instance = null;
    if ($instance === null) $instance = new MongoLikeDB();
    return $instance;
}
