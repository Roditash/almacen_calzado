<?php
/* ============================================================
   HELPERS / UTILIDADES GLOBALES
   ============================================================ */
if (session_status() === PHP_SESSION_NONE) session_start();

require_once __DIR__ . '/db.php';

/** Escape para HTML */
function h(?string $s): string {
    return htmlspecialchars($s ?? '', ENT_QUOTES, 'UTF-8');
}

/** Carrito en sesión */
function carrito_get(): array {
    return $_SESSION['carrito'] ?? [];
}
function carrito_set(array $c): void {
    $_SESSION['carrito'] = $c;
}
function carrito_total_items(): int {
    $sum = 0;
    foreach (carrito_get() as $it) $sum += (int)$it['cantidad'];
    return $sum;
}
function carrito_total_dinero(): float {
    $sum = 0;
    foreach (carrito_get() as $it) $sum += (float)$it['precio'] * (int)$it['cantidad'];
    return round($sum, 2);
}
function carrito_add(string $codigo): void {
    $producto = db()->findOne('productos', ['codigo' => $codigo]);
    if (!$producto) return;
    $c = carrito_get();
    if (isset($c[$codigo])) {
        $c[$codigo]['cantidad'] += 1;
    } else {
        $c[$codigo] = [
            'codigo'   => $producto['codigo'],
            'nombre'   => $producto['nombre'],
            'marca'    => $producto['marca'],
            'precio'   => (float)$producto['precio'],
            'talla'    => $producto['talla'],
            'color'    => $producto['color'],
            'cantidad' => 1,
        ];
    }
    carrito_set($c);
}
function carrito_update(string $codigo, int $cantidad): void {
    $c = carrito_get();
    if (!isset($c[$codigo])) return;
    if ($cantidad <= 0) { unset($c[$codigo]); }
    else                { $c[$codigo]['cantidad'] = $cantidad; }
    carrito_set($c);
}
function carrito_remove(string $codigo): void {
    $c = carrito_get();
    unset($c[$codigo]);
    carrito_set($c);
}
function carrito_clear(): void {
    $_SESSION['carrito'] = [];
}

/** Mapa de iconos por marca (FontAwesome) */
function icono_marca(string $marca): string {
    $map = [
        'Nike'        => 'fa-solid fa-shoe-prints',
        'Adidas'      => 'fa-solid fa-person-running',
        'Vans'        => 'fa-solid fa-skating',
        'Converse'    => 'fa-solid fa-shoe-prints',
        'Puma'        => 'fa-solid fa-paw',
        'Reebok'      => 'fa-solid fa-dumbbell',
        'New Balance' => 'fa-solid fa-walking',
    ];
    return $map[$marca] ?? 'fa-solid fa-shoe-prints';
}

/** Detectar página activa para nav */
function is_page(string $page): bool {
    $script = basename($_SERVER['SCRIPT_NAME']);
    return $script === $page;
}
