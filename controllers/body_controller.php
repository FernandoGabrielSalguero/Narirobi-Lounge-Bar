<?php declare(strict_types=1);
ini_set('display_errors', '1'); // puedes desactivar luego
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../models/body_model.php';

$action = $_GET['action'] ?? 'ping';

try {
    switch ($action) {
        case 'ping':
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(['ok' => true, 'data' => ['pong' => true]], JSON_UNESCAPED_UNICODE);
            exit;

        default:
            http_response_code(400);
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(['ok' => false, 'error' => 'Acción no soportada'], JSON_UNESCAPED_UNICODE);
            exit;
    }
} catch (Throwable $e) {
    http_response_code(500);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['ok' => false, 'error' => 'Error interno: ' . $e->getMessage()], JSON_UNESCAPED_UNICODE);
    exit;
}
