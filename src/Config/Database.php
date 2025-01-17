<?php

namespace App\Config;

use PDO;

class Database {
    private static $instance = null;
    private $pdo;

    public function __construct() {
        $this->pdo = new PDO(
            'mysql:host='. DB_HOST .';
            dbname='. DB_NAME, 
            DB_USER, 
            DB_PASS
        );
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    }

    public static function getInstance() {
        if (!self::$instance) {
            self::$instance = new Database();
        }
        return self::$instance;
    }

    public function getConnection() {
        return $this->pdo;
    }
}