<?php
// Fichier: test/ModelTest.php
namespace Tests;

use PHPUnit\Framework\TestCase;
use Models\Model;
use PDO;

class ModelTest extends TestCase
{
    private $pdo;
    private $model;
    
    protected function setUp(): void
    {
        // Créer une connexion PDO en mémoire pour les tests
        $this->pdo = new \PDOMock();
        
        // Créer une table de test
        $this->pdo->exec('CREATE TABLE test_table (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name TEXT,
            value INTEGER
        )');
        
        // Insérer des données de test
        $this->pdo->exec("INSERT INTO test_table (name, value) VALUES ('test1', 10)");
        $this->pdo->exec("INSERT INTO test_table (name, value) VALUES ('test2', 20)");
        
        // Initialiser le modèle avec la table de test
        $this->model = new class($this->pdo, 'test_table') extends Model {
            public function __construct(PDO $db, string $table) {
                parent::__construct($db, $table);
            }
        };
    }
    
    public function testFindById(): void
    {
        // Test de récupération d'un enregistrement par ID
        $result = $this->model->findById(1, false);
        $this->assertIsArray($result);
        $this->assertEquals('test1', $result['name']);
        $this->assertEquals(10, $result['value']);
        
        // Test avec fetch en mode objet
        $result = $this->model->findById(1, true);
        $this->assertIsObject($result);
        $this->assertEquals('test1', $result->name);
        $this->assertEquals(10, $result->value);
        
        // Test avec un ID inexistant
        $result = $this->model->findById(999, true);
        $this->assertFalse($result);
    }
    
    public function testFindAll(): void
    {
        // Test de récupération de tous les enregistrements
        $results = $this->model->findAll(false);
        $this->assertIsArray($results);
        $this->assertCount(2, $results);
        $this->assertEquals('test1', $results[0]['name']);
        $this->assertEquals('test2', $results[1]['name']);
        
        // Test avec fetch en mode objet
        $results = $this->model->findAll(true);
        $this->assertIsArray($results);
        $this->assertCount(2, $results);
        $this->assertEquals('test1', $results[0]->name);
        $this->assertEquals('test2', $results[1]->name);
    }
    
    public function testCreate(): void
    {
        // Test de création d'un enregistrement
        $id = $this->model->create(['name', 'value'], 'test3', 30);
        $this->assertEquals(3, $id);
        
        // Vérifier que l'enregistrement a bien été créé
        $stmt = $this->pdo->prepare("SELECT * FROM test_table WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        $this->assertEquals('test3', $result['name']);
        $this->assertEquals(30, $result['value']);
    }
    
    public function testUpdate(): void
    {
        // Test de mise à jour d'un enregistrement
        $result = $this->model->update(1, ['name', 'value'], 'updated', 100);
        $this->assertTrue($result);
        
        // Vérifier que l'enregistrement a bien été mis à jour
        $stmt = $this->pdo->prepare("SELECT * FROM test_table WHERE id = :id");
        $stmt->execute([':id' => 1]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        $this->assertEquals('updated', $result['name']);
        $this->assertEquals(100, $result['value']);
    }
    
    public function testDelete(): void
    {
        // Test de suppression d'un enregistrement
        $result = $this->model->delete(1);
        $this->assertTrue($result);
        
        // Vérifier que l'enregistrement a bien été supprimé
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM test_table WHERE id = :id");
        $stmt->execute([':id' => 1]);
        $count = $stmt->fetchColumn();
        
        $this->assertEquals(0, $count);
    }
}