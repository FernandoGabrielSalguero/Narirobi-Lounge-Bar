<?php

declare(strict_types=1);
ini_set('display_errors', '1');
error_reporting(E_ALL);

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../models/admin_gift_model.php';

header('Content-Type: application/json; charset=UTF-8');

function json_ok($data = null): void
{
    echo json_encode(['ok' => true, 'data' => $data], JSON_UNESCAPED_UNICODE);
    exit;
}
function json_error(string $message, int $code = 400): void
{
    http_response_code($code);
    echo json_encode(['ok' => false, 'error' => $message], JSON_UNESCAPED_UNICODE);
    exit;
}

try {
    if (!isset($pdo) || !($pdo instanceof \PDO)) {
        throw new \RuntimeException('Conexión PDO no disponible.');
    }
    $model  = new AdminGiftModel($pdo);
    $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

    if ($method === 'GET') {
        // Listar con filtros
        $nombre = isset($_GET['nombre']) ? trim((string)$_GET['nombre']) : null;
        $codigo = isset($_GET['codigo']) ? trim((string)$_GET['codigo']) : null;
        $estado = isset($_GET['estado']) ? trim((string)$_GET['estado']) : null;

        $data = $model->listar($nombre, $codigo, $estado);
        json_ok($data);
    }

    if ($method === 'POST') {
        // Soportar _action=redeem (canje) o crear
        $action = $_POST['_action'] ?? null;

        if ($action === 'redeem') {
            $codigo = isset($_POST['codigo']) ? trim((string)$_POST['codigo']) : '';
            if ($codigo === '' || strlen($codigo) !== 6) {
                json_error('Código inválido.');
            }
            $res = $model->canjearPorCodigo($codigo);
            json_ok(['message' => $res['message'] ?? 'OK', 'codigo' => $res['codigo'], 'estado' => $res['estado']]);
        }

        // Crear nueva gift card
        // Crear nueva gift card
        $nombre = isset($_POST['nombre']) ? trim((string)$_POST['nombre']) : '';
        $fecha  = isset($_POST['fecha_vencimiento']) ? trim((string)$_POST['fecha_vencimiento']) : '';
        $texto  = isset($_POST['texto']) ? trim((string)$_POST['texto']) : '';

        if ($nombre === '' || $fecha === '' || $texto === '') {
            json_error('Campos obligatorios faltantes (nombre, fecha_vencimiento, texto).');
        }

        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $fecha)) {
            json_error('Formato de fecha inválido. Use YYYY-MM-DD.');
        }

        $nueva = $model->crear($nombre, $fecha, $texto);
        json_ok($nueva);
    }

    // Método no permitido
    http_response_code(405);
    json_error('Método no permitido', 405);
} catch (\Throwable $e) {
    json_error('Error: ' . $e->getMessage(), 500);
}
