<?php

declare(strict_types=1);

class AdminDashboardModel
{
    private \PDO $pdo;

    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
        $this->pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
    }

    /**
     * Obtiene los colores de entorno (fila única id=1).
     * @return array{color_texto:string,color_fondo:string,color_acento:string}
     */
    public function getColors(): array
    {
        $sql = "SELECT color_texto, color_fondo, color_acento FROM entorno_colores WHERE id = 1";
        $stmt = $this->pdo->query($sql);
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        if (!$row) {
            // Si no existe, crear con defaults seguros
            $this->initializeDefaults();
            return [
                'color_texto' => '#111111',
                'color_fondo' => '#ffffff',
                'color_acento' => '#7c3aed',
            ];
        }
        return [
            'color_texto' => (string)$row['color_texto'],
            'color_fondo' => (string)$row['color_fondo'],
            'color_acento' => (string)$row['color_acento'],
        ];
    }

    /**
     * Actualiza los colores validados.
     */
    public function updateColors(string $texto, string $fondo, string $acento): bool
    {
        $sql = "INSERT INTO entorno_colores (id, color_texto, color_fondo, color_acento, updated_at)
                VALUES (1, :texto, :fondo, :acento, NOW())
                ON DUPLICATE KEY UPDATE
                    color_texto = VALUES(color_texto),
                    color_fondo = VALUES(color_fondo),
                    color_acento = VALUES(color_acento),
                    updated_at = NOW()";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':texto' => $texto,
            ':fondo' => $fondo,
            ':acento' => $acento,
        ]);
    }

    private function initializeDefaults(): void
    {
        $sql = "INSERT IGNORE INTO entorno_colores (id, color_texto, color_fondo, color_acento, updated_at)
                VALUES (1, '#111111', '#ffffff', '#7c3aed', NOW())";
        $this->pdo->exec($sql);
    }

    /* =======================
     *      CATEGORÍAS
     * ======================= */
    public function listCategories(): array
    {
        $sql = "SELECT id, nombre, estado, created_at, updated_at
                FROM categorias
                ORDER BY nombre ASC";
        $st = $this->pdo->query($sql);
        return $st->fetchAll(\PDO::FETCH_ASSOC) ?: [];
    }

    public function createCategory(string $nombre): int
    {
        $sql = "INSERT INTO categorias (nombre, estado, created_at, updated_at)
                VALUES (:nombre, 1, NOW(), NOW())";
        $st = $this->pdo->prepare($sql);
        $st->execute([':nombre' => $nombre]);
        return (int)$this->pdo->lastInsertId();
    }

    public function updateCategory(int $id, ?string $nombre, ?int $estado): bool
    {
        $sets = [];
        $params = [':id' => $id];
        if ($nombre !== null) {
            $sets[] = "nombre = :nombre";
            $params[':nombre'] = $nombre;
        }
        if ($estado !== null) {
            $sets[] = "estado = :estado";
            $params[':estado'] = $estado;
        }
        if (!$sets) return true;
        $sql = "UPDATE categorias SET " . implode(',', $sets) . ", updated_at = NOW() WHERE id = :id";
        $st = $this->pdo->prepare($sql);
        return $st->execute($params);
    }

    public function deleteCategory(int $id): bool
    {
        // eliminar relaciones primero por FK
        $this->pdo->beginTransaction();
        try {
            $st = $this->pdo->prepare("DELETE FROM categoria_subcategoria WHERE category_id = :id");
            $st->execute([':id' => $id]);
            $st2 = $this->pdo->prepare("DELETE FROM categorias WHERE id = :id");
            $st2->execute([':id' => $id]);
            $this->pdo->commit();
            return true;
        } catch (\Throwable $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }

    /* =======================
     *    SUBCATEGORÍAS
     * ======================= */
    public function listSubcategories(): array
    {
        $sql = "SELECT id, nombre, estado, created_at, updated_at
                FROM subcategorias
                ORDER BY nombre ASC";
        $st = $this->pdo->query($sql);
        return $st->fetchAll(\PDO::FETCH_ASSOC) ?: [];
    }

    public function createSubcategory(string $nombre): int
    {
        $sql = "INSERT INTO subcategorias (nombre, estado, created_at, updated_at)
                VALUES (:nombre, 1, NOW(), NOW())";
        $st = $this->pdo->prepare($sql);
        $st->execute([':nombre' => $nombre]);
        return (int)$this->pdo->lastInsertId();
    }

    public function updateSubcategory(int $id, ?string $nombre, ?int $estado): bool
    {
        $sets = [];
        $params = [':id' => $id];
        if ($nombre !== null) {
            $sets[] = "nombre = :nombre";
            $params[':nombre'] = $nombre;
        }
        if ($estado !== null) {
            $sets[] = "estado = :estado";
            $params[':estado'] = $estado;
        }
        if (!$sets) return true;
        $sql = "UPDATE subcategorias SET " . implode(',', $sets) . ", updated_at = NOW() WHERE id = :id";
        $st = $this->pdo->prepare($sql);
        return $st->execute($params);
    }

    public function deleteSubcategory(int $id): bool
    {
        $this->pdo->beginTransaction();
        try {
            $st = $this->pdo->prepare("DELETE FROM categoria_subcategoria WHERE subcategory_id = :id");
            $st->execute([':id' => $id]);
            $st2 = $this->pdo->prepare("DELETE FROM subcategorias WHERE id = :id");
            $st2->execute([':id' => $id]);
            $this->pdo->commit();
            return true;
        } catch (\Throwable $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }

    /* =======================
     *      RELACIONES
     * ======================= */
    public function getRelationsForCategory(int $categoryId): array
    {
        // asignadas
        $sqlA = "SELECT s.id, s.nombre, s.estado
                 FROM categoria_subcategoria cs
                 INNER JOIN subcategorias s ON s.id = cs.subcategory_id
                 WHERE cs.category_id = :cid
                 ORDER BY s.nombre ASC";
        $stA = $this->pdo->prepare($sqlA);
        $stA->execute([':cid' => $categoryId]);
        $assigned = $stA->fetchAll(\PDO::FETCH_ASSOC) ?: [];

        // disponibles (no asignadas y activas)
        $sqlB = "SELECT s.id, s.nombre, s.estado
                 FROM subcategorias s
                 WHERE s.id NOT IN (
                     SELECT subcategory_id FROM categoria_subcategoria WHERE category_id = :cid
                 )
                 ORDER BY s.nombre ASC";
        $stB = $this->pdo->prepare($sqlB);
        $stB->execute([':cid' => $categoryId]);
        $available = $stB->fetchAll(\PDO::FETCH_ASSOC) ?: [];

        return ['assigned' => $assigned, 'available' => $available];
    }

    public function linkCategorySub(int $categoryId, int $subcategoryId): bool
    {
        $sql = "INSERT IGNORE INTO categoria_subcategoria (category_id, subcategory_id, created_at)
                VALUES (:c, :s, NOW())";
        $st = $this->pdo->prepare($sql);
        return $st->execute([':c' => $categoryId, ':s' => $subcategoryId]);
    }

    public function unlinkCategorySub(int $categoryId, int $subcategoryId): bool
    {
        $sql = "DELETE FROM categoria_subcategoria
                WHERE category_id = :c AND subcategory_id = :s";
        $st = $this->pdo->prepare($sql);
        return $st->execute([':c' => $categoryId, ':s' => $subcategoryId]);
    }
}
