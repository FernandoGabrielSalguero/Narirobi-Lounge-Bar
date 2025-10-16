<?php

date_default_timezone_set('America/Argentina/Buenos_Aires');

function loadEnv($path) {
    if (!file_exists($path)) return;

    $lines = @file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    if (!$lines) return;

    foreach ($lines as $line) {
        $line = trim($line);
        if ($line === '' || str_starts_with($line, '#')) continue;
        if (!str_contains($line, '=')) continue;
        [$name, $value] = explode('=', $line, 2);
        putenv(trim($name) . '=' . trim($value));
    }
}

loadEnv(__DIR__ . '/.env');

try {
    $dsn = sprintf(
        'mysql:host=%s;dbname=%s;charset=utf8mb4',
        getenv('DB_HOST'),
        getenv('DB_NAME')
    );

    $pdo = new \PDO(
        $dsn,
        getenv('DB_USER'),
        getenv('DB_PASS'),
        [
            \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
            \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
            \PDO::ATTR_PERSISTENT => false
        ]
    );
} catch (\PDOException $e) {
    error_log('[Config][ERROR] Error de conexión: ' . $e->getMessage());
    http_response_code(500);
    die('Error de conexión a la base de datos.');
}
