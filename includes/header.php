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
  <div class="wrap">
    <nav class="top">
      <a class="brand" href="index.php">
        <img src="MEGAFEST.png" alt="MegaFest" style="height: 38px; width: auto; vertical-align: middle;">
      </a>
      <div class="nav-links" style="display: flex; align-items: center; flex-wrap: nowrap;">
        <a href="index.php">Portal</a>
        <a href="index.php#crear">Crear celebración</a>
        <a href="dashboard.php">Panel de control</a>

        <?php if (isset($_SESSION['user'])): ?>
          <!-- Contenedor del menú desplegable de la cuenta -->
          <div class="user-dropdown">
            <button class="user-btn">
              <!-- Ícono SVG de usuario -->
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align: middle; margin-right: 6px;"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
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
          <a href="login.php" class="btn-login" style="margin-left: 15px; color: #ff4fd8; border: 1px solid rgba(255, 79, 216, 0.4); padding: 6px 14px; border-radius: 8px;">Acceso / Registro</a>
        <?php endif; ?>
      </div>
    </nav>
