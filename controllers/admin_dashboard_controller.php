<?php

declare(strict_types=1);

ini_set('display_errors', '1');
error_reporting(E_ALL);

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../models/admin_dashboard_model.php';

header('Content-Type: application/json; charset=utf-8');

$model = new AdminDashboardModel($pdo);
$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$r = $_GET['r'] ?? 'colors';

/**
 * Validadores de color (#hex 3/6) o rgb(r,g,b)
 */
function is_valid_color(string $v): bool
{
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
    // Router simple por recurso (?r=colors|categories|subcategories|relations)
    if ($r === 'colors') {
        if ($method === 'GET') {
            $data = $model->getColors();
            echo json_encode(['ok' => true, 'data' => $data], JSON_UNESCAPED_UNICODE);
            exit;
        }
        if ($method === 'POST') {
            $raw = file_get_contents('php://input') ?: '';
            $payload = [];
            if (!empty($_POST)) {
                $payload = $_POST;
            } elseif ($raw) {
                $decoded = json_decode($raw, true);
                if (is_array($decoded)) {
                    $payload = $decoded;
                }
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
                echo json_encode(['ok' => true, 'data' => ['color_texto' => $texto, 'color_fondo' => $fondo, 'color_acento' => $acento]], JSON_UNESCAPED_UNICODE);
            } else {
                echo json_encode(['ok' => false, 'error' => 'No se pudieron actualizar los colores.'], JSON_UNESCAPED_UNICODE);
            }
            exit;
        }
        http_response_code(405);
        echo json_encode(['ok' => false, 'error' => 'Método no permitido.'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    // ======== Categorías ========
    if ($r === 'categories') {
        if ($method === 'GET') {
            $data = $model->listCategories();
            echo json_encode(['ok' => true, 'data' => $data], JSON_UNESCAPED_UNICODE);
            exit;
        }
        if ($method === 'POST') {
            $raw = file_get_contents('php://input') ?: '';
            $payload = json_decode($raw, true) ?: $_POST;
            $op = $payload['op'] ?? '';
            if ($op === 'create') {
                $nombre = trim((string)($payload['nombre'] ?? ''));
                if ($nombre === '') {
                    echo json_encode(['ok' => false, 'error' => 'Nombre requerido'], JSON_UNESCAPED_UNICODE);
                    exit;
                }
                $id = $model->createCategory($nombre);
                echo json_encode(['ok' => true, 'data' => ['id' => $id, 'nombre' => $nombre]], JSON_UNESCAPED_UNICODE);
                exit;
            }
            if ($op === 'update') {
                $id = (int)($payload['id'] ?? 0);
                if ($id <= 0) {
                    echo json_encode(['ok' => false, 'error' => 'ID inválido'], JSON_UNESCAPED_UNICODE);
                    exit;
                }
                $nombre = array_key_exists('nombre', $payload) ? trim((string)$payload['nombre']) : null;
                $estado = array_key_exists('estado', $payload) ? (int)$payload['estado'] : null;
                $model->updateCategory($id, $nombre, $estado);
                echo json_encode(['ok' => true, 'data' => ['id' => $id]], JSON_UNESCAPED_UNICODE);
                exit;
            }
            if ($op === 'delete') {
                $id = (int)($payload['id'] ?? 0);
                if ($id <= 0) {
                    echo json_encode(['ok' => false, 'error' => 'ID inválido'], JSON_UNESCAPED_UNICODE);
                    exit;
                }
                $model->deleteCategory($id);
                echo json_encode(['ok' => true], JSON_UNESCAPED_UNICODE);
                exit;
            }
            echo json_encode(['ok' => false, 'error' => 'Operación no soportada'], JSON_UNESCAPED_UNICODE);
            exit;
        }
        http_response_code(405);
        echo json_encode(['ok' => false, 'error' => 'Método no permitido'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    // ======== Subcategorías ========
    if ($r === 'subcategories') {
        if ($method === 'GET') {
            $data = $model->listSubcategories();
            echo json_encode(['ok' => true, 'data' => $data], JSON_UNESCAPED_UNICODE);
            exit;
        }
        if ($method === 'POST') {
            $raw = file_get_contents('php://input') ?: '';
            $payload = json_decode($raw, true) ?: $_POST;
            $op = $payload['op'] ?? '';
            if ($op === 'create') {
                $nombre = trim((string)($payload['nombre'] ?? ''));
                if ($nombre === '') {
                    echo json_encode(['ok' => false, 'error' => 'Nombre requerido'], JSON_UNESCAPED_UNICODE);
                    exit;
                }
                $id = $model->createSubcategory($nombre);
                echo json_encode(['ok' => true, 'data' => ['id' => $id, 'nombre' => $nombre]], JSON_UNESCAPED_UNICODE);
                exit;
            }
            if ($op === 'update') {
                $id = (int)($payload['id'] ?? 0);
                if ($id <= 0) {
                    echo json_encode(['ok' => false, 'error' => 'ID inválido'], JSON_UNESCAPED_UNICODE);
                    exit;
                }
                $nombre = array_key_exists('nombre', $payload) ? trim((string)$payload['nombre']) : null;
                $estado = array_key_exists('estado', $payload) ? (int)$payload['estado'] : null;
                $model->updateSubcategory($id, $nombre, $estado);
                echo json_encode(['ok' => true, 'data' => ['id' => $id]], JSON_UNESCAPED_UNICODE);
                exit;
            }
            if ($op === 'delete') {
                $id = (int)($payload['id'] ?? 0);
                if ($id <= 0) {
                    echo json_encode(['ok' => false, 'error' => 'ID inválido'], JSON_UNESCAPED_UNICODE);
                    exit;
                }
                $model->deleteSubcategory($id);
                echo json_encode(['ok' => true], JSON_UNESCAPED_UNICODE);
                exit;
            }
            echo json_encode(['ok' => false, 'error' => 'Operación no soportada'], JSON_UNESCAPED_UNICODE);
            exit;
        }
        http_response_code(405);
        echo json_encode(['ok' => false, 'error' => 'Método no permitido'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    // ======== Relaciones ========
    if ($r === 'relations') {
        if ($method === 'GET') {
            $categoryId = (int)($_GET['category_id'] ?? 0);
            if ($categoryId <= 0) {
                echo json_encode(['ok' => false, 'error' => 'category_id requerido'], JSON_UNESCAPED_UNICODE);
                exit;
            }
            $data = $model->getRelationsForCategory($categoryId);
            echo json_encode(['ok' => true, 'data' => $data], JSON_UNESCAPED_UNICODE);
            exit;
        }
        if ($method === 'POST') {
            $raw = file_get_contents('php://input') ?: '';
            $payload = json_decode($raw, true) ?: $_POST;
            $op = $payload['op'] ?? '';
            $cat = (int)($payload['category_id'] ?? 0);
            $sub = (int)($payload['subcategory_id'] ?? 0);
            if ($cat <= 0 || $sub <= 0) {
                echo json_encode(['ok' => false, 'error' => 'IDs inválidos'], JSON_UNESCAPED_UNICODE);
                exit;
            }
            if ($op === 'link') {
                $ok = $model->linkCategorySub($cat, $sub);
                echo json_encode(['ok' => $ok, 'data' => ['category_id' => $cat, 'subcategory_id' => $sub]], JSON_UNESCAPED_UNICODE);
                exit;
            }
            if ($op === 'unlink') {
                $ok = $model->unlinkCategorySub($cat, $sub);
                echo json_encode(['ok' => $ok, 'data' => ['category_id' => $cat, 'subcategory_id' => $sub]], JSON_UNESCAPED_UNICODE);
                exit;
            }
            echo json_encode(['ok' => false, 'error' => 'Operación no soportada'], JSON_UNESCAPED_UNICODE);
            exit;
        }
        http_response_code(405);
        echo json_encode(['ok' => false, 'error' => 'Método no permitido'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    // Si llega aquí, recurso desconocido
    http_response_code(404);
    echo json_encode(['ok' => false, 'error' => 'Recurso no encontrado'], JSON_UNESCAPED_UNICODE);
} catch (\Throwable $e) {
    http_response_code(500);
    echo json_encode(['ok' => false, 'error' => 'Error del servidor: ' . $e->getMessage()], JSON_UNESCAPED_UNICODE);
}
