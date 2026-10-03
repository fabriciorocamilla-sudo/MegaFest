<?php
declare(strict_types=1);
require_once __DIR__ . '/includes/bootstrap.php';

$title = 'MegaFest · Portal de celebraciones';
$items = loadCelebraciones();
usort($items, static fn($a, $b) => strcmp($a['fecha'] ?? '', $b['fecha'] ?? ''));
require __DIR__ . '/includes/header.php';
?>

<section class="hero">
  <div>
    <span class="kicker">Escena viva · 2026</span>
    <h1>El calendario más colorido de tu ciudad</h1>
    <p class="lede">
      MegaFest concentra torneos, aniversarios, noches de concierto y días festivos
      en un mismo escenario. Cambia la temática, arma tu celebración y mírala
      saltar al panel de control.
    </p>
    <div class="theme-row" role="tablist" aria-label="Temáticas">
      <button type="button" class="chip" data-theme-set="evento">🎶 Eventos</button>
      <button type="button" class="chip" data-theme-set="deporte">⚡ Deportes</button>
      <button type="button" class="chip" data-theme-set="aniversario">🥂 Aniversarios</button>
      <button type="button" class="chip" data-theme-set="festivo">🎆 Días festivos</button>
    </div>
    <a class="btn primary" href="#crear">Lanzar una celebración</a>
  </div>
  <aside class="hero-card" id="live-preview">
    <span class="badge">Celebración</span>
    <h2 class="live-title">Tu celebración en vivo</h2>
    <p class="live-desc">Completa el formulario y verás aquí cómo se siente la temática.</p>
    <p class="meta live-meta">Fecha · hora · lugar</p>
  </aside>
</section>

<div class="section-head">
  <div>
    <h2>Cartelera central</h2>
    <p class="lede">Filtra por universo temático. Todo se pinta al instante.</p>
  </div>
  <div class="theme-row">
    <button type="button" class="chip active" data-filter="todos">Todos</button>
    <button type="button" class="chip" data-filter="evento">Eventos</button>
    <button type="button" class="chip" data-filter="deporte">Deportes</button>
    <button type="button" class="chip" data-filter="aniversario">Aniversarios</button>
    <button type="button" class="chip" data-filter="festivo">Festivos</button>
  </div>
</div>

<section class="grid cards">
  <?php foreach ($items as $item): ?>
    <article class="card" data-card-tipo="<?= e($item['tipo'] ?? 'evento') ?>" style="--accent: <?= e($item['color'] ?? '#ff4fd8') ?>">
      <span class="badge"><?= e(tipoEmoji($item['tipo'] ?? 'evento') . ' ' . tipoLabel($item['tipo'] ?? 'evento')) ?></span>
      <h3><?= e($item['titulo'] ?? 'Sin título') ?></h3>
      <p><?= e($item['descripcion'] ?? '') ?></p>
      <p class="meta">
        <span><?= e($item['fecha'] ?? '') ?> <?= e($item['hora'] ?? '') ?></span>
        <span><?= e($item['lugar'] ?? '') ?></span>
        <?php if (!empty($item['costo_total'])): ?>
          <span style="color: var(--accent-3);">Presupuesto: $<?= number_format((float)$item['costo_total'], 2) ?></span>
        <?php endif; ?>
      </p>
    </article>
  <?php endforeach; ?>
</section>

<section class="form-box" id="crear">
  <h2>Formulario dinámico de celebraciones</h2>
  <p class="lede">Elige el tipo y el portal revela campos propios de esa temática.</p>
  <form id="fest-form" class="form-grid" action="procesar.php" method="post">
    <input type="hidden" name="accion" value="crear">
    <label>
      Tipo de celebración
      <select name="tipo" id="tipo" required>
        <option value="evento">Evento</option>
        <option value="deporte">Deporte</option>
        <option value="aniversario">Aniversario</option>
        <option value="festivo">Día festivo</option>
      </select>
    </label>
    <label>
      Título
      <input type="text" name="titulo" maxlength="90" placeholder="Nombre que encienda la noche" required>
    </label>
    <label>
      Fecha
      <input type="date" name="fecha" required>
    </label>
    <label>
      Hora
      <input type="time" name="hora" required>
    </label>
    <label class="full">
      Lugar
      <input type="text" name="lugar" maxlength="120" placeholder="Estadio, rooftop, plaza..." required>
    </label>
    <label class="full">
      Descripción
      <textarea name="descripcion" maxlength="400" placeholder="¿Qué va a pasar y por qué nadie se lo puede perder?" required></textarea>
    </label>
    <label>
      Color de acento
      <input type="color" name="color" value="#ff4fd8">
    </label>
    <label>
      Intensidad
      <select name="intensidad">
        <option value="baja">Baja · íntimo</option>
        <option value="media" selected>Media · vibrante</option>
        <option value="alta">Alta · explosivo</option>
      </select>
    </label>
    
    <!-- Nuevos campos para cotización -->
    <label>
      Costo base estimado ($)
      <input type="number" name="precio_base" min="0" step="1" placeholder="Ej. 150">
    </label>
    <label>
      Horas estimadas de servicio
      <input type="number" name="horas" min="1" max="24" placeholder="Ej. 4">
    </label>

    <label data-show="deporte">
      Disciplina
      <input type="text" name="deporte" placeholder="Fútbol 7, 3x3, atletismo...">
    </label>
    <label data-show="deporte">
      Equipos o atletas
      <input type="text" name="equipo" placeholder="Rayos Magenta vs Titanes Cian">
    </label>
    <label data-show="aniversario">
      Años
      <input type="number" name="anos" min="1" max="200" placeholder="10">
    </label>
    <label data-show="aniversario">
      Homenaje
      <input type="text" name="homenaje" placeholder="A quién se celebra">
    </label>
    <label data-show="festivo">
      Tradición
      <input type="text" name="tradicion" placeholder="Ofrenda, posada, desfile...">
    </label>
    <label data-show="evento">
      Aforo estimado
      <input type="number" name="aforo" min="10" max="100000" placeholder="800">
    </label>
    <label class="check full">
      <input type="checkbox" name="destacado" value="1"> Marcar como destacado en el panel
    </label>
    <div class="full">
      <button class="btn primary" type="submit">Publicar en MegaFest</button>
    </div>
  </form>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
