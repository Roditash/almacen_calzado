<?php
require_once __DIR__ . '/helpers.php';
$cart_count = carrito_total_items();
$current = basename($_SERVER['SCRIPT_NAME']);
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= isset($page_title) ? h($page_title) . ' — ' : '' ?>El Paso Perfecto</title>
  <link rel="stylesheet" href="styles.css">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&family=Caveat:wght@400;700&family=JetBrains+Mono:wght@400;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.4.0/css/all.min.css">
</head>
<body>

  <!-- HEADER / NAV -->
  <header class="main-header">
    <div class="header-container">
      <a href="index.php" class="logo" style="text-decoration:none;color:inherit;">
        <i class="fas fa-shoe-prints"></i>
        <div class="logo-text">
          <h1>El Paso Perfecto</h1>
          <span>Almacén de Calzado</span>
        </div>
      </a>
      <nav class="main-nav" id="main-nav">
        <ul>
          <li><a href="index.php" class="nav-link <?= $current==='index.php'?'active':'' ?>"><i class="fas fa-home"></i> Inicio</a></li>
          <li><a href="index.php#productos" class="nav-link"><i class="fas fa-shoe-prints"></i> Productos</a></li>
          <li><a href="db.php" class="nav-link <?= $current==='db.php'?'active':'' ?>"><i class="fas fa-database"></i> DB</a></li>
          <li><a href="index.php#contacto" class="nav-link"><i class="fas fa-envelope"></i> Contacto</a></li>
          <li><a href="carrito.php" class="nav-link nav-cart <?= $current==='carrito.php'?'active':'' ?>">
            <i class="fas fa-shopping-cart"></i> Carrito
            <span class="cart-count" id="cart-count"><?= $cart_count ?></span>
          </a></li>
        </ul>
      </nav>
      <button class="menu-toggle" id="menu-toggle" aria-label="Abrir menú">
        <i class="fas fa-bars"></i>
      </button>
    </div>
  </header>
