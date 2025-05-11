<?php
// Fichier: test/UserModelTest.php
namespace Tests;

use PHPUnit\Framework\TestCase;
use Models\UserModel;
use PDO;

class UserModelTest extends TestCase
{
    private $pdo;
    private $model;
    
    protected function setUp(): void
    {
        // Créer une connexion PDO en mémoire pour les tests
        $this->pdo = new \PDOMock();
        
        // Créer la table users pour les tests
        $this->pdo->exec('CREATE TABLE users (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            email TEXT,
            password TEXT,
            nom TEXT,
            prenom TEXT,
            role TEXT,
            created_at TEXT
        )');
        
        // Initialiser le modèle
        $this->model = new UserModel($this->pdo);
    }
    
    public function testGetUserByEmail(): void
    {
        // Insérer un utilisateur de test
        $email = 'test@example.com';
        $password = password_hash('password123', PASSWORD_DEFAULT);
        
        $stmt = $this->pdo->prepare(
            "INSERT INTO users (email, password, nom, prenom, role, created_at) 
             VALUES (:email, :password, :nom, :prenom, :role, :created_at)"
        );
        
        $stmt->execute([
            ':email' => $email,
            ':password' => $password,
            ':nom' => 'Doe',
            ':prenom' => 'John',
            ':role' => 'user',
            ':created_at' => date('Y-m-d H:i:s')
        ]);
        
        // Tester la méthode getUserByEmail
        $user = $this->model->getUserByEmail($email);
        $this->assertIsArray($user);
        $this->assertEquals($email, $user['email']);
        $this->assertEquals('Doe', $user['nom']);
        $this->assertEquals('John', $user['prenom']);
        
        // Tester avec un email inexistant
        $user = $this->model->getUserByEmail('nonexistent@example.com');
        $this->assertFalse($user);
    }
    
    public function testAuthenticate(): void
    {
        // Insérer un utilisateur de test
        $email = 'auth@example.com';
        $password = 'securepass';
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        
        $stmt = $this->pdo->prepare(
            "INSERT INTO users (email, password, nom, prenom, role, created_at) 
             VALUES (:email, :password, :nom, :prenom, :role, :created_at)"
        );
        
        $stmt->execute([
            ':email' => $email,
            ':password' => $hashedPassword,
            ':nom' => 'Auth',
            ':prenom' => 'User',
            ':role' => 'user',
            ':created_at' => date('Y-m-d H:i:s')
        ]);
        
        // Tester l'authentification avec les bonnes informations
        $user = $this->model->authenticate($email, $password);
        $this->assertIsArray($user);
        $this->assertEquals($email, $user['email']);
        
        // Tester l'authentification avec un mauvais mot de passe
        $user = $this->model->authenticate($email, 'wrongpassword');
        $this->assertNull($user);
        
        // Tester l'authentification avec un email inexistant
        $user = $this->model->authenticate('nonexistent@example.com', $password);
        $this->assertNull($user);
    }
    
    public function testRegister(): void
    {
        // Tester l'enregistrement d'un utilisateur
        $email = 'register@example.com';
        $password = 'newpassword';
        $username = 'Jane Doe';
        
        $result = $this->model->register($email, $password, $username);
        $this->assertTrue($result);
        
        // Vérifier que l'utilisateur a bien été enregistré
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE email = :email");
        $stmt->execute([':email' => $email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        $this->assertEquals($email, $user['email']);
        $this->assertEquals('Doe', $user['nom']);
        $this->assertEquals('Jane', $user['prenom']);
        $this->assertEquals('user', $user['role']);
        
        // Vérifier que le mot de passe a bien été hashé
        $this->assertTrue(password_verify($password, $user['password']));
    }
    
    public function testHasUsers(): void
    {
        // Au départ, la table est vide
        $result = $this->model->hasUsers();
        $this->assertFalse($result);
        
        // Insérer un utilisateur
        $stmt = $this->pdo->prepare(
            "INSERT INTO users (email, password, nom, prenom, role, created_at) 
             VALUES (:email, :password, :nom, :prenom, :role, :created_at)"
        );
        
        $stmt->execute([
            ':email' => 'hasusers@example.com',
            ':password' => password_hash('pass', PASSWORD_DEFAULT),
            ':nom' => 'Test',
            ':prenom' => 'User',
            ':role' => 'user',
            ':created_at' => date('Y-m-d H:i:s')
        ]);
        
        // Maintenant la table n'est plus vide
        $result = $this->model->hasUsers();
        $this->assertTrue($result);
    }
}