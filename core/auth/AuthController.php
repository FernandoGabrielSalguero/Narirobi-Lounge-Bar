<?php

declare(strict_types=1);

namespace Core\Auth;

require_once __DIR__ . '/AuthModel.php';
require_once __DIR__ . '/AuthView.php';

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

session_start();

$action = $_GET['action'] ?? '';

try {
    switch ($action) {
        case 'view_login':
            header('Content-Type: text/html; charset=utf-8');
            echo AuthView::loginModal();
            exit;

        case 'login':
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                serverLog('WARN', 'Método no permitido para login', ['method' => $_SERVER['REQUEST_METHOD'] ?? 'unknown']);
                AuthView::error('Método no permitido.', 405);
            }
            handleLogin();
            break;

        default:
            serverLog('WARN', 'Acción no soportada', ['action' => $action]);
            AuthView::error('Acción no soportada.', 404);
    }
} catch (\Throwable $e) {
    serverLog('ERROR', 'Excepción no controlada', ['msg' => $e->getMessage()]);
    AuthView::error('Ocurrió un error inesperado. Inténtalo nuevamente.', 500);
}

function handleLogin(): void
{
    $inputRaw = file_get_contents('php://input') ?: '';
    $data = json_decode($inputRaw, true);

    if (!is_array($data)) {
        serverLog('WARN', 'Payload inválido (no JSON).', ['raw' => mb_substr($inputRaw, 0, 200)]);
        AuthView::error('Payload inválido.', 400);
    }

    $usuario    = trim((string)($data['usuario']    ?? ''));
    $contrasena = (string)($data['contrasena'] ?? '');

    if ($usuario === '' || $contrasena === '') {
        serverLog('WARN', 'Campos obligatorios faltantes', ['usuarioEmpty' => $usuario === '', 'contrasenaEmpty' => $contrasena === '']);
        AuthView::error('Usuario y contraseña son obligatorios.');
    }

    $model = new AuthModel();
    $user  = $model->findUserByUsername($usuario);

    $hash = $user['contrasena'] ?? password_hash('dontcare', PASSWORD_BCRYPT);
    $valid = password_verify($contrasena, $hash);

    serverLog('INFO', 'Intento de login', [
        'usuario' => $usuario,
        'userFound' => (bool)$user,
        'hashLen' => isset($user['contrasena']) ? strlen((string)$user['contrasena']) : 0,
        'valid' => $valid
    ]);

    if (!$user || !$valid) {
        AuthView::error('Usuario o contraseña incorrectos.', 401);
    }

    $_SESSION['user_id']   = (int)$user['id'];
    $_SESSION['usuario']   = $user['usuario'];
    $_SESSION['rol']       = $user['rol'];
    $_SESSION['logged_in'] = true;

    $model->touchLastLogin((int)$user['id']);

    $redirect = '/views/colaborador/colaborador_dashboard_view.php';
    if ($user['rol'] === 'admin') {
        $redirect = '/views/admin/admin_dashboard_view.php';
    }

    serverLog('INFO', 'Login OK, redirigiendo', ['redirect' => $redirect, 'rol' => $user['rol']]);
    AuthView::success(['redirect' => $redirect]);
}


function serverLog(string $level, string $message, array $context = []): void
{
    $ctx = $context ? ' ' . json_encode($context, JSON_UNESCAPED_UNICODE) : '';
    error_log(sprintf('[AuthAPI][%s] %s%s', $level, $message, $ctx));
}
