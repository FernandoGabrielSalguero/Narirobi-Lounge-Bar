<?php
declare(strict_types=1);

class AdminReservasModel
{
    private \PDO $pdo;

    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
        $this->pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        $this->pdo->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE, \PDO::FETCH_ASSOC);
    }

    /** @return array<int, array<string,mixed>> */
    public function listar(): array
    {
        $sql = "SELECT id, nombre, telefono, fecha, hora, personas, estado, notas
                FROM reservas
                ORDER BY fecha DESC, hora DESC";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll();
    }

    /** @return array<string,mixed>|null */
    public function obtener(int $id): ?array
    {
        $stmt = $this->pdo->prepare(
            "SELECT id, nombre, telefono, fecha, hora, personas, estado, notas
             FROM reservas WHERE id = :id"
        );
        $stmt->bindValue(':id', $id, \PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch();
        return $row ?: null;
    }

    /** @return int ID generado */
    public function crear(array $data): int
    {
        $sql = "INSERT INTO reservas
                (nombre, telefono, fecha, hora, personas, estado, notas)
                VALUES (:nombre, :telefono, :fecha, :hora, :personas, :estado, :notas)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':nombre', $data['nombre']);
        $stmt->bindValue(':telefono', $data['telefono']);
        $stmt->bindValue(':fecha', $data['fecha']);
        $stmt->bindValue(':hora', $data['hora']);
        $stmt->bindValue(':personas', (int)$data['personas'], \PDO::PARAM_INT);
        $stmt->bindValue(':estado', $data['estado']);
        $stmt->bindValue(':notas', $data['notas']);
        $stmt->execute();
        /** @var int */
        $id = (int)$this->pdo->lastInsertId();
        return $id;
    }

    public function actualizar(int $id, array $data): bool
    {
        $sql = "UPDATE reservas
                SET nombre = :nombre,
                    telefono = :telefono,
                    fecha = :fecha,
                    hora = :hora,
                    personas = :personas,
                    estado = :estado,
                    notas = :notas
                WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':id', $id, \PDO::PARAM_INT);
        $stmt->bindValue(':nombre', $data['nombre']);
        $stmt->bindValue(':telefono', $data['telefono']);
        $stmt->bindValue(':fecha', $data['fecha']);
        $stmt->bindValue(':hora', $data['hora']);
        $stmt->bindValue(':personas', (int)$data['personas'], \PDO::PARAM_INT);
        $stmt->bindValue(':estado', $data['estado']);
        $stmt->bindValue(':notas', $data['notas']);
        return $stmt->execute();
    }

    public function eliminar(int $id): bool
    {
        $stmt = $this->pdo->prepare("DELETE FROM reservas WHERE id = :id");
        $stmt->bindValue(':id', $id, \PDO::PARAM_INT);
        return $stmt->execute();
    }
}
