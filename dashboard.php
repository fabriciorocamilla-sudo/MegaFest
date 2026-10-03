<?php

declare(strict_types=1);
require_once __DIR__ . '/includes/bootstrap.php';
<?php
declare(strict_types=1);
session_start();

// Si el usuario no ha iniciado sesión, redirigir al login limpio
if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}

require_once __DIR__ . '/includes/bootstrap.php';

$title = 'MegaFest · Panel de control';
$items = loadCelebraciones();
$stats = statsCelebraciones($items);
$editarId = trim((string) ($_GET['editar'] ?? ''));
$editando = $editarId !== '' ? findCelebracion($editarId) : null;

usort($items, static function ($a, $b) {
    $dest = ((int) !empty($b['destacado'])) <=> ((int) !empty($a['destacado']));
    if ($dest !== 0) {
        return $dest;
    }
    return strcmp($a['fecha'] ?? '', $b['fecha'] ?? '');
});

require __DIR__ . '/includes/header.php';
?>

<section>
  <span class="kicker">Sala de control</span>
  <h1>Dashboard de celebraciones</h1>
  <p class="lede">Vista centralizada de eventos, deportes, aniversarios y días festivos. Edita, destaca o retira fichas sin salir del panel.</p>
</section>

<section class="stats">
  <article class="stat"><span>Total en cartelera</span><b><?= (int) $stats['total'] ?></b></article>
  <article class="stat"><span>Próximos</span><b><?= (int) $stats['proximos'] ?></b></article>
  <article class="stat"><span>Destacados</span><b><?= (int) $stats['destacados'] ?></b></article>
  <article class="stat"><span>Mix temático</span><b><?= (int) $stats['evento'] ?>/<?= (int) $stats['deporte'] ?>/<?= (int) $stats['aniversario'] ?>/<?= (int) $stats['festivo'] ?></b></article>
</section>

<?php if ($editando): ?>
<section class="form-box">
  <h2>Editar · <?= e($editando['titulo'] ?? '') ?></h2>
  <form id="fest-form" class="form-grid" action="procesar.php" method="post">
    <input type="hidden" name="accion" value="actualizar">
    <input type="hidden" name="id" value="<?= e($editando['id']) ?>">
    <label>
      Tipo
      <select name="tipo" id="tipo" data-locked="1" required>
        <?php foreach (TIPOS as $tipo): ?>
          <option value="<?= e($tipo) ?>" <?= ($editando['tipo'] ?? '') === $tipo ? 'selected' : '' ?>><?= e(tipoLabel($tipo)) ?></option>
        <?php endforeach; ?>
      </select>
    </label>
    <label>
      Título
      <input type="text" name="titulo" maxlength="90" value="<?= e($editando['titulo'] ?? '') ?>" required>
    </label>
    <label>
      Fecha
      <input type="date" name="fecha" value="<?= e($editando['fecha'] ?? '') ?>" required>
    </label>
    <label>
      Hora
      <input type="time" name="hora" value="<?= e($editando['hora'] ?? '') ?>" required>
    </label>
    <label class="full">
      Lugar
      <input type="text" name="lugar" value="<?= e($editando['lugar'] ?? '') ?>" required>
    </label>
    <label class="full">
      Descripción
      <textarea name="descripcion" maxlength="400" required><?= e($editando['descripcion'] ?? '') ?></textarea>
    </label>
    <label>
      Color
      <input type="color" name="color" value="<?= e($editando['color'] ?? '#ff4fd8') ?>">
    </label>
    <label>
      Intensidad
      <select name="intensidad">
        <?php foreach (['baja' => 'Baja', 'media' => 'Media', 'alta' => 'Alta'] as $val => $lab): ?>
          <option value="<?= e($val) ?>" <?= ($editando['intensidad'] ?? '') === $val ? 'selected' : '' ?>><?= e($lab) ?></option>
        <?php endforeach; ?>
      </select>
    </label>
    <label data-show="deporte">
      Disciplina
      <input type="text" name="deporte" value="<?= e((string) ($editando['deporte'] ?? '')) ?>">
    </label>
    <label data-show="deporte">
      Equipos
      <input type="text" name="equipo" value="<?= e((string) ($editando['equipo'] ?? '')) ?>">
    </label>
    <label data-show="aniversario">
      Años
      <input type="number" name="anos" min="1" max="200" value="<?= e((string) ($editando['anos'] ?? '')) ?>">
    </label>
    <label data-show="aniversario">
      Homenaje
      <input type="text" name="homenaje" value="<?= e((string) ($editando['homenaje'] ?? '')) ?>">
    </label>
    <label data-show="festivo">
      Tradición
      <input type="text" name="tradicion" value="<?= e((string) ($editando['tradicion'] ?? '')) ?>">
    </label>
    <label data-show="evento">
      Aforo
      <input type="number" name="aforo" min="0" value="<?= e((string) ($editando['aforo'] ?? '')) ?>">
    </label>
    <label class="check full">
      <input type="checkbox" name="destacado" value="1" <?= !empty($editando['destacado']) ? 'checked' : '' ?>> Destacado
    </label>
    <div class="full actions">
      <button class="btn primary" type="submit">Guardar cambios</button>
      <a class="btn ghost" href="dashboard.php">Cancelar</a>
    </div>
  </form>
