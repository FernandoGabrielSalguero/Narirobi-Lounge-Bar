<?php

declare(strict_types=1);

class AdminGiftModel
{
    private \PDO $pdo;

    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
        $this->pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        $this->pdo->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE, \PDO::FETCH_ASSOC);
    }

    /**
     * Genera un código único de 6 dígitos (000000–999999), evitando colisiones.
     */
    private function generarCodigoUnico(): string
    {
        do {
            $codigo = str_pad((string)random_int(0, 999999), 6, '0', STR_PAD_LEFT);
            $stmt = $this->pdo->prepare('SELECT 1 FROM gift_cards WHERE codigo = ? LIMIT 1');
            $stmt->execute([$codigo]);
            $existe = (bool)$stmt->fetchColumn();
        } while ($existe);
        return $codigo;
    }

    /**
     * Crea una gift card en estado 'pendiente'.
     */
    public function crear(string $nombre, string $fechaVencimiento, string $texto): array
    {
        if ($nombre === '' || $fechaVencimiento === '' || $texto === '') {
            throw new \InvalidArgumentException('Nombre, fecha de vencimiento y texto son obligatorios.');
        }

        $codigo = $this->generarCodigoUnico();

        $sql = 'INSERT INTO gift_cards (nombre, texto, fecha_vencimiento, codigo, estado)
            VALUES (:nombre, :texto, :fecha_vencimiento, :codigo, "pendiente")';
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':nombre', $nombre);
        $stmt->bindValue(':texto', $texto);
        $stmt->bindValue(':fecha_vencimiento', $fechaVencimiento);
        $stmt->bindValue(':codigo', $codigo);
        $stmt->execute();

        $id = (int)$this->pdo->lastInsertId();

        return [
            'id' => $id,
            'nombre' => $nombre,
            'texto' => $texto,
            'fecha_vencimiento' => $fechaVencimiento,
            'codigo' => $codigo,
            'estado' => 'pendiente',
        ];
    }



    /**
     * Lista gift cards opcionalmente filtradas por nombre/código/estado.
     */
    public function listar(?string $nombre, ?string $codigo, ?string $estado): array
    {
        $where = [];
        $params = [];

        if ($nombre !== null && $nombre !== '') {
            $where[] = 'nombre LIKE ?';
            $params[] = '%' . $nombre . '%';
        }
        if ($codigo !== null && $codigo !== '') {
            $where[] = 'codigo LIKE ?';
            $params[] = '%' . $codigo . '%';
        }
        if ($estado !== null && $estado !== '') {
            $where[] = 'estado = ?';
            $params[] = $estado;
        }

        $sql = 'SELECT id, nombre, texto, fecha_vencimiento, codigo, estado
        FROM gift_cards ' . (count($where) ? ('WHERE ' . implode(' AND ', $where)) : '') . '
        ORDER BY id DESC';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /**
     * Canjea una gift card por código si está en estado 'pendiente'.
     */
    public function canjearPorCodigo(string $codigo): array
    {
        $this->pdo->beginTransaction();
        try {
            $stmt = $this->pdo->prepare('SELECT id, estado, fecha_vencimiento FROM gift_cards WHERE codigo = ? FOR UPDATE');
            $stmt->execute([$codigo]);
            $row = $stmt->fetch();

            if (!$row) {
                throw new \RuntimeException('Código inexistente.');
            }
            // Validar vencimiento
            $hoy = (new \DateTimeImmutable('today'))->format('Y-m-d');
            if ($row['fecha_vencimiento'] < $hoy) {
                throw new \RuntimeException('La Gift Card está vencida y no puede canjearse.');
            }
            if ($row['estado'] !== 'pendiente') {
                throw new \RuntimeException('La Gift Card ya fue canjeada.');
            }

            $upd = $this->pdo->prepare('UPDATE gift_cards SET estado = "canjeado" WHERE id = ?');
            $upd->execute([(int)$row['id']]);
            $this->pdo->commit();

            return ['id' => (int)$row['id'], 'codigo' => $codigo, 'estado' => 'canjeado', 'message' => 'Canje exitoso.'];
        } catch (\Throwable $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }
}
