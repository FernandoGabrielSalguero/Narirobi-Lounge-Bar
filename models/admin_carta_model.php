<?php

declare(strict_types=1);

class AdminCartaModel
{
    private \PDO $pdo;

    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
        $this->pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        $this->pdo->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE, \PDO::FETCH_ASSOC);
    }

    /** CATEGORÍAS **/
    public function listCategorias(): array
    {
        $sql = "SELECT id, nombre FROM categorias WHERE estado = 1 ORDER BY nombre ASC";
        $st = $this->pdo->query($sql);
        return $st->fetchAll();
    }

    public function listSubcategoriasByCategoria(int $categoriaId): array
    {
        if ($categoriaId <= 0) {
            return [];
        }
        $sql = "SELECT s.id, s.nombre
                FROM categoria_subcategoria cs
                INNER JOIN subcategorias s ON s.id = cs.subcategory_id
                WHERE cs.category_id = :cat AND s.estado = 1
                ORDER BY s.nombre ASC";
        $st = $this->pdo->prepare($sql);
        $st->execute([':cat' => $categoriaId]);
        return $st->fetchAll();
    }

    /** ORDEN (interno, ya no expuesto en UI) **/
    public function nextOrden(): int
    {
        $sql = "SELECT COALESCE(MAX(orden), 0) + 1 AS next FROM productos";
        $st = $this->pdo->query($sql);
        $row = $st->fetch();
        return (int)($row['next'] ?? 1);
    }

    /** CRUD PRODUCTOS **/
    public function createProducto(array $p): int
    {
        // Calculamos orden internamente para cumplir NOT NULL sin exponer en UI
        $orden = $this->nextOrden();
        $sql = "INSERT INTO productos
        (orden, precio, nombre, aclaracion_1, aclaracion_2, aclaracion_3, detalle, categoria, subcategoria, icono)
        VALUES (:orden, :precio, :nombre, :a1, :a2, :a3, :detalle, :categoria, :subcategoria, :icono)";
        $st = $this->pdo->prepare($sql);
        $st->execute([
            ':orden' => $orden,
            ':precio' => $p['precio'],
            ':nombre' => $p['nombre'],
            ':a1' => $p['aclaracion_1'],
            ':a2' => $p['aclaracion_2'],
            ':a3' => $p['aclaracion_3'],
            ':detalle' => $p['detalle'],
            ':categoria' => $p['categoria'],
            ':subcategoria' => $p['subcategoria'],
            ':icono' => ($p['icono'] ?? ''),
        ]);

        return (int)$this->pdo->lastInsertId();
    }

    public function listProductos(): array
    {
        $sql = "SELECT
            p.id,
            p.nombre,
            p.precio,
            p.categoria,
            p.subcategoria,
            p.aclaracion_1,
            p.aclaracion_2,
            p.aclaracion_3,
            p.detalle,
            p.icono,
            c.nombre AS categoria_nombre,
            s.nombre AS subcategoria_nombre
        FROM productos p
        INNER JOIN categorias c ON c.id = p.categoria
        INNER JOIN subcategorias s ON s.id = p.subcategoria
        ORDER BY p.id ASC";
        $st = $this->pdo->query($sql);
        return $st->fetchAll();
    }

    public function updateProducto(int $id, array $p): bool
    {
        $sql = "UPDATE productos
           SET precio=:precio, nombre=:nombre, aclaracion_1=:a1,
               aclaracion_2=:a2, aclaracion_3=:a3, detalle=:detalle,
               categoria=:categoria, subcategoria=:subcategoria, icono=:icono
         WHERE id=:id";
        $st = $this->pdo->prepare($sql);
        return $st->execute([
            ':precio' => $p['precio'],
            ':nombre' => $p['nombre'],
            ':a1' => $p['aclaracion_1'],
            ':a2' => $p['aclaracion_2'],
            ':a3' => $p['aclaracion_3'],
            ':detalle' => $p['detalle'],
            ':categoria' => $p['categoria'],
            ':subcategoria' => $p['subcategoria'],
            ':icono' => ($p['icono'] ?? ''),
            ':id' => $id
        ]);
    }

    public function deleteProducto(int $id): bool
    {
        $st = $this->pdo->prepare("DELETE FROM productos WHERE id = :id");
        return $st->execute([':id' => $id]);
    }
}
