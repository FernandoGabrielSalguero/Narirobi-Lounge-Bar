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

    /** Ejemplo: podríamos leer preferencias de branding a futuro */
    public function getBrandName(): string
    {
        return 'Nairobi Lounge Bar';
    }
}
