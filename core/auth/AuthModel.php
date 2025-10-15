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
        /**
         * 1) Traer $pdo desde config.php (que a su vez carga .env)
         *    - Si config.php creó $pdo, lo reutilizamos.
         *    - Si no, usamos los valores de getenv() para crear un PDO acá.
         */
        $pdo = null;
        $configPath = __DIR__ . '/../../config.php';
        if (is_readable($configPath)) {
            /** @noinspection PhpIncludeInspection */
            require_once $configPath; // define $pdo y carga .env en getenv()
            if ($pdo instanceof PDO) {
                $this->pdo = $pdo;
                return;
            }
        }

        /**
         * 2) Fallback: construir PDO con variables de entorno ya cargadas por config.php
         *    (También funciona si otro bootstrap cargó el .env previamente).
         */
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
            // Mensaje genérico hacia afuera; el detalle va a logs si corresponde
            throw new PDOException('Error de conexión a la base de datos.');
        }
    }

    /**
     * Devuelve array con datos mínimos del usuario o null si no existe.
     */
public function findUserByUsername(string $usuario): ?array
{
    $sql = "SELECT id, usuario, contrasena, rol
            FROM usuarios
            WHERE LOWER(usuario) = LOWER(:usuario)
            LIMIT 1";
    $stmt = $this->pdo->prepare($sql);
    $stmt->bindValue(':usuario', trim($usuario), PDO::PARAM_STR);
    $stmt->execute();
    $row = $stmt->fetch();
    return $row ?: null;
}

    /**
     * (Opcional) Auditar último login; ignora error si la columna no existe.
     */
    public function touchLastLogin(int $userId): void
    {
        try {
            $stmt = $this->pdo->prepare("UPDATE usuarios SET ultimo_login = NOW() WHERE id = :id");
            $stmt->bindValue(':id', $userId, PDO::PARAM_INT);
            $stmt->execute();
        } catch (\Throwable $e) {
            // Silencioso
        }
    }
}
