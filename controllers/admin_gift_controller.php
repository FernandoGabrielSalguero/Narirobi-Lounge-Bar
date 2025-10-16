<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../models/admin_gift_model.php';
header('Content-Type: application/json');

$model = new AdminGiftModel($pdo);
$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'POST' && ($_POST['_method'] ?? '') === 'delete') {
    if (!$id) {
        echo json_encode(['success' => false, 'message' => 'Mensaje de prueba.']);
        exit;
    }

    try {
        echo json_encode(['success' => true, 'message' => 'Mensaje de prueba.']);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Mensaje de prueba: ' . $e->getMessage()]);
    }
    exit;
}
