<?php
declare(strict_types=1);

namespace Core\Auth;

final class AuthView
{
    /**
     * Respuesta JSON consistente {ok, data|error}
     */
    public static function json(array $payload): void
    {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($payload, JSON_UNESCAPED_UNICODE);
        exit;
    }

    public static function success(array $data = []): void
    {
        self::json(['ok' => true, 'data' => $data]);
    }

    public static function error(string $message, int $status = 400): void
    {
        http_response_code($status);
        self::json(['ok' => false, 'error' => $message]);
    }
}
