<?php

declare(strict_types=1);

final class HeaderModel
{
    private \PDO $pdo;

    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
        $this->pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        $this->pdo->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE, \PDO::FETCH_ASSOC);
    }

    /** Branding (placeholder para futuras preferencias en DB) */
    public function getBrandName(): string
    {
        return 'Nairobi Lounge Bar';
    }

    /**
     * Inserta una reserva en la tabla reservas.
     * Campos: nombre, telefono, fecha (DATE), hora (TIME), personas (TINYINT UNSIGNED), notas (TEXT), estado='pendiente'
     * @return int ID autoincrement generado
     */
    public function crearReserva(string $nombre, string $telefono, string $fecha, string $hora, int $personas, ?string $notas): int
    {
        if ($personas < 1) {
            throw new \InvalidArgumentException('Cantidad inválida.');
        }
        $sql = "INSERT INTO reservas (nombre, telefono, fecha, hora, personas, estado, notas, created_at, updated_at)
            VALUES (:nombre, :telefono, :fecha, :hora, :personas, 'pendiente', :notas, CURRENT_TIMESTAMP(), CURRENT_TIMESTAMP())";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':nombre', $nombre, \PDO::PARAM_STR);
        $stmt->bindValue(':telefono', $telefono, \PDO::PARAM_STR);
        $stmt->bindValue(':fecha', $fecha, \PDO::PARAM_STR);
        $stmt->bindValue(':hora', $hora, \PDO::PARAM_STR);
        $stmt->bindValue(':personas', $personas, \PDO::PARAM_INT);
        $stmt->bindValue(':notas', $notas, \PDO::PARAM_STR);
        $stmt->execute();
        return (int)$this->pdo->lastInsertId();
    }
}
