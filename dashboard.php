<?php
declare(strict_types=1);
require_once __DIR__ . '/includes/bootstrap.php';

// Asegurar que solo se procese mediante peticiones POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: dashboard.php');
    exit;
}

$accion = $_POST['accion'] ?? '';
$id = trim((string) ($_POST['id'] ?? ''));

// Cargar los elementos actuales desde el almacenamiento
$items = loadCelebraciones();

if ($accion === 'eliminar' && $id !== '') {
    // Filtrar el arreglo para excluir el elemento que coincide con el ID a eliminar
    $items = array_values(array_filter($items, static function ($item) use ($id) {
        return ($item['id'] ?? '') !== $id;
    }));

    // Guardar los cambios de forma persistente
    guardarCelebraciones($items);

} elseif ($accion === 'destacar' && $id !== '') {
    // Cambiar el estado de destacado
    foreach ($items as &$item) {
        if (($item['id'] ?? '') === $id) {
            $item['destacado'] = empty($item['destacado']);
            break;
        }
    }
    unset($item);
    guardarCelebraciones($items);

} elseif ($accion === 'actualizar' && $id !== '') {
    // Actualizar los datos del elemento editado
    foreach ($items as &$item) {
        if (($item['id'] ?? '') === $id) {
            $item['tipo'] = $_POST['tipo'] ?? $item['tipo'];
            $item['titulo'] = trim($_POST['titulo'] ?? $item['titulo']);
            $item['fecha'] = $_POST['fecha'] ?? $item['fecha'];
            $item['hora'] = $_POST['hora'] ?? $item['hora'];
            $item['lugar'] = trim($_POST['lugar'] ?? $item['lugar']);
            $item['descripcion'] = trim($_POST['descripcion'] ?? $item['descripcion']);
            $item['color'] = $_POST['color'] ?? $item['color'];
            $item['intensidad'] = $_POST['intensidad'] ?? $item['intensidad'];
            $item['destacado'] = isset($_POST['destacado']) ? 1 : 0;
            
            // Campos específicos según el tipo de celebración
            $item['deporte'] = trim($_POST['deporte'] ?? '');
            $item['equipo'] = trim($_POST['equipo'] ?? '');
            $item['anos'] = $_POST['anos'] ?? '';
            $item['homenaje'] = trim($_POST['homenaje'] ?? '');
            $item['tradicion'] = trim($_POST['tradicion'] ?? '');
            $item['aforo'] = $_POST['aforo'] ?? '';
            break;
        }
    }
    unset($item);
    guardarCelebraciones($items);
}

// Redireccionar de vuelta al panel de control limpio
header('Location: dashboard.php');
exit;
