<?php
declare(strict_types=1);

ini_set('display_errors', '1');
error_reporting(E_ALL);

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../models/admin_reservas_model.php';

header('Content-Type: application/json; charset=UTF-8');

function jsonOk($data = null): void {
    echo json_encode(['ok' => true, 'data' => $data], JSON_UNESCAPED_UNICODE);
    exit;
}
function jsonErr(string $msg, int $code = 400): void {
    http_response_code($code);
    echo json_encode(['ok' => false, 'error' => $msg], JSON_UNESCAPED_UNICODE);
    exit;
}

/** Sanitiza string simple */
function s(?string $v): string {
    return trim((string)$v);
}

try {
    if (!isset($pdo) || !$pdo instanceof PDO) {
        throw new RuntimeException('Conexión PDO no disponible');
    }

    $model = new AdminReservasModel($pdo);
    $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

    if ($method === 'GET') {
        // GET por id (detalles) o listado
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        if ($id > 0) {
            $row = $model->obtener($id);
            if (!$row) jsonErr('Reserva no encontrada', 404);
            jsonOk($row);
        } else {
            $rows = $model->listar();
            jsonOk($rows);
        }
    }

    if ($method === 'POST') {
        $override = s($_POST['_method'] ?? '');
        if ($override === 'delete') {
            $id = (int)($_POST['id'] ?? 0);
            if ($id <= 0) jsonErr('ID inválido');
            $ok = $model->eliminar($id);
            if (!$ok) jsonErr('No se pudo eliminar');
            jsonOk(['id' => $id]);
        } elseif ($override === 'put') {
            // Update
            $id = (int)($_POST['id'] ?? 0);
            if ($id <= 0) jsonErr('ID inválido');

            $data = [
                'nombre'   => s($_POST['nombre'] ?? ''),
                'telefono' => s($_POST['telefono'] ?? ''),
                'fecha'    => s($_POST['fecha'] ?? ''),
                'hora'     => s($_POST['hora'] ?? ''),
                'personas' => (int)($_POST['personas'] ?? 0),
                'estado'   => s($_POST['estado'] ?? 'pendiente'),
                'notas'    => s($_POST['notas'] ?? ''),
            ];

            if ($data['nombre'] === '' || $data['telefono'] === '' || $data['fecha'] === '' || $data['hora'] === '' || $data['personas'] < 1) {
                jsonErr('Campos obligatorios faltantes');
            }
            if (!in_array($data['estado'], ['pendiente','confirmada','finalizada','cancelada'], true)) {
                jsonErr('Estado inválido');
            }

            $ok = $model->actualizar($id, $data);
            if (!$ok) jsonErr('No se pudo actualizar');
            $row = $model->obtener($id);
            jsonOk($row);
        } else {
            // Create
            $data = [
                'nombre'   => s($_POST['nombre'] ?? ''),
                'telefono' => s($_POST['telefono'] ?? ''),
                'fecha'    => s($_POST['fecha'] ?? ''),
                'hora'     => s($_POST['hora'] ?? ''),
                'personas' => (int)($_POST['personas'] ?? 0),
                'estado'   => s($_POST['estado'] ?? 'pendiente'),
                'notas'    => s($_POST['notas'] ?? ''),
            ];

            if ($data['nombre'] === '' || $data['telefono'] === '' || $data['fecha'] === '' || $data['hora'] === '' || $data['personas'] < 1) {
                jsonErr('Campos obligatorios faltantes');
            }
            if (!in_array($data['estado'], ['pendiente','confirmada','finalizada','cancelada'], true)) {
                jsonErr('Estado inválido');
            }

            $id = $model->crear($data);
            $row = $model->obtener($id);
            jsonOk($row);
        }
    }

    // Método no soportado
    jsonErr('Método no permitido', 405);

} catch (Throwable $e) {
    jsonErr('Error: ' . $e->getMessage(), 500);
}
