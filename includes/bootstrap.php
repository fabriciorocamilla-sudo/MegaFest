<?php
declare(strict_types=1);

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

date_default_timezone_set('America/Mexico_City');
require_once __DIR__ . '/data.php';

function e(?string $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function flash(?string $message = null, string $type = 'ok'): ?array
{
    if ($message !== null) {
        $_SESSION['flash'] = ['message' => $message, 'type' => $type];
        return null;
    }
    $flash = $_SESSION['flash'] ?? null;
    unset($_SESSION['flash']);
    return is_array($flash) ? $flash : null;
}

function tipoLabel(string $tipo): string
{
    return match ($tipo) {
        'deporte' => 'Deporte',
        'aniversario' => 'Aniversario',
        'festivo' => 'Día festivo',
        default => 'Evento',
    };
}

function tipoEmoji(string $tipo): string
{
    return match ($tipo) {
        'deporte' => '⚡',
        'aniversario' => '🥂',
        'festivo' => '🎆',
        default => '🎶',
    };
}
