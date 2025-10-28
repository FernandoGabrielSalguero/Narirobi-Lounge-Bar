<?php
declare(strict_types=1);

final class AdminOrdenModel
{
    private \PDO $pdo;

    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
        $this->pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        $this->pdo->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE, \PDO::FETCH_ASSOC);
    }

    /**
     * Devuelve estructura completa:
     * - categorias [{id,nombre,orden}]
     * - subcategorias [{id,nombre,orden}]
     * - productos [{id,nombre,orden,categoria_id,subcategoria_id}]
     * - mapa_subcategorias_por_categoria {catId: [sub...]}
     * - mapa_productos_por_cat_sub {"catId_subId": [prod...]}
     * @return array<string,mixed>
     */
    public function obtenerEstructuraOrden(): array
    {
        // Categorías
        $cats = $this->pdo->query(
            "SELECT id, nombre, orden FROM categorias ORDER BY orden ASC, id ASC"
        )->fetchAll();

        // Subcategorías
        $subs = $this->pdo->query(
            "SELECT id, nombre, orden FROM subcategorias ORDER BY orden ASC, id ASC"
        )->fetchAll();

        // Relación categoría-subcategoría (para armar árbol)
        $rel = $this->pdo->query(
            "SELECT category_id, subcategory_id FROM categoria_subcategoria"
        )->fetchAll();

        // Productos (solo columnas necesarias)
        $stmt = $this->pdo->query(
    "SELECT
        id,
        nombre,
        orden,
        precio,
        aclaracion_1,
        aclaracion_2,
        aclaracion_3,
        detalle,
        icono,
        categoria  AS categoria_id,
        subcategoria AS subcategoria_id
     FROM productos
     ORDER BY orden ASC, id ASC"
);
$prods = $stmt->fetchAll();

        // Mapear subcategorías por categoría
        $mapSubPorCat = [];
        $subById = [];
        foreach ($subs as $s) { $subById[(int)$s['id']] = $s; }
        foreach ($rel as $r) {
            $cid = (int)$r['category_id'];
            $sid = (int)$r['subcategory_id'];
            if (!isset($subById[$sid])) continue;
            $mapSubPorCat[$cid] ??= [];
            $mapSubPorCat[$cid][] = $subById[$sid];
        }

        // Mapear productos por (cat,sub)
        $mapProdPorCatSub = [];
        foreach ($prods as $p) {
            $key = ((int)$p['categoria_id']).'_'.((int)$p['subcategoria_id']);
            $mapProdPorCatSub[$key] ??= [];
            $mapProdPorCatSub[$key][] = $p;
        }

        return [
            'categorias' => $cats,
            'subcategorias' => $subs,
            'productos' => $prods,
            'mapa_subcategorias_por_categoria' => $mapSubPorCat,
            'mapa_productos_por_cat_sub' => $mapProdPorCatSub,
        ];
    }

    /**
     * Actualiza órdenes de forma transaccional.
     * @param array<int,array{id:int,orden:int}> $cats
     * @param array<int,array{id:int,orden:int}> $subs
     * @param array<int,array{id:int,orden:int}> $prods
     */
    public function actualizarOrdenes(array $cats, array $subs, array $prods): void
    {
        $this->pdo->beginTransaction();
        try {
            if ($cats) {
                $stmtC = $this->pdo->prepare("UPDATE categorias SET orden = :orden WHERE id = :id");
                foreach ($cats as $c) {
                    $stmtC->bindValue(':orden', (int)$c['orden'], \PDO::PARAM_INT);
                    $stmtC->bindValue(':id', (int)$c['id'], \PDO::PARAM_INT);
                    $stmtC->execute();
                }
            }
            if ($subs) {
                $stmtS = $this->pdo->prepare("UPDATE subcategorias SET orden = :orden WHERE id = :id");
                foreach ($subs as $s) {
                    $stmtS->bindValue(':orden', (int)$s['orden'], \PDO::PARAM_INT);
                    $stmtS->bindValue(':id', (int)$s['id'], \PDO::PARAM_INT);
                    $stmtS->execute();
                }
            }
            if ($prods) {
                $stmtP = $this->pdo->prepare("UPDATE productos SET orden = :orden WHERE id = :id");
                foreach ($prods as $p) {
                    $stmtP->bindValue(':orden', (int)$p['orden'], \PDO::PARAM_INT);
                    $stmtP->bindValue(':id', (int)$p['id'], \PDO::PARAM_INT);
                    $stmtP->execute();
                }
            }
            $this->pdo->commit();
        } catch (\Throwable $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }
}