</section>
<?php endif; ?>

<section class="panel">
  <div class="section-head" style="padding: 18px 18px 0;">
    <h2>Inventario vivo</h2>
    <div class="theme-row">
      <select id="dash-filter">
        <option value="todos">Todos los tipos</option>
        <option value="evento">Eventos</option>
        <option value="deporte">Deportes</option>
        <option value="aniversario">Aniversarios</option>
        <option value="festivo">Festivos</option>
      </select>
      <input id="dash-search" type="search" placeholder="Buscar título, lugar o tipo...">
    </div>
  </div>
  <div class="table-wrap">
    <table>
      <thead>
        <tr>
          <th>Celebración</th>
          <th>Tipo</th>
          <th>Cuando</th>
          <th>Extra</th>
          <th>Acciones</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($items as $item): ?>
          <?php
            $search = strtolower(($item['titulo'] ?? '') . ' ' . ($item['lugar'] ?? '') . ' ' . ($item['tipo'] ?? ''));
            $extra = match ($item['tipo'] ?? '') {
                'deporte' => trim(($item['deporte'] ?? '') . ' · ' . ($item['equipo'] ?? ''), ' ·'),
                'aniversario' => trim(($item['anos'] ?? '') . ' años · ' . ($item['homenaje'] ?? ''), ' ·'),
                'festivo' => (string) ($item['tradicion'] ?? ''),
                default => ($item['aforo'] ?? '') ? ('Aforo ' . $item['aforo']) : 'Escenario libre',
            };
          ?>
          <tr data-row data-tipo="<?= e($item['tipo'] ?? 'evento') ?>" data-search="<?= e($search) ?>">
            <td>
              <strong><?= e($item['titulo'] ?? '') ?></strong><br>
              <span class="meta"><?= e($item['lugar'] ?? '') ?><?= !empty($item['destacado']) ? ' · ★ destacado' : '' ?></span>
            </td>
            <td><?= e(tipoEmoji($item['tipo'] ?? 'evento') . ' ' . tipoLabel($item['tipo'] ?? 'evento')) ?></td>
            <td><?= e($item['fecha'] ?? '') ?><br><?= e($item['hora'] ?? '') ?></td>
            <td><?= e($extra) ?></td>
            <td class="actions">
              <a class="btn" href="dashboard.php?editar=<?= e($item['id']) ?>">Editar</a>
              <form action="procesar.php" method="post" style="display:inline;">
                <input type="hidden" name="accion" value="destacar">
                <input type="hidden" name="id" value="<?= e($item['id']) ?>">
                <button class="btn ghost" type="submit"><?= !empty($item['destacado']) ? 'Quitar brillo' : 'Destacar' ?></button>
              </form>
              <form action="procesar.php" method="post" style="display:inline;" onsubmit="return confirm('¿Retirar esta celebración del panel?');">
                <input type="hidden" name="accion" value="eliminar">
                <input type="hidden" name="id" value="<?= e($item['id']) ?>">
                <button class="btn danger" type="submit">Eliminar</button>
              </form>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
