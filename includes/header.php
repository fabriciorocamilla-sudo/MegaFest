<?php
declare(strict_types=1);
require_once __DIR__ . '/bootstrap.php';

$page = $page ?? 'inicio';
$title = $title ?? 'MegaFest · Celebraciones a todo color';
$flash = flash();
$theme = $_GET['tema'] ?? 'evento';
if (!in_array($theme, TIPOS, true)) {
    $theme = 'evento';
}
?>
<!DOCTYPE html>
<html lang="es" data-theme="<?= e($theme) ?>">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= e($title) ?></title>
  <link rel="stylesheet" href="styles.css">
</head>
<body>
  <div class="orb a"></div>
  <div class="orb b"></div>
  <div class="orb c"></div>
  
  <!-- Barra de navegación completa -->
  <div style="width: 100%; background: transparent; position: relative; z-index: 100;">
    <nav class="top" style="display: flex; align-items: center; justify-content: space-between; width: 100% !important; max-width: 100% !important; margin: 0 !important; padding: 15px 40px !important; box-sizing: border-box;">
        
      <!-- Contenedor del logo con el margen exacto para ubicarlo sobre "ESCENA VIVA" -->
      <div style="margin-left: 550px !important;">
        <a class="brand" href="index.php" style="display: flex; align-items: center; text-decoration: none;">
            <img src="MEGAFEST.png" alt="MegaFest" style="height: 60px; width: auto; display: block;">
        </a>
      </div>

      <!-- Enlaces y usuario alineados a la derecha -->
      <div class="nav-links" style="display: flex; align-items: center; gap: 15px; flex-wrap: nowrap; margin-left: auto;">
        <a href="index.php">Portal</a>
        <a href="index.php#crear">Crear celebración</a>
        <a href="dashboard.php">Panel de control</a>

        <?php if (isset($_SESSION['user'])): ?>
          <!-- Menú desplegable de cuenta -->
          <div class="user-dropdown">
            <button class="user-btn">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align: middle; margin-right: 6px;"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
              <?= htmlspecialchars($_SESSION['nombre'] ?? 'Mi Cuenta', ENT_QUOTES, 'UTF-8') ?>
            </button>
            <div class="dropdown-content">
              <div class="dropdown-header">
                <strong><?= htmlspecialchars($_SESSION['nombre'] ?? '', ENT_QUOTES, 'UTF-8') ?></strong>
                <small><?= htmlspecialchars($_SESSION['user'] ?? '', ENT_QUOTES, 'UTF-8') ?></small>
                <span class="badge-rol"><?= strtoupper(htmlspecialchars($_SESSION['rol'] ?? 'cliente', ENT_QUOTES, 'UTF-8')) ?></span>
              </div>
              <a href="dashboard.php">Sala de control</a>
              <a href="logout.php" class="logout-link">Cerrar sesión</a>
            </div>
          </div>
        <?php else: ?>
          <a href="login.php" class="btn primary" style="padding: 10px 18px;">Acceso / Registro</a>
        <?php endif; ?>

      </div>
    </nav>
  </div>

  <!-- Contenido principal -->
  <div class="wrap">
