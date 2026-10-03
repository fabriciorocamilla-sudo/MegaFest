<?php
declare(strict_types=1);

const DATA_FILE = __DIR__ . '/../data/celebraciones.json';
const TIPOS = ['evento', 'deporte', 'aniversario', 'festivo'];

function dataDir(): string
{
    $dir = dirname(DATA_FILE);
    if (!is_dir($dir)) {
        mkdir($dir, 0775, true);
    }
    return $dir;
}

function loadCelebraciones(): array
{
    dataDir();
    if (!is_file(DATA_FILE)) {
        $seed = seedCelebraciones();
        saveCelebraciones($seed);
        return $seed;
    }

    $raw = file_get_contents(DATA_FILE);
    $decoded = json_decode($raw ?: '[]', true);
    return is_array($decoded) ? $decoded : [];
}

function saveCelebraciones(array $items): bool
{
    dataDir();
    $json = json_encode(array_values($items), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    return file_put_contents(DATA_FILE, $json, LOCK_EX) !== false;
}

function findCelebracion(string $id): ?array
{
    foreach (loadCelebraciones() as $item) {
        if (($item['id'] ?? '') === $id) {
            return $item;
        }
    }
    return null;
}

function upsertCelebracion(array $item): array
{
    $items = loadCelebraciones();
    $found = false;
    foreach ($items as $i => $row) {
        if (($row['id'] ?? '') === $item['id']) {
            $items[$i] = $item;
            $found = true;
            break;
        }
    }
    if (!$found) {
        array_unshift($items, $item);
    }
    saveCelebraciones($items);
    return $item;
}

function deleteCelebracion(string $id): bool
{
    $items = loadCelebraciones();
    $filtered = array_values(array_filter($items, static fn($row) => ($row['id'] ?? '') !== $id));
    if (count($filtered) === count($items)) {
        return false;
    }
    return saveCelebraciones($filtered);
}

function statsCelebraciones(array $items): array
{
    $stats = [
        'total' => count($items),
        'evento' => 0,
        'deporte' => 0,
        'aniversario' => 0,
        'festivo' => 0,
        'destacados' => 0,
        'proximos' => 0,
    ];
    $hoy = date('Y-m-d');
    foreach ($items as $item) {
        $tipo = $item['tipo'] ?? '';
        if (isset($stats[$tipo])) {
            $stats[$tipo]++;
        }
        if (!empty($item['destacado'])) {
            $stats['destacados']++;
        }
        if (($item['fecha'] ?? '') >= $hoy) {
            $stats['proximos']++;
        }
    }
    return $stats;
}

function seedCelebraciones(): array
{
    return [
        [
            'id' => 'mf-001',
            'titulo' => 'Copa MegaFest Urbana',
            'tipo' => 'deporte',
            'fecha' => '2026-10-18',
            'hora' => '18:00',
            'lugar' => 'Estadio Neón, CDMX',
            'descripcion' => 'Torneo relámpago de fútbol 7 con DJ en vivo, luces LED y zona de food trucks.',
            'color' => '#22c55e',
            'intensidad' => 'alta',
            'equipo' => 'Rayos Magenta vs Titanes Cian',
            'deporte' => 'Fútbol 7',
            'destacado' => true,
            'creado' => '2026-09-20 10:00:00',
        ],
        [
            'id' => 'mf-002',
            'titulo' => 'Aniversario 10 · Estudio Prisma',
            'tipo' => 'aniversario',
            'fecha' => '2026-11-02',
            'hora' => '20:30',
            'lugar' => 'Rooftop Aurora',
            'descripcion' => 'Una década de diseño, música y comunidad. Brindis, recuerdos y show de mapping.',
            'color' => '#f59e0b',
            'intensidad' => 'media',
            'equipo' => '',
            'deporte' => '',
            'anos' => 10,
            'homenaje' => 'Equipo fundador Prisma',
            'destacado' => true,
            'creado' => '2026-09-21 12:40:00',
        ],
        [
            'id' => 'mf-003',
            'titulo' => 'Día de Muertos Glow Parade',
            'tipo' => 'festivo',
            'fecha' => '2026-11-01',
            'hora' => '19:00',
            'lugar' => 'Paseo de las Luces',
            'descripcion' => 'Desfile de ofrendas contemporáneas, catrinas fluorescentes y altares interactivos.',
            'color' => '#f97316',
            'intensidad' => 'alta',
            'tradicion' => 'Ofrenda colectiva',
            'destacado' => true,
            'creado' => '2026-09-22 09:15:00',
        ],
        [
            'id' => 'mf-004',
            'titulo' => 'Noche Indie del Solsticio',
            'tipo' => 'evento',
            'fecha' => '2026-12-21',
            'hora' => '21:00',
            'lugar' => 'Hangar Cosmos',
            'descripcion' => 'Festival de bandas emergentes, arte digital y pista de baile infinita.',
            'color' => '#a855f7',
            'intensidad' => 'alta',
            'aforo' => 1200,
            'destacado' => false,
            'creado' => '2026-09-25 16:05:00',
        ],
        [
            'id' => 'mf-005',
            'titulo' => 'Open de Básquet Callejero',
            'tipo' => 'deporte',
            'fecha' => '2026-10-25',
            'hora' => '16:30',
            'lugar' => 'Cancha Volcán',
            'descripcion' => '3x3 con tableros LED, replay instantáneo y premiación holográfica.',
            'color' => '#06b6d4',
            'intensidad' => 'alta',
            'equipo' => 'Street Kings',
            'deporte' => 'Básquet 3x3',
            'destacado' => false,
            'creado' => '2026-09-26 11:20:00',
        ],
        [
            'id' => 'mf-006',
            'titulo' => 'Navidad Pixelada',
            'tipo' => 'festivo',
            'fecha' => '2026-12-24',
            'hora' => '17:00',
            'lugar' => 'Plaza Central MegaFest',
            'descripcion' => 'Mercadillo iluminado, coro sintético y árbol interactivo de 12 metros.',
            'color' => '#ef4444',
            'intensidad' => 'media',
            'tradicion' => 'Intercambio secreto',
            'destacado' => false,
            'creado' => '2026-09-28 08:00:00',
        ],
    ];
}
