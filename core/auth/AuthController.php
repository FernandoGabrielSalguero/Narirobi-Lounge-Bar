<?php
declare(strict_types=1);

namespace Core\Auth;

use Exception;

require_once __DIR__ . '/AuthModel.php';
require_once __DIR__ . '/AuthView.php';

session_start();

$action = $_GET['action'] ?? '';

try {
    if ($action === 'login') {
        handleLogin();
    } else {
        AuthView::error('Acción no soportada.', 404);
    }
} catch (\Throwable $e) {
    AuthView::error('Ocurrió un error inesperado. Inténtalo nuevamente.', 500);
}

/**
 * POST JSON: { usuario, contrasena }
 * Respuesta JSON: { ok, data: {redirect} } | { ok:false, error }
 */
function handleLogin(): void
{
    $inputRaw = file_get_contents('php://input') ?: '';
    $data = json_decode($inputRaw, true, 512, JSON_THROW_ON_ERROR);

    $usuario    = trim((string)($data['usuario']    ?? ''));
    $contrasena = (string)($data['contrasena'] ?? '');

    if ($usuario === '' || $contrasena === '') {
        AuthView::error('Usuario y contraseña son obligatorios.');
    }

    $model = new AuthModel();
    $user  = $model->findUserByUsername($usuario);

    // Evitar timing leaks
    $hash = $user['contrasena'] ?? password_hash('dontcare', PASSWORD_BCRYPT);
    $valid = password_verify($contrasena, $hash);

    if (!$user || !$valid) {
        AuthView::error('Usuario o contraseña incorrectos.', 401);
    }

    // Iniciar sesión mínima
    $_SESSION['user_id']  = (int)$user['id'];
    $_SESSION['usuario']  = $user['usuario'];
    $_SESSION['rol']      = $user['rol'];
    $_SESSION['logged_in']= true;

    // Auditar último login (si existe la columna)
    $model->touchLastLogin((int)$user['id']);

    // Redirección por rol
    $redirect = '/views/collaborador/colaborador_dashboard.php';
    if ($user['rol'] === 'admin') {
        $redirect = '/views/admin/admin_dashboard.php';
    }

    AuthView::success(['redirect' => $redirect]);
}
