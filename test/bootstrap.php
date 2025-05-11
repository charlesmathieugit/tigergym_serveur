<?php
// Fichier: test/bootstrap.php
require_once __DIR__ . '/../vendor/autoload.php';

// Définir une classe PDOMock pour les tests
class PDOMock extends PDO {
    public function __construct() {
        // Utiliser SQLite en mémoire pour les tests
        parent::__construct('sqlite::memory:');
        $this->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }
}