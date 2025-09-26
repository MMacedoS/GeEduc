<?php

namespace App\Config;

use PDO;

class Database extends SingletonInstance
{
    private ?PDO $pdo;

    public function __construct()
    {
        $this->pdo = new PDO(
            'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME,
            DB_USER,
            DB_PASS
        );
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    }

    public function getConnection(): ?PDO
    {
        if ($this->pdo === null) {
            $this->__construct();
        }

        return $this->pdo;
    }

    public function closeConnection(): void
    {
        $this->pdo = null;
    }
}
