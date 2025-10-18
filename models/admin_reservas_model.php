<?php

declare(strict_types=1);

class AdminReservasModel
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    /** Listado con filtros y paginación (12 por página) */
    public function listar(array $filtros, int $page = 1, int $perPage = 12): array
    {
        $where = [];
        $params = [];

        if (!empty($filtros['q'])) {
            $where[] = '(r.nombre LIKE :q OR r.telefono LIKE :q)';
            $params[':q'] = '%' . $filtros['q'] . '%';
        }
        if (!empty($filtros['estado'])) {
            $where[] = 'r.estado = :estado';
            $params[':estado'] = $filtros['estado'];
        }
        if (!empty($filtros['fecha_desde'])) {
            $where[] = 'r.fecha >= :fdesde';
            $params[':fdesde'] = $filtros['fecha_desde'];
        }
        if (!empty($filtros['fecha_hasta'])) {
            $where[] = 'r.fecha <= :fhasta';
            $params[':fhasta'] = $filtros['fecha_hasta'];
        }
        if (!empty($filtros['hora'])) {
            $where[] = 'r.hora = :hora';
            $params[':hora'] = $filtros['hora'];
        }

        $sqlWhere = $where ? ('WHERE ' . implode(' AND ', $where)) : '';
        $offset = max(0, ($page - 1) * $perPage);

        $sql = "
            SELECT r.id, r.nombre, r.telefono, r.fecha, r.hora, r.comensales, r.detalle, r.estado, r.detalle_cancelacion
            FROM reservas r
            $sqlWhere
            ORDER BY r.fecha ASC, r.hora ASC
            LIMIT :limit OFFSET :offset
        ";

        $stmt = $this->pdo->prepare($sql);
        foreach ($params as $k => $v) {
            $stmt->bindValue($k, $v, is_int($v) ? PDO::PARAM_INT : PDO::PARAM_STR);
        }
        $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // total para paginación
        $stmtCount = $this->pdo->prepare("SELECT COUNT(*) FROM reservas r $sqlWhere");
        foreach ($params as $k => $v) {
            $stmtCount->bindValue($k, $v, is_int($v) ? PDO::PARAM_INT : PDO::PARAM_STR);
        }
        $stmtCount->execute();
        $total = (int)$stmtCount->fetchColumn();

        return ['items' => $rows, 'total' => $total, 'page' => $page, 'per_page' => $perPage];
    }

    /** Crear reserva (cumple UNIQUE fecha+hora+telefono) */
    public function crear(array $data): int
    {
        $this->validarAlta($data);

        $sql = "INSERT INTO reservas
            (nombre, telefono, fecha, hora, comensales, detalle, estado, detalle_cancelacion)
            VALUES (:nombre, :telefono, :fecha, :hora, :comensales, :detalle, :estado, :detalle_cancelacion)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':nombre' => $data['nombre'],
            ':telefono' => $data['telefono'],
            ':fecha' => $data['fecha'],
            ':hora' => $data['hora'],
            ':comensales' => (int)$data['comensales'],
            ':detalle' => $data['detalle'] ?? '',
            ':estado' => $data['estado'] ?? 'pendiente',
            ':detalle_cancelacion' => $data['detalle_cancelacion'] ?? null,
        ]);

        $id = (int)$this->pdo->lastInsertId();
        $this->registrarHistorial($id, 'crear', null, $data['estado'] ?? 'pendiente', $data['detalle'] ?? '');
        return $id;
    }

    /** Obtener detalle por id */
    public function obtener(int $id): ?array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM reservas WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    /** Editar (cualquier campo salvo id) y log de cambios */
    public function editar(int $id, array $data): bool
    {
        $actual = $this->obtener($id);
        if (!$actual) {
            throw new RuntimeException('Reserva no encontrada');
        }

        // si cambian fecha/hora/telefono respetar UNIQUE
        if (
            (isset($data['fecha']) && $data['fecha'] !== $actual['fecha']) ||
            (isset($data['hora']) && $data['hora'] !== $actual['hora']) ||
            (isset($data['telefono']) && $data['telefono'] !== $actual['telefono'])
        ) {
            $this->verificarDuplicado($data['fecha'] ?? $actual['fecha'], $data['hora'] ?? $actual['hora'], $data['telefono'] ?? $actual['telefono'], $id);
        }

        $campos = [
            'nombre',
            'telefono',
            'fecha',
            'hora',
            'comensales',
            'detalle',
            'estado',
            'detalle_cancelacion'
        ];
        $sets = [];
        $params = [':id' => $id];
        foreach ($campos as $c) {
            if (array_key_exists($c, $data)) {
                $sets[] = "$c = :$c";
                $params[":$c"] = ($c === 'comensales') ? (int)$data[$c] : $data[$c];
            }
        }
        if (!$sets) {
            return true;
        }

        $sql = "UPDATE reservas SET " . implode(', ', $sets) . " WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $ok = $stmt->execute($params);

        // historial si cambió estado
        if (isset($data['estado']) && $data['estado'] !== $actual['estado']) {
            $this->registrarHistorial($id, 'cambiar_estado', $actual['estado'], $data['estado'], $data['detalle_cancelacion'] ?? null);
        } else {
            $this->registrarHistorial($id, 'editar', null, null, null);
        }

        return $ok;
    }

    /** Cambiar estado rápido (acciones en card) */
    public function cambiarEstado(int $id, string $nuevoEstado, ?string $motivo = null): bool
    {
        $valid = ['pendiente', 'confirmada', 'finalizada', 'cancelada'];
        if (!in_array($nuevoEstado, $valid, true)) {
            throw new InvalidArgumentException('Estado inválido');
        }
        $stmt = $this->pdo->prepare("UPDATE reservas SET estado = :estado, detalle_cancelacion = :motivo WHERE id = :id");
        $ok = $stmt->execute([':estado' => $nuevoEstado, ':motivo' => $motivo, ':id' => $id]);

        $actual = $this->obtener($id);
        $this->registrarHistorial($id, 'cambiar_estado', null, $nuevoEstado, $motivo);

        return $ok;
    }

    /** Ocupación (por rango) agrupada por fecha y hora */
    public function ocupacion(string $from, string $to): array
    {
        $sql = "SELECT fecha, hora, COUNT(*) AS reservas, SUM(comensales) AS total_comensales
                FROM reservas
                WHERE fecha BETWEEN :f AND :t
                GROUP BY fecha, hora
                ORDER BY fecha, hora";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':f' => $from, ':t' => $to]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /** Historial mínimo de cambios */
    public function historial(int $reservaId): array
    {
        $stmt = $this->pdo->prepare("SELECT id, reserva_id, accion, estado_anterior, estado_nuevo, nota, created_at
                                     FROM reservas_historial
                                     WHERE reserva_id = :id
                                     ORDER BY created_at DESC, id DESC");
        $stmt->execute([':id' => $reservaId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // ===== Helpers =====

    private function validarAlta(array $d): void
    {
        if (empty($d['nombre']) || empty($d['telefono']) || empty($d['fecha']) || empty($d['hora'])) {
            throw new InvalidArgumentException('Faltan campos obligatorios');
        }
        if ((int)($d['comensales'] ?? 0) < 1 || (int)$d['comensales'] > 200) {
            throw new InvalidArgumentException('Cantidad de comensales inválida (1–200)');
        }
        // Unique por fecha+hora+telefono
        $this->verificarDuplicado($d['fecha'], $d['hora'], $d['telefono'], null);
    }

    private function verificarDuplicado(string $fecha, string $hora, string $telefono, ?int $excluirId): void
    {
        $sql = "SELECT id FROM reservas WHERE fecha = :f AND hora = :h AND telefono = :t";
        $params = [':f' => $fecha, ':h' => $hora, ':t' => $telefono];
        if ($excluirId !== null) {
            $sql .= " AND id <> :id";
            $params[':id'] = $excluirId;
        }
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        if ($stmt->fetchColumn()) {
            throw new RuntimeException('Ya existe una reserva con ese teléfono para la misma fecha y hora');
        }
    }

    private function registrarHistorial(int $reservaId, string $accion, ?string $estadoAnterior, ?string $estadoNuevo, ?string $nota): void
    {
        $stmt = $this->pdo->prepare("INSERT INTO reservas_historial
            (reserva_id, accion, estado_anterior, estado_nuevo, nota)
            VALUES (:rid, :accion, :ea, :en, :nota)");
        $stmt->execute([
            ':rid' => $reservaId,
            ':accion' => $accion,
            ':ea' => $estadoAnterior,
            ':en' => $estadoNuevo,
            ':nota' => $nota
        ]);
    }
}
