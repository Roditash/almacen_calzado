<?php
require_once __DIR__ . '/includes/helpers.php';

$db = db();

/* ============================================
   EJECUTAR LAS 8 CONSULTAS DEL RUBRO
   ============================================ */
$consultas = [
    [
        'titulo' => '1. Mostrar todos los productos',
        'shell'  => 'db.productos.find()',
        'data'   => $db->find('productos'),
    ],
    [
        'titulo' => '2. Buscar productos de la marca Nike',
        'shell'  => 'db.productos.find({ marca: "Nike" })',
        'data'   => $db->find('productos', ['marca' => 'Nike']),
    ],
    [
        'titulo' => '3. Productos con precio mayor a $25',
        'shell'  => 'db.productos.find({ precio: { $gt: 25 } })',
        'data'   => $db->find('productos', ['precio' => ['$gt' => 25]]),
    ],
    [
        'titulo' => '4. Productos con stock menor a 5',
        'shell'  => 'db.productos.find({ stock: { $lt: 5 } })',
        'data'   => $db->find('productos', ['stock' => ['$lt' => 5]]),
    ],
    [
        'titulo' => '5. Ordenar productos por precio ascendente',
        'shell'  => 'db.productos.find().sort({ precio: 1 })',
        'data'   => $db->find('productos', [], ['sort' => ['precio' => 1]]),
    ],
    [
        'titulo' => '6. Mostrar clientes registrados',
        'shell'  => 'db.clientes.find()',
        'data'   => $db->find('clientes'),
    ],
    [
        'titulo' => '7. Mostrar ventas realizadas',
        'shell'  => 'db.ventas.find()',
        'data'   => $db->find('ventas'),
    ],
    [
        'titulo' => '8. Buscar una venta por número (V001)',
        'shell'  => 'db.ventas.findOne({ numero_venta: "V001" })',
        'data'   => array_filter([$db->findOne('ventas', ['numero_venta' => 'V001'])]),
    ],
];

$stats = $db->stats();

/* Galería de evidencias (imágenes ya existentes) */
$evidencias = [
    ['img'=>'images/evidencia1.png','titulo'=>'Listar Productos',       'desc'=>'db.productos.find()'],
    ['img'=>'images/evidencia2.png','titulo'=>'Filtro por Marca Nike',  'desc'=>'{ marca: "Nike" }'],
    ['img'=>'images/evidencia3.png','titulo'=>'Precio mayor a $25',     'desc'=>'{ precio: { $gt: 25 } }'],
    ['img'=>'images/evidencia4.png','titulo'=>'Stock menor a 5',        'desc'=>'{ stock: { $lt: 5 } }'],
    ['img'=>'images/evidencia5.png','titulo'=>'Consulta con Sort',      'desc'=>'sort({ precio: 1 })'],
    ['img'=>'images/evidencia6.png','titulo'=>'Listar Clientes',        'desc'=>'db.clientes.find()'],
    ['img'=>'images/evidencia7.png','titulo'=>'Listar Ventas',          'desc'=>'db.Ventas.find()'],
    ['img'=>'images/evidencia8.png','titulo'=>'Buscar Venta V001',      'desc'=>'{ numero_venta: "V001" }'],
];

$page_title = 'Base de Datos';
include __DIR__ . '/includes/header.php';
?>

<section class="db-section">

  <div class="section-header">
    <span class="section-tag">MongoDB</span>
    <h2>Panel de la Base de Datos</h2>
    <p>Base de datos <strong><?= h($db->dbName) ?></strong> — Estado de conexión, colecciones, consultas y evidencias</p>
  </div>

  <!-- =====================================================
       1) ESTADO DE CONEXIÓN
       ===================================================== -->
  <div class="db-status-card <?= $db->connected ? 'ok' : 'err' ?>">
    <div class="db-status-left">
      <div class="db-status-icon">
        <i class="fas <?= $db->connected ? 'fa-circle-check' : 'fa-circle-xmark' ?>"></i>
      </div>
      <div>
        <h3>
          <?= $db->connected ? 'Conexión establecida' : 'Error de conexión' ?>
          <span class="badge-live"><span class="dot"></span> <?= $db->connected ? 'LIVE' : 'DOWN' ?></span>
        </h3>
        <p>
          <?php if ($db->connected): ?>
            Servidor: <code><?= h($db->host) ?></code> · Driver: <code><?= h($db->driver) ?></code> · Latencia: <code><?= h((string)$db->latency_ms) ?> ms</code>
          <?php else: ?>
            <?= h($db->error ?? 'No se pudo conectar') ?>
          <?php endif; ?>
        </p>
      </div>
    </div>
    <div class="db-status-right">
      <div class="meta">
        <span>Base de datos</span>
        <strong><?= h($db->dbName) ?></strong>
      </div>
      <div class="meta">
        <span>Colecciones</span>
        <strong><?= count($db->listCollections()) ?></strong>
      </div>
      <div class="meta">
        <span>Documentos</span>
        <strong><?= array_sum(array_column($stats, 'documentos')) ?></strong>
      </div>
    </div>
  </div>

  <!-- =====================================================
       2) MONGO SHELL — ping de prueba
       ===================================================== -->
  <div class="mongo-shell">
    <div class="shell-header">
      <span class="dot red"></span><span class="dot yellow"></span><span class="dot green"></span>
      <span class="shell-title">mongosh — almacen_calzado</span>
    </div>
    <div class="shell-body">
