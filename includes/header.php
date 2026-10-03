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
       <img src="/MEGAFEST.png" alt="MegaFest" style="width: 180px; height: auto; vertical-align: middle;">
      </a>
      <div class="nav-links">
        <a href="index.php">Portal</a>
        <a href="index.php#crear">Crear celebración</a>
        <a href="dashboard.php">Panel de control</a>
        <a href="login.php" class="btn-login" style="margin-left: 15px; color: #ff4fd8; border: 1px solid rgba(255, 79, 216, 0.4); padding: 6px 14px; border-radius: 8px;">Acceso / Registro</a>
      </div>
    </nav>
    <?php if ($flash): ?>
      <div class="flash <?= e($flash['type']) ?>"><?= e($flash['message']) ?></div>
    <?php endif; ?>
