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

    /** Placeholder para futuras consultas (p.ej. menú/carta) */
    public function health(): bool
    {
        return true;
    }
}
