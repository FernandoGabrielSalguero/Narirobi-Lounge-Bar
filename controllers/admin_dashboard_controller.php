<?php
declare(strict_types=1);

ini_set('display_errors', '1');
error_reporting(E_ALL);

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../models/admin_dashboard_model.php';

header('Content-Type: application/json; charset=utf-8');

$model = new AdminDashboardModel($pdo);
$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

/**
 * Validadores de color (#hex 3/6) o rgb(r,g,b)
 */
function is_valid_color(string $v): bool {
    $v = trim($v);
    if (preg_match('/^#(?:[0-9a-fA-F]{3}|[0-9a-fA-F]{6})$/', $v)) {
        return true;
    }
    if (preg_match('/^rgb\(\s*(?:[01]?\d?\d|2[0-4]\d|25[0-5])\s*,\s*(?:[01]?\d?\d|2[0-4]\d|25[0-5])\s*,\s*(?:[01]?\d?\d|2[0-4]\d|25[0-5])\s*\)$/', $v)) {
        return true;
    }
    return false;
}

try {
    if ($method === 'GET') {
        $data = $model->getColors();
        echo json_encode(['ok' => true, 'data' => $data], JSON_UNESCAPED_UNICODE);
        exit;
    }

    if ($method === 'POST') {
        // Soporta application/json y form-urlencoded
        $raw = file_get_contents('php://input') ?: '';
        $payload = [];
        if (!empty($_POST)) {
            $payload = $_POST;
        } elseif ($raw) {
            $decoded = json_decode($raw, true);
            if (is_array($decoded)) { $payload = $decoded; }
        }

        $texto  = isset($payload['color_texto']) ? trim((string)$payload['color_texto']) : '';
        $fondo  = isset($payload['color_fondo']) ? trim((string)$payload['color_fondo']) : '';
        $acento = isset($payload['color_acento']) ? trim((string)$payload['color_acento']) : '';

        if ($texto === '' || $fondo === '' || $acento === '') {
            echo json_encode(['ok' => false, 'error' => 'Todos los campos son obligatorios.'], JSON_UNESCAPED_UNICODE);
            exit;
        }
        if (!is_valid_color($texto) || !is_valid_color($fondo) || !is_valid_color($acento)) {
            echo json_encode(['ok' => false, 'error' => 'Formato de color inválido. Usá #hex (3/6) o rgb(r,g,b).'], JSON_UNESCAPED_UNICODE);
            exit;
        }

        $saved = $model->updateColors($texto, $fondo, $acento);
        if ($saved) {
            echo json_encode(['ok' => true, 'data' => ['color_texto'=>$texto,'color_fondo'=>$fondo,'color_acento'=>$acento]], JSON_UNESCAPED_UNICODE);
        } else {
            echo json_encode(['ok' => false, 'error' => 'No se pudieron actualizar los colores.'], JSON_UNESCAPED_UNICODE);
        }
        exit;
    }

    // Método no soportado
    http_response_code(405);
    echo json_encode(['ok' => false, 'error' => 'Método no permitido.'], JSON_UNESCAPED_UNICODE);
} catch (\Throwable $e) {
    http_response_code(500);
    echo json_encode(['ok' => false, 'error' => 'Error del servidor: '.$e->getMessage()], JSON_UNESCAPED_UNICODE);
}
