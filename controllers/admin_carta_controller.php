<?php

declare(strict_types=1);
ini_set('display_errors', '1');
error_reporting(E_ALL);

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../models/admin_carta_model.php';
header('Content-Type: application/json; charset=utf-8');

function respondOk($data): void
{
    echo json_encode(['ok' => true, 'data' => $data], JSON_UNESCAPED_UNICODE);
    exit;
}
function respondErr(string $msg, int $code = 400): void
{
    http_response_code($code);
    echo json_encode(['ok' => false, 'error' => $msg], JSON_UNESCAPED_UNICODE);
    exit;
}

try {
    if (!isset($pdo) || !($pdo instanceof PDO)) {
        respondErr('Conexión no disponible', 500);
    }
    $model = new AdminCartaModel($pdo);
    $action = $_GET['action'] ?? '';

    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        switch ($action) {
            case 'listCategorias':
                respondOk($model->listCategorias());

            case 'listSubcategorias':
                $cat = (int)($_GET['categoria'] ?? 0);
                respondOk($model->listSubcategoriasByCategoria($cat));

                // Nota: nextOrden ya no es necesario en UI, se mantiene omitido aquí.

            case 'listProductos':
                respondOk($model->listProductos());

            default:
                respondErr('Acción GET no reconocida', 404);
        }
    }

    // POST: JSON body
    $raw = file_get_contents('php://input');
    $json = json_decode($raw ?: '', true) ?: [];

    switch ($action) {
        case 'createProducto':
            $p = [
                'precio' => (float)($json['precio'] ?? 0),
                'nombre' => trim((string)($json['nombre'] ?? '')),
                'aclaracion_1' => trim((string)($json['aclaracion_1'] ?? '')),
                'aclaracion_2' => trim((string)($json['aclaracion_2'] ?? '')),
                'aclaracion_3' => trim((string)($json['aclaracion_3'] ?? '')),
                'detalle' => trim((string)($json['detalle'] ?? '')),
                'categoria' => (int)($json['categoria'] ?? 0),
                'subcategoria' => (int)($json['subcategoria'] ?? 0),
                'icono' => trim((string)($json['icono'] ?? '')),
            ];

            $allowedIconos = ['', 'sin_tacc', 'nuevo', 'promo'];
            if (!in_array($p['icono'], $allowedIconos, true)) {
                respondErr('Icono inválido. Valores permitidos: (vacío), sin_tacc, nuevo, promo');
            }

            if ($p['precio'] <= 0 || $p['categoria'] <= 0 || $p['subcategoria'] <= 0 || $p['nombre'] === '') {
                respondErr('Datos inválidos: nombre, precio (>0), categoría y subcategoría son obligatorios');
            }
            if (mb_strlen($p['detalle']) > 255) {
                respondErr('Detalle no puede superar 255 caracteres');
            }

            // Validación del vínculo cat-subcat
            $subs = $model->listSubcategoriasByCategoria($p['categoria']);
            $okPair = false;
            foreach ($subs as $s) {
                if ((int)$s['id'] === $p['subcategoria']) {
                    $okPair = true;
                    break;
                }
            }
            if (!$okPair) {
                respondErr('La subcategoría no pertenece a la categoría seleccionada');
            }

            $id = $model->createProducto($p);
            respondOk(['id' => $id]);

        case 'updateProducto':
            $id = (int)($json['id'] ?? 0);
            if ($id <= 0) {
                respondErr('ID inválido');
            }
            $p = [
                'precio' => (float)($json['precio'] ?? 0),
                'nombre' => trim((string)($json['nombre'] ?? '')),
                'aclaracion_1' => trim((string)($json['aclaracion_1'] ?? '')),
                'aclaracion_2' => trim((string)($json['aclaracion_2'] ?? '')),
                'aclaracion_3' => trim((string)($json['aclaracion_3'] ?? '')),
                'detalle' => trim((string)($json['detalle'] ?? '')),
                'categoria' => (int)($json['categoria'] ?? 0),
                'subcategoria' => (int)($json['subcategoria'] ?? 0),
                'icono' => trim((string)($json['icono'] ?? '')),
            ];

            $allowedIconos = ['', 'sin_tacc', 'nuevo', 'promo'];
            if (!in_array($p['icono'], $allowedIconos, true)) {
                respondErr('Icono inválido. Valores permitidos: (vacío), sin_tacc, nuevo, promo');
            }

            if ($p['precio'] <= 0 || $p['categoria'] <= 0 || $p['subcategoria'] <= 0 || $p['nombre'] === '') {
                respondErr('Datos inválidos');
            }
            if (mb_strlen($p['detalle']) > 255) {
                respondErr('Detalle no puede superar 255 caracteres');
            }
            // Validar vínculo cat-subcat al editar
            $subs = $model->listSubcategoriasByCategoria($p['categoria']);
            $okPair = false;
            foreach ($subs as $s) {
                if ((int)$s['id'] === $p['subcategoria']) {
                    $okPair = true;
                    break;
                }
            }
            if (!$okPair) {
                respondErr('La subcategoría no pertenece a la categoría seleccionada');
            }

            $ok = $model->updateProducto($id, $p);
            respondOk(['updated' => (bool)$ok]);

        case 'deleteProducto':
            $id = (int)($json['id'] ?? 0);
            if ($id <= 0) {
                respondErr('ID inválido');
            }
            $ok = $model->deleteProducto($id);
            respondOk(['deleted' => (bool)$ok]);

        default:
            respondErr('Acción POST no reconocida', 404);
    }
} catch (Throwable $e) {
    respondErr('Error inesperado: ' . $e->getMessage(), 500);
}
