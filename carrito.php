<?php
require_once __DIR__ . '/includes/helpers.php';

$msg = null;

// POST → acciones de carrito
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $codigo = $_POST['codigo'] ?? '';
    switch ($action) {
        case 'add':
            if ($codigo) { carrito_add($codigo); $msg = 'Producto añadido al carrito.'; }
            break;
        case 'update':
            $cant = (int)($_POST['cantidad'] ?? 1);
            carrito_update($codigo, $cant);
            $msg = 'Carrito actualizado.';
            break;
        case 'remove':
            carrito_remove($codigo);
            $msg = 'Producto eliminado del carrito.';
            break;
        case 'clear':
            carrito_clear();
            $msg = 'Carrito vaciado.';
            break;
    }

    // Si la petición es AJAX (fetch), devolver JSON
    if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'fetch') {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode([
            'ok'      => true,
            'count'   => carrito_total_items(),
            'total'   => carrito_total_dinero(),
            'message' => $msg,
        ]);
        exit;
    }
    // Para form clásico, PRG
    header('Location: carrito.php?msg=' . urlencode($msg ?? ''));
    exit;
}

$carrito = carrito_get();
$page_title = 'Carrito';
include __DIR__ . '/includes/header.php';
?>

<section class="cart-section">
  <div class="section-header">
    <span class="section-tag">Mi Carrito</span>
    <h2>Tu Carrito de Compras</h2>
    <p>Revisa tus productos seleccionados antes de finalizar la compra</p>
  </div>

  <?php if (!empty($_GET['msg'])): ?>
    <div class="alert alert-success"><i class="fas fa-check-circle"></i> <?= h($_GET['msg']) ?></div>
  <?php endif; ?>

  <?php if (empty($carrito)): ?>
    <div class="empty-cart">
      <i class="fas fa-cart-arrow-down"></i>
      <h3>Tu carrito está vacío</h3>
      <p>Aún no has añadido productos. ¡Explora nuestro catálogo!</p>
      <a href="index.php#productos" class="btn btn-primary"><i class="fas fa-shoe-prints"></i> Ver Catálogo</a>
    </div>
  <?php else: ?>

    <div class="cart-grid">
      <div class="cart-items">
        <?php foreach ($carrito as $item): ?>
          <div class="cart-item">
            <div class="cart-item-icon">
              <i class="<?= h(icono_marca($item['marca'])) ?>"></i>
            </div>
            <div class="cart-item-info">
              <p class="ci-brand"><?= h($item['marca']) ?></p>
              <h4><?= h($item['nombre']) ?></h4>
              <p class="ci-meta">
                <span><i class="fas fa-ruler"></i> Talla <?= h((string)$item['talla']) ?></span>
                <span><i class="fas fa-palette"></i> <?= h($item['color']) ?></span>
                <span><i class="fas fa-barcode"></i> <?= h($item['codigo']) ?></span>
              </p>
            </div>
            <form method="post" class="qty-form">
              <input type="hidden" name="action" value="update">
              <input type="hidden" name="codigo" value="<?= h($item['codigo']) ?>">
              <button type="button" class="qty-btn" onclick="this.nextElementSibling.stepDown();this.form.submit();">−</button>
              <input type="number" name="cantidad" value="<?= (int)$item['cantidad'] ?>" min="0" onchange="this.form.submit()">
              <button type="button" class="qty-btn" onclick="this.previousElementSibling.stepUp();this.form.submit();">+</button>
            </form>
            <div class="ci-price">
              <p class="ci-unit">$<?= number_format($item['precio'], 2) ?> c/u</p>
              <p class="ci-subtotal">$<?= number_format($item['precio'] * $item['cantidad'], 2) ?></p>
            </div>
            <form method="post" class="ci-remove">
              <input type="hidden" name="action" value="remove">
              <input type="hidden" name="codigo" value="<?= h($item['codigo']) ?>">
              <button type="submit" title="Eliminar"><i class="fas fa-trash"></i></button>
            </form>
          </div>
        <?php endforeach; ?>
      </div>

      <aside class="cart-summary">
        <h3>Resumen del Pedido</h3>
        <div class="summary-row">
          <span>Productos:</span>
          <strong><?= carrito_total_items() ?></strong>
        </div>
        <div class="summary-row">
          <span>Subtotal:</span>
          <strong>$<?= number_format(carrito_total_dinero(), 2) ?></strong>
        </div>
        <div class="summary-row">
          <span>Envío:</span>
          <strong style="color:#28a745;">Gratis</strong>
        </div>
        <hr>
        <div class="summary-row total">
          <span>Total:</span>
          <strong>$<?= number_format(carrito_total_dinero(), 2) ?></strong>
        </div>

        <a href="checkout.php" class="btn btn-primary btn-block">
          <i class="fas fa-credit-card"></i> Finalizar Compra
        </a>
        <form method="post" style="margin-top:10px;">
          <input type="hidden" name="action" value="clear">
          <button type="submit" class="btn btn-secondary btn-block" style="background:#f1f1f1;color:#444;border:1px solid #ddd;">
            <i class="fas fa-trash-alt"></i> Vaciar Carrito
          </button>
        </form>
        <a href="index.php#productos" class="continue-link"><i class="fas fa-arrow-left"></i> Seguir comprando</a>
      </aside>
    </div>

  <?php endif; ?>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
