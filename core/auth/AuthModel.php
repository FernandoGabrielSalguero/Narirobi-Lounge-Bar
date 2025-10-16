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
        $pdo = null;
        $configPath = __DIR__ . '/../../config.php';
        if (is_readable($configPath)) {
            /** @noinspection PhpIncludeInspection */
            /** @var PDO|null $pdo  <-- ayuda al analizador estático */
            require_once $configPath; // define $pdo
            if (isset($pdo) && $pdo instanceof PDO) { // OK por 'use PDO'
                $this->pdo = $pdo;
                return;
            }
        }
        // ...


        $host = getenv('DB_HOST') ?: '127.0.0.1';
        $db   = getenv('DB_NAME') ?: '';
        $user = getenv('DB_USER') ?: '';
        $pass = getenv('DB_PASS') ?: '';
        $charset = 'utf8mb4';

        if ($db === '' || $user === '') {
            throw new PDOException('Configuración de BD incompleta: defina DB_NAME y DB_USER en .env / config.php.');
        }

        $dsn = "mysql:host={$host};dbname={$db};charset={$charset}";
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_PERSISTENT         => false,
        ];

        try {
            $this->pdo = new PDO($dsn, $user, $pass, $options);
        } catch (PDOException $e) {
            error_log('[AuthModel][ERROR] Fallo conexión PDO: ' . $e->getMessage());
            throw new PDOException('Error de conexión a la base de datos.');
        }
    }
    public function findUserByUsername(string $usuario): ?array
    {
        $u = trim($usuario);

        $sql = "SELECT id, usuario, contrasena, rol
            FROM usuarios
            WHERE LOWER(usuario) = LOWER(:u)
               OR LOWER(nombre)  = LOWER(:u)
            LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':u', $u, PDO::PARAM_STR);
        $stmt->execute();
        $row = $stmt->fetch();
        if (!$row) {
            error_log('[AuthModel][INFO] Usuario no encontrado: ' . $u);
        }
        return $row ?: null;
    }

    public function touchLastLogin(int $userId): void
    {
        try {
            $stmt = $this->pdo->prepare("UPDATE usuarios SET ultimo_login = NOW() WHERE id = :id");
            $stmt->bindValue(':id', $userId, PDO::PARAM_INT);
            $stmt->execute();
        } catch (\Throwable $e) {
        }
    }
}
