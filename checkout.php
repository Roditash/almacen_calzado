<?php
require_once __DIR__ . '/includes/helpers.php';

$carrito = carrito_get();
$mensaje = null;
$venta_creada = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($carrito)) {
    $nombre    = trim($_POST['nombre']    ?? '');
    $apellido  = trim($_POST['apellido']  ?? '');
    $telefono  = trim($_POST['telefono']  ?? '');
    $direccion = trim($_POST['direccion'] ?? '');

    if ($nombre && $apellido && $telefono && $direccion) {
        // 1) Registrar / obtener cliente
        $existente = db()->findOne('clientes', ['telefono' => $telefono]);
        if ($existente) {
            $codigoCliente = $existente['codigo_cliente'];
        } else {
            $totalClientes = db()->count('clientes');
            $codigoCliente = 'C' . str_pad((string)($totalClientes + 1), 3, '0', STR_PAD_LEFT);
            db()->insertOne('clientes', [
                'codigo_cliente' => $codigoCliente,
                'nombre'    => $nombre,
                'apellido'  => $apellido,
                'telefono'  => $telefono,
                'direccion' => $direccion,
            ]);
        }

        // 2) Crear venta(s) — una por cada producto del carrito
        $totalVentas = db()->count('ventas');
        $productosTexto = [];
        $totalGeneral = 0;
        foreach ($carrito as $it) {
            $totalVentas++;
            $numVenta = 'V' . str_pad((string)$totalVentas, 3, '0', STR_PAD_LEFT);
            $subtotal = $it['precio'] * $it['cantidad'];
            $totalGeneral += $subtotal;
            db()->insertOne('ventas', [
                'numero_venta' => $numVenta,
                'fecha'    => date('Y-m-d'),
                'cliente'  => $nombre . ' ' . $apellido,
                'producto' => $it['nombre'],
                'cantidad' => (int)$it['cantidad'],
                'total'    => $subtotal,
            ]);
            $productosTexto[] = "{$it['cantidad']}× {$it['nombre']}";
        }

        $venta_creada = [
            'cliente'   => $nombre . ' ' . $apellido,
            'codigo_cliente' => $codigoCliente,
            'productos' => $productosTexto,
            'total'     => $totalGeneral,
            'fecha'     => date('Y-m-d H:i'),
        ];
        carrito_clear();
    } else {
        $mensaje = 'Por favor completa todos los campos.';
    }
}

$page_title = 'Checkout';
include __DIR__ . '/includes/header.php';
?>

<section class="checkout-section">
  <div class="section-header">
    <span class="section-tag">Pago</span>
    <h2>Finalizar Compra</h2>
    <p>Tus datos se guardarán en la colección <code>clientes</code> y la venta en <code>ventas</code></p>
  </div>

  <?php if ($venta_creada): ?>
    <div class="checkout-success">
      <div class="success-icon"><i class="fas fa-check-circle"></i></div>
      <h3>¡Compra realizada con éxito!</h3>
      <p>Gracias <strong><?= h($venta_creada['cliente']) ?></strong>, tu pedido fue registrado en la base de datos.</p>
      <div class="success-info">
        <div><span>Código Cliente:</span><strong><?= h($venta_creada['codigo_cliente']) ?></strong></div>
        <div><span>Productos:</span><strong><?= h(implode(', ', $venta_creada['productos'])) ?></strong></div>
        <div><span>Total:</span><strong>$<?= number_format($venta_creada['total'], 2) ?></strong></div>
        <div><span>Fecha:</span><strong><?= h($venta_creada['fecha']) ?></strong></div>
      </div>
      <div style="display:flex;gap:12px;justify-content:center;flex-wrap:wrap;margin-top:20px;">
        <a href="index.php#productos" class="btn btn-primary"><i class="fas fa-shoe-prints"></i> Seguir Comprando</a>
        <a href="db.php#col-ventas" class="btn btn-secondary" style="background:#1a1a2e;color:#fff;">
          <i class="fas fa-database"></i> Ver Registro en DB
        </a>
      </div>
    </div>
  <?php elseif (empty($carrito)): ?>
    <div class="empty-cart">
      <i class="fas fa-cart-arrow-down"></i>
      <h3>Tu carrito está vacío</h3>
      <a href="index.php#productos" class="btn btn-primary"><i class="fas fa-shoe-prints"></i> Ver Catálogo</a>
    </div>
  <?php else: ?>
    <?php if ($mensaje): ?>
      <div class="alert alert-error"><i class="fas fa-exclamation-triangle"></i> <?= h($mensaje) ?></div>
    <?php endif; ?>

    <div class="checkout-grid">
      <form method="post" class="checkout-form">
        <h3><i class="fas fa-user"></i> Datos del Cliente</h3>
        <div class="form-row">
          <div class="form-field">
            <label>Nombre</label>
            <input type="text" name="nombre" required value="<?= h($_POST['nombre'] ?? '') ?>">
          </div>
          <div class="form-field">
            <label>Apellido</label>
            <input type="text" name="apellido" required value="<?= h($_POST['apellido'] ?? '') ?>">
          </div>
        </div>
        <div class="form-field">
          <label>Teléfono</label>
          <input type="text" name="telefono" required placeholder="7777-1234" value="<?= h($_POST['telefono'] ?? '') ?>">
        </div>
        <div class="form-field">
          <label>Dirección</label>
          <input type="text" name="direccion" required placeholder="Ciudad / Departamento" value="<?= h($_POST['direccion'] ?? '') ?>">
        </div>

        <button type="submit" class="btn btn-primary btn-block">
          <i class="fas fa-lock"></i> Confirmar Pedido — $<?= number_format(carrito_total_dinero(), 2) ?>
        </button>
      </form>

      <aside class="checkout-summary">
        <h3>Tu Pedido</h3>
        <ul>
          <?php foreach ($carrito as $it): ?>
            <li>
              <span><?= (int)$it['cantidad'] ?>× <?= h($it['nombre']) ?></span>
              <strong>$<?= number_format($it['precio'] * $it['cantidad'], 2) ?></strong>
            </li>
          <?php endforeach; ?>
        </ul>
        <hr>
        <div class="summary-row total">
          <span>Total:</span>
          <strong>$<?= number_format(carrito_total_dinero(), 2) ?></strong>
        </div>
      </aside>
    </div>
  <?php endif; ?>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
