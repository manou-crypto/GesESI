<?php
namespace App\Config;

class Database {
    private static $instance = null;
    private $pdo;

    private function __construct() {
        // Configuration MySQL pour XAMPP par défaut
        $host = '127.0.0.1';
        $dbname = 'ecole_db';
        $user = 'root';
        $pass = '';

        $dsn = "mysql:host=$host;dbname=$dbname;charset=utf8mb4";

        try {
            $this->pdo = new \PDO($dsn, $user, $pass);
            $this->pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
            $this->pdo->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE, \PDO::FETCH_OBJ);
        } catch (\PDOException $e) {
            die("Erreur de connexion a la base de donnees MySQL XAMPP. Assurez-vous d'avoir importer le schema.sql dans PhpMyAdmin : " . $e->getMessage());
        }
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new Database();
        }
        return self::$instance->pdo;
    }
}
