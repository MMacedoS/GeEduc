<?php

namespace App\Config;

use PDO;

class Database
{
    private static ?Database $instance = null; 
    private PDO $pdo;

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

    private function __clone() {}

    public function __wakeup() {}

    public static function getInstance(): self
    {
        if (!self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getConnection(): PDO
    {
        return $this->pdo;
    }

    public function closeConnection()
    {
        $this->pdo = null; 
    }
}