<pre><span class="sh-prompt">almacen_calzado&gt;</span> <span class="sh-cmd">db.runCommand({ ping: 1 })</span>
<span class="sh-out">{ ok: 1 }</span>

<span class="sh-prompt">almacen_calzado&gt;</span> <span class="sh-cmd">show collections</span>
<span class="sh-out"><?php foreach ($db->listCollections() as $c) echo h($c) . "\n"; ?></span>
<span class="sh-prompt">almacen_calzado&gt;</span> <span class="sh-cmd">db.stats()</span>
<span class="sh-out">{
  db: "almacen_calzado",
  collections: <?= count($db->listCollections()) ?>,
  objects: <?= array_sum(array_column($stats, 'documentos')) ?>,
  dataSize: "<?= array_sum(array_column($stats, 'tamano_kb')) ?> KB",
  ok: 1
}</span></pre>
    </div>
  </div>

  <!-- =====================================================
       3) COLECCIONES (cards)
       ===================================================== -->
  <h3 class="db-h3"><i class="fas fa-layer-group"></i> Colecciones</h3>
  <div class="collections-grid">
    <?php foreach ($stats as $col => $info): ?>
      <a href="#col-<?= h($col) ?>" class="collection-card">
        <div class="cc-icon">
          <i class="fas <?= $col==='productos'?'fa-shoe-prints':($col==='clientes'?'fa-users':'fa-cash-register') ?>"></i>
        </div>
        <h4><?= h($col) ?></h4>
        <p class="cc-count"><?= (int)$info['documentos'] ?> documentos</p>
        <p class="cc-size"><?= h((string)$info['tamano_kb']) ?> KB</p>
      </a>
    <?php endforeach; ?>
  </div>

  <!-- =====================================================
       4) VISUALIZADOR ESTILO MONGODB COMPASS
       ===================================================== -->
  <?php foreach ($db->listCollections() as $col):
    $docs = $db->find($col); ?>
    <h3 class="db-h3" id="col-<?= h($col) ?>">
      <i class="fas fa-database"></i> almacen_calzado.<?= h($col) ?>
      <span class="badge-count"><?= count($docs) ?> docs</span>
    </h3>
    <div class="compass-view">
      <?php foreach ($docs as $doc): ?>
        <div class="json-doc">
          <div class="json-doc-header">
            <i class="fas fa-file-code"></i> <span class="oid">_id: ObjectId("<?= h(substr($doc['_id'] ?? '', 0, 24)) ?>")</span>
          </div>
<pre class="json-pretty"><?php
  unset($doc['_id']);
  echo h(json_encode($doc, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
?></pre>
        </div>
      <?php endforeach; ?>
    </div>
  <?php endforeach; ?>

  <!-- =====================================================
       5) CONSULTAS DEL RUBRO (en vivo)
       ===================================================== -->
  <h3 class="db-h3" id="consultas"><i class="fas fa-magnifying-glass-chart"></i> Consultas en vivo (Parte 3 del rubro)</h3>
  <p class="db-sub">Estas 8 consultas se ejecutan en tiempo real contra <code>almacen_calzado</code> al cargar esta página.</p>

  <div class="queries-list">
    <?php foreach ($consultas as $i => $q): ?>
      <details class="query-box" <?= $i < 2 ? 'open' : '' ?>>
        <summary>
          <span class="q-num"><?= str_pad((string)($i+1), 2, '0', STR_PAD_LEFT) ?></span>
          <span class="q-title"><?= h($q['titulo']) ?></span>
          <span class="q-count"><?= count($q['data']) ?> resultado<?= count($q['data'])==1?'':'s' ?></span>
        </summary>
        <div class="query-body">
          <div class="q-shell">
            <span class="sh-prompt">almacen_calzado&gt;</span>
            <span class="sh-cmd"><?= h($q['shell']) ?></span>
          </div>
<pre class="q-output"><?php
  echo h(json_encode(array_values($q['data']), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
?></pre>
        </div>
      </details>
    <?php endforeach; ?>
  </div>

  <!-- =====================================================
       6) EVIDENCIAS POLAROID
       ===================================================== -->
  <h3 class="db-h3" id="evidencias"><i class="fas fa-camera-retro"></i> Evidencias (Capturas de MongoDB)</h3>
  <p class="db-sub">Capturas tomadas durante la ejecución original en MongoDB Atlas — haz clic para ampliar.</p>

  <div class="polaroid-gallery" id="evidence-gallery">
    <?php foreach ($evidencias as $ev): ?>
      <figure class="polaroid" data-img="<?= h($ev['img']) ?>">
        <img src="<?= h($ev['img']) ?>" alt="<?= h($ev['titulo']) ?>" loading="lazy">
        <figcaption class="polaroid-caption">
          <h4><?= h($ev['titulo']) ?></h4>
          <p><?= h($ev['desc']) ?></p>
        </figcaption>
      </figure>
    <?php endforeach; ?>
  </div>

</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
