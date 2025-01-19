<?php

namespace App\Config;

use PDO;

class Database {
    private static $instance = null;
    private ?PDO $pdo; // Permite null

    private function __construct() {
        $this->pdo = new PDO(
            'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME, 
            DB_USER, 
            DB_PASS
        );
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    }

    // Garante uma única instância
    public static function getInstance(): self {
        if (!self::$instance) {
            self::$instance = new Database();
        }
        return self::$instance;
    }

    // Retorna a conexão
    public function getConnection(): ?PDO {
        return $this->pdo;
    }

    // Fecha a conexão
    public function closeConnection(): void {
        $this->pdo = null; // Agora é permitido
    }
}
