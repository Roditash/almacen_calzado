<?php
require_once __DIR__ . '/includes/helpers.php';

// Filtro por marca por GET
$marca_filtro = $_GET['marca'] ?? 'all';
$filtro = ($marca_filtro === 'all') ? [] : ['marca' => $marca_filtro];
$productos = db()->find('productos', $filtro, ['sort' => ['codigo' => 1]]);

// Marcas únicas para filtros
$todas = db()->find('productos');
$marcas = array_values(array_unique(array_map(fn($p) => $p['marca'], $todas)));
sort($marcas);

$page_title = 'Inicio';
include __DIR__ . '/includes/header.php';
?>

  <!-- BANNER / HERO -->
  <section id="inicio" class="hero-banner">
    <div class="hero-overlay"></div>
    <div class="hero-content">
      <span class="hero-badge">★ Calidad Premium ★</span>
      <h2>Camina con <span class="highlight">Estilo</span></h2>
      <p>Descubre las mejores marcas de calzado: Nike, Adidas, Vans, Puma, Converse y más. Todo en un solo lugar.</p>
      <div class="hero-buttons">
        <a href="#productos" class="btn btn-primary"><i class="fas fa-shopping-bag"></i> Ver Catálogo</a>
        <a href="#contacto" class="btn btn-secondary"><i class="fas fa-phone"></i> Contáctanos</a>
      </div>
    </div>
    <div class="hero-shapes">
      <span class="shape shape-1"></span>
      <span class="shape shape-2"></span>
      <span class="shape shape-3"></span>
    </div>
  </section>

  <!-- INFO RÁPIDA -->
  <section class="features-strip">
    <div class="feature-item">
      <i class="fas fa-truck-fast"></i>
      <div><h4>Envíos Rápidos</h4><p>A todo el país</p></div>
    </div>
    <div class="feature-item">
      <i class="fas fa-medal"></i>
      <div><h4>Marcas Originales</h4><p>100% garantizadas</p></div>
    </div>
    <div class="feature-item">
      <i class="fas fa-tags"></i>
      <div><h4>Mejores Precios</h4><p>Ofertas todo el año</p></div>
    </div>
    <div class="feature-item">
      <i class="fas fa-database"></i>
      <div><h4>BD almacen_calzado</h4><p><a href="db.php" style="color:var(--primary);font-weight:600;">Ver consultas →</a></p></div>
    </div>
  </section>

  <!-- PRODUCTOS -->
  <section id="productos" class="products-section">
    <div class="section-header">
      <span class="section-tag">Catálogo</span>
      <h2>Nuestros Productos</h2>
      <p>Datos servidos en tiempo real desde la base de datos <strong>almacen_calzado</strong> · colección <code>productos</code></p>
    </div>

    <div class="filters-bar">
      <a href="index.php#productos" class="filter-btn <?= $marca_filtro==='all'?'active':'' ?>">Todos</a>
      <?php foreach ($marcas as $m): ?>
        <a href="index.php?marca=<?= urlencode($m) ?>#productos" class="filter-btn <?= $marca_filtro===$m?'active':'' ?>"><?= h($m) ?></a>
      <?php endforeach; ?>
    </div>

    <div id="products-grid" class="products-grid">
      <?php if (empty($productos)): ?>
        <div class="loading"><i class="fas fa-box-open"></i> No hay productos para esta marca.</div>
      <?php else: foreach ($productos as $idx => $p):
        $stockClass = $p['stock'] < 10 ? 'low' : '';
        $stockText  = $p['stock'] < 10 ? "¡Quedan {$p['stock']}!" : "Stock: {$p['stock']}";
      ?>
        <article class="product-card" style="animation: fadeInUp 0.5s ease <?= $idx*0.05 ?>s backwards;">
          <div class="product-image">
            <span class="product-badge"><?= h($p['codigo']) ?></span>
            <span class="stock-badge <?= $stockClass ?>"><?= h($stockText) ?></span>
            <i class="<?= h(icono_marca($p['marca'])) ?>"></i>
          </div>
          <div class="product-info">
            <p class="product-marca"><?= h($p['marca']) ?></p>
            <h3 class="product-name"><?= h($p['nombre']) ?></h3>
            <div class="product-meta">
              <span><i class="fas fa-ruler"></i> Talla <?= h((string)$p['talla']) ?></span>
              <span><i class="fas fa-palette"></i> <?= h($p['color']) ?></span>
            </div>
            <div class="product-footer">
              <p class="product-price">$<?= h((string)$p['precio']) ?><small> USD</small></p>
              <form method="post" action="carrito.php" class="add-cart-form" data-ajax="1">
                <input type="hidden" name="action" value="add">
                <input type="hidden" name="codigo" value="<?= h($p['codigo']) ?>">
                <button type="submit" class="add-cart" aria-label="Añadir al carrito" title="Añadir al carrito">
                  <i class="fas fa-cart-plus"></i>
                </button>
              </form>
            </div>
          </div>
        </article>
      <?php endforeach; endif; ?>
    </div>
  </section>

  <!-- CONTACTO -->
  <section id="contacto" class="contact-section">
    <div class="section-header">
      <span class="section-tag">Contáctanos</span>
      <h2>Visítanos o Llámanos</h2>
      <p>Estamos listos para atenderte y ayudarte a encontrar tu calzado perfecto</p>
    </div>

    <div class="contact-grid">
      <article class="contact-card">
        <div class="contact-icon"><i class="fas fa-phone-alt"></i></div>
        <h3>Teléfono</h3>
        <p class="contact-info">+503 2222-3344</p>
        <p class="contact-sub">Lun - Sáb: 8:00 AM - 7:00 PM</p>
      </article>
      <article class="contact-card">
        <div class="contact-icon"><i class="fas fa-map-marker-alt"></i></div>
        <h3>Dirección</h3>
        <p class="contact-info">Av. Independencia #123</p>
        <p class="contact-sub">San Salvador, El Salvador</p>
      </article>
      <article class="contact-card">
        <div class="contact-icon"><i class="fas fa-envelope-open-text"></i></div>
        <h3>Correo</h3>
        <p class="contact-info">contacto@elpasoperfecto.com</p>
        <p class="contact-sub">Respuesta en menos de 24 horas</p>
      </article>
      <article class="contact-card">
        <div class="contact-icon"><i class="fab fa-whatsapp"></i></div>
        <h3>WhatsApp</h3>
        <p class="contact-info">+503 7777-8899</p>
        <p class="contact-sub">Atención inmediata</p>
      </article>
    </div>
  </section>

<?php include __DIR__ . '/includes/footer.php'; ?>
