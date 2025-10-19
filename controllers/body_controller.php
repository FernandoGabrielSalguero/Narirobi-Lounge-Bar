<?php

declare(strict_types=1);

ini_set('display_errors', '1'); // desactivar en prod
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../models/body_model.php';

$action = $_GET['action'] ?? 'ping';

try {
    header('Content-Type: application/json; charset=utf-8');

    switch ($action) {
        case 'ping':
            echo json_encode(['ok' => true, 'data' => ['pong' => true]], JSON_UNESCAPED_UNICODE);
            exit;

        case 'carta_data':
            if (!isset($pdo) || !($pdo instanceof PDO)) {
                throw new RuntimeException('Conexión PDO no disponible en config.php ($pdo).');
            }

            $model = new BodyModel($pdo);

            $colors  = $model->getColors();
            $images  = $model->getImages();
            $grouped = $model->getGroupedProducts();

            echo json_encode([
                'ok'   => true,
                'data' => [
                    'colors'   => $colors,
                    'images'   => $images,
                    'products' => $grouped,
                ],
            ], JSON_UNESCAPED_UNICODE);
            exit;

        default:
            http_response_code(400);
            echo json_encode(['ok' => false, 'error' => 'Acción no soportada']);
            exit;
    }
} catch (Throwable $e) {
    http_response_code(500);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode([
        'ok' => false,
        'error' => 'Error interno: ' . $e->getMessage(),
    ], JSON_UNESCAPED_UNICODE);
    exit;
}
