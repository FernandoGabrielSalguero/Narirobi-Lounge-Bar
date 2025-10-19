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
                s.id  AS subcategoria_id,
                s.nombre AS subcategoria_nombre,
                p.id,
                p.orden,
                p.precio,
                p.nombre,
                p.aclaracion_1,
                p.aclaracion_2,
                p.aclaracion_3,
                p.detalle,
                p.icono
            FROM productos p
            INNER JOIN categorias c   ON c.id = p.categoria AND c.estado = 1
            INNER JOIN subcategorias s ON s.id = p.subcategoria AND s.estado = 1
            ORDER BY c.nombre ASC, s.nombre ASC, p.orden ASC, p.nombre ASC
        ";

    $stmt = $this->pdo->query($sql);
    $rows = $stmt->fetchAll();
    $hasIcon = true;

    // Agrupar
    $byCat = [];
    foreach ($rows as $r) {
      $catId = (int)$r['categoria_id'];
      $subId = (int)$r['subcategoria_id'];

      if (!isset($byCat[$catId])) {
        $byCat[$catId] = [
          'categoria_id' => $catId,
          'categoria_nombre' => $r['categoria_nombre'],
          'subcategorias' => []
        ];
      }
      if (!isset($byCat[$catId]['subcategorias'][$subId])) {
        $byCat[$catId]['subcategorias'][$subId] = [
          'subcategoria_id' => $subId,
          'subcategoria_nombre' => $r['subcategoria_nombre'],
          'productos' => []
        ];
      }

      $producto = [
        'id' => (int)$r['id'],
        'orden' => (int)$r['orden'],
        'precio' => $r['precio'],
        'nombre' => $r['nombre'],
        'detalle' => $r['detalle'],
        'aclaracion_1' => $r['aclaracion_1'] ?? null,
        'aclaracion_2' => $r['aclaracion_2'] ?? null,
        'aclaracion_3' => $r['aclaracion_3'] ?? null,
      ];
      if ($hasIcon) {
        $producto['icono'] = $r['icono'] ?? null;
      }

      $byCat[$catId]['subcategorias'][$subId]['productos'][] = $producto;
    }

    // Normalizar índices de subcategorías
    $result = [];
    foreach ($byCat as $cat) {
      $cat['subcategorias'] = array_values($cat['subcategorias']);
      $result[] = $cat;
    }
    return $result;
  }
}
