<?php
declare(strict_types=1);

ini_set('display_errors', '0'); // En producción ocultamos errores
error_reporting(E_ALL);

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../models/admin_orden_model.php';

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

try {
    if (!isset($pdo) || !$pdo instanceof PDO) {
        throw new RuntimeException('Conexión PDO no disponible');
    }

    $model = new AdminOrdenModel($pdo);
    $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

    if ($method === 'GET') {
        // Devuelve estructura ordenable completa (categorías, subcategorías, productos + mapas)
        $data = $model->obtenerEstructuraOrden();
        jsonOk($data);
    }

    if ($method === 'POST') {
        // Espera JSON: { action:"updateOrder", categorias:[{id,orden}], subcategorias:[...], productos:[...] }
        $raw = file_get_contents('php://input') ?: '';
        $input = json_decode($raw, true);
        if (!is_array($input)) {
            jsonErr('JSON inválido');
        }
        $action = $input['action'] ?? '';
        if ($action !== 'updateOrder') {
            jsonErr('Acción no soportada', 405);
        }

        $cats = is_array($input['categorias'] ?? null) ? $input['categorias'] : [];
        $subs = is_array($input['subcategorias'] ?? null) ? $input['subcategorias'] : [];
        $prods = is_array($input['productos'] ?? null) ? $input['productos'] : [];

        // Validaciones básicas
        $validaLista = static function(array $lst): bool {
            foreach ($lst as $it) {
                if (!isset($it['id'], $it['orden'])) return false;
                if (!is_numeric($it['id']) || !is_numeric($it['orden'])) return false;
            }
            return true;
        };
        if (!$validaLista($cats) || !$validaLista($subs) || !$validaLista($prods)) {
            jsonErr('Formato de listas inválido');
        }

        $model->actualizarOrdenes($cats, $subs, $prods);
        jsonOk(['actualizado' => true]);
    }

    jsonErr('Método no permitido', 405);

} catch (Throwable $e) {
    // Log opcional a archivo si lo necesitás
    jsonErr('Error: ' . $e->getMessage(), 500);
}
