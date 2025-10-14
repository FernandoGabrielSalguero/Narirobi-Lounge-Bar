<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../models/Admin_RolesModel.php';

$model = new Admin_RolesModel($pdo);
header('Content-Type: application/json');

$action = $_GET['action'] ?? $_POST['action'] ?? '';

switch ($action) {

    default:
        echo json_encode(['error' => 'Acción no reconocida']);
        break;
}
