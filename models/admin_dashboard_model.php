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
}
