<?php

declare(strict_types=1);

final class BodyModel
{
  private \PDO $pdo;

  public function __construct(\PDO $pdo)
  {
    $this->pdo = $pdo;
    $this->pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
    $this->pdo->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE, \PDO::FETCH_ASSOC);
  }

  public function health(): bool
  {
    $stmt = $this->pdo->query('SELECT 1');
    return (bool) $stmt->fetchColumn();
  }

  /**
   * Devuelve 1 fila con color_texto, color_fondo, color_acento.
   */
  public function getColors(): array
  {
    $sql = "SELECT color_texto, color_fondo, color_acento
                FROM entorno_colores
                ORDER BY id ASC
                LIMIT 1";
    $stmt = $this->pdo->query($sql);
    $row = $stmt->fetch() ?: [];

    // Defaults seguros si la tabla está vacía
    return [
      'color_texto'  => $row['color_texto']  ?? '#111111',
      'color_fondo'  => $row['color_fondo']  ?? '#ffffff',
      'color_acento' => $row['color_acento'] ?? '#7c3aed',
    ];
  }

  /**
   * Devuelve lista de URLs para el carrusel.
   * Detecta si existe la columna `url_imagen`; si no existe usa `url`.
   */
  public function getImages(): array
  {
    // Detectar si existe `url_imagen`
    $hasUrlImagen = false;
    try {
      $chk = $this->pdo->query("SHOW COLUMNS FROM imagenes LIKE 'url_imagen'");
      $hasUrlImagen = (bool) $chk->fetch();
    } catch (\Throwable $e) {
      $hasUrlImagen = false;
    }

    $sql = $hasUrlImagen
      ? "SELECT COALESCE(url_imagen, url) AS url FROM imagenes WHERE COALESCE(url_imagen, url) IS NOT NULL ORDER BY id ASC"
      : "SELECT url AS url FROM imagenes WHERE url IS NOT NULL AND url <> '' ORDER BY id ASC";

    $stmt = $this->pdo->query($sql);
    $rows = $stmt->fetchAll();

    $images = [];
    foreach ($rows as $r) {
      $u = trim((string)($r['url'] ?? ''));
      if ($u !== '') {
        $images[] = ['url' => $u];
      }
    }
    return $images;
  }


  /**
   * Devuelve productos agrupados por categoría y subcategoría:
   * [
   *   { categoria_id, categoria_nombre, subcategorias: [
   *       { subcategoria_id, subcategoria_nombre, productos: [ {...} ] }
   *   ] }
   * ]
   */
  public function getGroupedProducts(): array
{
    $sql = "
        SELECT
            c.id  AS categoria_id,
            c.nombre AS categoria_nombre,
            c.orden AS categoria_orden,
            s.id  AS subcategoria_id,
            s.nombre AS subcategoria_nombre,
            s.orden AS subcategoria_orden,
            p.id AS producto_id,
            p.orden AS producto_orden,
            p.precio,
            p.nombre AS producto_nombre,
            p.aclaracion_1,
            p.aclaracion_2,
            p.aclaracion_3,
            p.detalle,
            p.icono
        FROM categoria_subcategoria cs
        INNER JOIN categorias c ON c.id = cs.category_id AND c.estado = 1
        INNER JOIN subcategorias s ON s.id = cs.subcategory_id AND s.estado = 1
        LEFT JOIN productos p 
            ON p.categoria = cs.category_id 
           AND p.subcategoria = cs.subcategory_id
        ORDER BY 
            c.orden ASC, 
            s.orden ASC,
            p.orden ASC,
            c.nombre ASC,
            s.nombre ASC,
            p.nombre ASC
    ";

    $stmt = $this->pdo->query($sql);
    $rows = $stmt->fetchAll();

    $byCat = [];
    foreach ($rows as $r) {
        $catId = (int)$r['categoria_id'];
        $subId = (int)$r['subcategoria_id'];

        if (!isset($byCat[$catId])) {
            $byCat[$catId] = [
                'categoria_id' => $catId,
                'categoria_nombre' => $r['categoria_nombre'],
                'orden' => (int)$r['categoria_orden'],
                'subcategorias' => []
            ];
        }

        if (!isset($byCat[$catId]['subcategorias'][$subId])) {
            $byCat[$catId]['subcategorias'][$subId] = [
                'subcategoria_id' => $subId,
                'subcategoria_nombre' => $r['subcategoria_nombre'],
                'orden' => (int)$r['subcategoria_orden'],
                'productos' => []
            ];
        }

        if (!empty($r['producto_id'])) {
            $producto = [
                'id' => (int)$r['producto_id'],
                'orden' => (int)$r['producto_orden'],
                'precio' => $r['precio'],
                'nombre' => $r['producto_nombre'],
                'detalle' => $r['detalle'],
                'aclaracion_1' => $r['aclaracion_1'] ?? null,
                'aclaracion_2' => $r['aclaracion_2'] ?? null,
                'aclaracion_3' => $r['aclaracion_3'] ?? null,
                'icono' => $r['icono'] ?? null,
            ];
            $byCat[$catId]['subcategorias'][$subId]['productos'][] = $producto;
        }
    }

    // Normalizar índices
    $result = [];
    foreach ($byCat as $cat) {
        $cat['subcategorias'] = array_values($cat['subcategorias']);
        $result[] = $cat;
    }
    return $result;
}

}
