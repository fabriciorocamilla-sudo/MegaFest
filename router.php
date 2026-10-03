<?php
declare(strict_types=1);

$uri = urldecode(parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/');
$path = __DIR__ . $uri;

if ($uri !== '/' && is_file($path)) {
    return false;
}

$routes = [
    '/' => 'index.php',
    '/index.php' => 'index.php',
    '/procesar.php' => 'procesar.php',
    '/dashboard.php' => 'dashboard.php',
];

if (isset($routes[$uri])) {
    require __DIR__ . '/' . $routes[$uri];
    return true;
}

http_response_code(404);
header('Content-Type: text/html; charset=UTF-8');
echo '<!DOCTYPE html><html lang="es"><head><meta charset="UTF-8"><title>MegaFest · 404</title></head><body style="font-family:sans-serif;background:#0b0618;color:#fff;display:grid;place-items:center;height:100vh;margin:0"><div><h1>Pista no encontrada</h1><p>Esa ruta no existe en MegaFest.</p><a href="/" style="color:#ff4fd8">Volver al portal</a></div></body></html>';
