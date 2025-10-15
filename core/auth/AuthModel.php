<?php
declare(strict_types=1);

namespace Core\Auth;

use PDO;
use PDOException;

final class AuthModel
{
    private PDO $pdo;

    public function __construct()
    {
        // 1) Intentar tomar $pdo de config.php
        $pdo = null;
        $configPath = __DIR__ . '/../../config.php';
        if (file_exists($configPath)) {
            /** @noinspection PhpIncludeInspection */
            require_once $configPath;
            if (isset($pdo) && $pdo instanceof PDO) {
                $this->pdo = $pdo;
                return;
            }
        }
        // 2) Intentar crear PDO con constantes
        $host = defined('DB_HOST') ? DB_HOST : 'localhost';
        $db   = defined('DB_NAME') ? DB_NAME : '';
        $user = defined('DB_USER') ? DB_USER : '';
        $pass = defined('DB_PASS') ? DB_PASS : '';
        $charset = 'utf8mb4';

        $dsn = "mysql:host={$host};dbname={$db};charset={$charset}";
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_PERSISTENT         => false,
        ];
        try {
            $this->pdo = new PDO($dsn, $user, $pass, $options);
        } catch (PDOException $e) {
            throw new PDOException('Error de conexión a la base de datos.');
        }
    }

    /**
     * Devuelve array con datos mínimos del usuario o null si no existe.
     */
    public function findUserByUsername(string $usuario): ?array
    {
        $sql = "SELECT id, usuario, contrasena, rol FROM usuarios WHERE usuario = :usuario LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':usuario', $usuario, PDO::PARAM_STR);
        $stmt->execute();
        $row = $stmt->fetch();
        return $row ?: null;
    }

public function touchLastLogin(int $userId): void
{
    try {
        $stmt = $this->pdo->prepare("UPDATE usuarios SET ultimo_login = NOW() WHERE id = :id");
        $stmt->bindValue(':id', $userId, PDO::PARAM_INT);
        $stmt->execute();
    } catch (\Throwable $e) {
        // Silencioso: no romper si la columna no existe.
    }
}

}
