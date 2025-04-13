<?php

namespace Database;

class Database {
    private static ?\PDO $instance = null;
    private static string $dsn = 'mysql:localhost;port=3306;dbname=charles_db1;charset=utf8';
    private static string $username = 'charles_db1';
    private static string $password = 'Kag3qtepr***';

    private function __construct() {}

    public static function getInstance(): \PDO {
        if (self::$instance === null) {
            try {
                error_log("=== Tentative de connexion à la base de données ===");
                error_log("DSN: " . self::$dsn);
                error_log("Username: " . self::$username);
                
                self::$instance = new \PDO(self::$dsn, self::$username, self::$password);
                self::$instance->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
                self::$instance->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE, \PDO::FETCH_ASSOC);
                
                // Vérifier que la base de données est accessible
                $testQuery = "SELECT COUNT(*) as count FROM articles";
                $stmt = self::$instance->query($testQuery);
                $result = $stmt->fetch();
                error_log("Nombre total d'articles dans la base : " . $result['count']);
                
                // Vérifier les catégories disponibles
                $catQuery = "SELECT DISTINCT category, COUNT(*) as count FROM articles GROUP BY category";
                $stmt = self::$instance->query($catQuery);
                $categories = $stmt->fetchAll();
                foreach ($categories as $cat) {
                    error_log("Catégorie '{$cat['category']}' : {$cat['count']} article(s)");
                }
                
                error_log("=== Connexion à la base de données réussie ===");
                
            } catch (\PDOException $e) {
                error_log("Erreur de connexion à la base de données : " . $e->getMessage());
                die('Erreur de connexion : ' . $e->getMessage());
            }
        }

        return self::$instance;
    }

    private function __clone() {}
    
    public function __wakeup() {
        throw new \Exception("Cannot deserialize a singleton.");
    }
}
