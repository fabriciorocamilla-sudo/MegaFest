<?php
declare(strict_types=1);
require_once __DIR__ . '/includes/bootstrap.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

$accion = $_POST['accion'] ?? 'crear';

if ($accion === 'eliminar') {
    $id = trim((string) ($_POST['id'] ?? ''));
    if ($id !== '' && deleteCelebracion($id)) {
        flash('Celebración retirada del panel.');
    } else {
        flash('No se pudo eliminar esa celebración.', 'err');
    }
    header('Location: dashboard.php');
    exit;
}

if ($accion === 'destacar') {
    $id = trim((string) ($_POST['id'] ?? ''));
    $item = $id !== '' ? findCelebracion($id) : null;
    if ($item) {
        $item['destacado'] = empty($item['destacado']);
        upsertCelebracion($item);
        flash($item['destacado'] ? 'Ahora brilla en destacados.' : 'Se quitó de destacados.');
    } else {
        flash('No encontramos esa ficha.', 'err');
    }
    header('Location: dashboard.php');
    exit;
}

$tipo = $_POST['tipo'] ?? 'evento';
if (!in_array($tipo, TIPOS, true)) {
    $tipo = 'evento';
}

$titulo = trim((string) ($_POST['titulo'] ?? ''));
$fecha = trim((string) ($_POST['fecha'] ?? ''));
$hora = trim((string) ($_POST['hora'] ?? ''));
$lugar = trim((string) ($_POST['lugar'] ?? ''));
$descripcion = trim((string) ($_POST['descripcion'] ?? ''));
$color = trim((string) ($_POST['color'] ?? '#ff4fd8'));
$intensidad = trim((string) ($_POST['intensidad'] ?? 'media'));

$errores = [];
if ($titulo === '' || strlen($titulo) > 90) {
    $errores[] = 'El título es obligatorio (máx. 90).';
}
if ($fecha === '' || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $fecha)) {
    $errores[] = 'La fecha no es válida.';
}
if ($hora === '' || !preg_match('/^\d{2}:\d{2}$/', $hora)) {
    $errores[] = 'La hora no es válida.';
}
if ($lugar === '') {
    $errores[] = 'Indica un lugar.';
}
if ($descripcion === '' || strlen($descripcion) > 400) {
    $errores[] = 'La descripción es obligatoria (máx. 400).';
}
if (!preg_match('/^#[0-9A-Fa-f]{6}$/', $color)) {
    $color = '#ff4fd8';
}
if (!in_array($intensidad, ['baja', 'media', 'alta'], true)) {
    $intensidad = 'media';
}

if ($errores) {
    flash(implode(' ', $errores), 'err');
    $back = ($accion === 'actualizar') ? 'dashboard.php' : 'index.php#crear';
    header('Location: ' . $back);
    exit;
}

$id = trim((string) ($_POST['id'] ?? ''));
$existente = ($accion === 'actualizar' && $id !== '') ? findCelebracion($id) : null;

$item = [
    'id' => $existente['id'] ?? ('mf-' . bin2hex(random_bytes(4))),
    'titulo' => $titulo,
    'tipo' => $tipo,
    'fecha' => $fecha,
    'hora' => $hora,
    'lugar' => $lugar,
    'descripcion' => $descripcion,
    'color' => $color,
    'intensidad' => $intensidad,
    'destacado' => isset($_POST['destacado']),
    'creado' => $existente['creado'] ?? date('Y-m-d H:i:s'),
    'actualizado' => date('Y-m-d H:i:s'),
];

if ($tipo === 'deporte') {
    $item['deporte'] = trim((string) ($_POST['deporte'] ?? ''));
    $item['equipo'] = trim((string) ($_POST['equipo'] ?? ''));
}
if ($tipo === 'aniversario') {
    $item['anos'] = max(1, (int) ($_POST['anos'] ?? 1));
    $item['homenaje'] = trim((string) ($_POST['homenaje'] ?? ''));
}
if ($tipo === 'festivo') {
    $item['tradicion'] = trim((string) ($_POST['tradicion'] ?? ''));
}
if ($tipo === 'evento') {
    $item['aforo'] = max(0, (int) ($_POST['aforo'] ?? 0));
}

upsertCelebracion($item);

if ($accion === 'actualizar') {
    flash('La ficha se actualizó en el panel de control.');
    header('Location: dashboard.php');
    exit;
}

flash('¡Celebración publicada! Ya aparece en la cartelera y en el dashboard.');
header('Location: dashboard.php');
exit;
