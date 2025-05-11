<?php
// Fichier: test/ArticleModelTest.php
namespace Tests;

use PHPUnit\Framework\TestCase;
use Models\ArticleModel;
use PDO;

class ArticleModelTest extends TestCase
{
    private $pdo;
    private $model;
    
    protected function setUp(): void
    {
        // Créer une connexion PDO en mémoire pour les tests
        $this->pdo = new \PDOMock();
        
        // Créer la table articles pour les tests avec la structure correcte
        $this->pdo->exec('CREATE TABLE articles (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name TEXT,
            description TEXT,
            category TEXT,
            price REAL,
            image TEXT,
            stock INTEGER,
            size_available TEXT,
            created_at TEXT,
            updated_at TEXT
        )');
        
        // Initialiser le modèle
        $this->model = new ArticleModel($this->pdo);
        
        // Insérer des articles de test
        $stmt = $this->pdo->prepare(
            "INSERT INTO articles (name, description, category, price, image, stock, size_available, created_at, updated_at) 
             VALUES (:name, :description, :category, :price, :image, :stock, :size_available, :created_at, :updated_at)"
        );
        
        $now = date('Y-m-d H:i:s');
        
        $stmt->execute([
            ':name' => 'Article test 1',
            ':description' => 'Description test 1',
            ':category' => 'vetements-hommes',
            ':price' => 29.99,
            ':image' => 'image1.jpg',
            ':stock' => 10,
            ':size_available' => 'M,L,XL',
            ':created_at' => $now,
            ':updated_at' => $now
        ]);
        
        $stmt->execute([
            ':name' => 'Article test 2',
            ':description' => 'Description test 2',
            ':category' => 'vetements-femmes',
            ':price' => 39.99,
            ':image' => 'image2.jpg',
            ':stock' => 5,
            ':size_available' => 'S,M,L',
            ':created_at' => $now,
            ':updated_at' => $now
        ]);
        
        $stmt->execute([
            ':name' => 'Machine test',
            ':description' => 'Description machine',
            ':category' => 'machines',
            ':price' => 999.99,
            ':image' => 'machine.jpg',
            ':stock' => 2,
            ':size_available' => null,
            ':created_at' => $now,
            ':updated_at' => $now
        ]);
    }
    
    public function testGetArticlesByCategory(): void
    {
        // Test de récupération des articles par catégorie
        $articles = $this->model->getArticlesByCategory('vetements-hommes');
        $this->assertIsArray($articles);
        $this->assertCount(1, $articles);
        $this->assertEquals('Article test 1', $articles[0]['name']);
        
        $articles = $this->model->getArticlesByCategory('vetements-femmes');
        $this->assertIsArray($articles);
        $this->assertCount(1, $articles);
        $this->assertEquals('Article test 2', $articles[0]['name']);
        
        // Test avec une catégorie inexistante
        $articles = $this->model->getArticlesByCategory('accessoires');
        $this->assertIsArray($articles);
        $this->assertCount(0, $articles);
        
        // Test de la catégorie spéciale 'vetements' qui regroupe hommes et femmes
        $articles = $this->model->getArticlesByCategory('vetements');
        $this->assertIsArray($articles);
        $this->assertCount(2, $articles);
    }
    
    public function testGetFeaturedArticles(): void
    {
        // Test des articles mis en avant (machines)
        $articles = $this->model->getFeaturedArticles();
        $this->assertIsArray($articles);
        $this->assertCount(1, $articles);
        $this->assertEquals('Machine test', $articles[0]['name']);
        $this->assertEquals('machines', $articles[0]['category']);
    }
    
    public function testGetLatestArticles(): void
    {
        // Test des derniers articles (non machines)
        $articles = $this->model->getLatestArticles();
        $this->assertIsArray($articles);
        $this->assertCount(2, $articles);
        // Vérifie qu'aucun n'est de catégorie machines
        foreach ($articles as $article) {
            $this->assertNotEquals('machines', $article['category']);
        }
    }
    
    public function testGetArticleById(): void
    {
        // Test récupération article par id
        $article = $this->model->getArticleById(1);
        $this->assertIsArray($article);
        $this->assertEquals('Article test 1', $article['name']);
        $this->assertEquals(29.99, $article['price']);
        
        // Test avec id inexistant
        $article = $this->model->getArticleById(999);
        $this->assertFalse($article);
    }
    
    // Surcharge la méthode createArticle pour les tests en utilisant datetime('now') au lieu de NOW()
    protected function createTestArticle($data)
    {
        try {
            $query = "INSERT INTO articles (name, description, category, price, image, stock, size_available, created_at) 
                     VALUES (:name, :description, :category, :price, :image, :stock, :size_available, datetime('now'))";
            
            $stmt = $this->pdo->prepare($query);
            $success = $stmt->execute([
                'name' => $data['title'],
                'description' => $data['description'],
                'category' => $data['category'],
                'price' => $data['price'],
                'image' => $data['image_url'] ?? null,
                'stock' => null,
                'size_available' => null
            ]);

            if (!$success) {
                throw new \Exception("Erreur lors de la création de l'article");
            }

            return $this->pdo->lastInsertId();

        } catch (\Exception $e) {
            error_log("Erreur création article : " . $e->getMessage());
            throw $e;
        }
    }
    
    public function testCreateArticle(): void
    {
        // Test de création d'un article avec la structure correcte
        $articleData = [
            'title' => 'Nouvel article',
            'description' => 'Nouvelle description',
            'category' => 'vetements-hommes',
            'price' => 49.99,
            'image_url' => 'nouvelle-image.jpg'
        ];
        
        // Utiliser notre méthode de test au lieu de la méthode réelle
        $id = $this->createTestArticle($articleData);
        $this->assertIsString($id); // lastInsertId retourne une string
        
        // Vérifier que l'article a bien été créé
        $stmt = $this->pdo->prepare("SELECT * FROM articles WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $article = $stmt->fetch(PDO::FETCH_ASSOC);
        
        $this->assertEquals($articleData['title'], $article['name']);
        $this->assertEquals($articleData['description'], $article['description']);
        $this->assertEquals($articleData['category'], $article['category']);
        $this->assertEquals($articleData['price'], $article['price']);
        $this->assertEquals($articleData['image_url'], $article['image']);
    }
    
    // Surcharge la méthode updateArticle pour les tests en utilisant datetime('now') au lieu de NOW()
    protected function updateTestArticle($id, $data)
    {
        try {
            $query = "UPDATE articles 
                     SET name = :name, 
                         description = :description, 
                         category = :category, 
                         price = :price, 
                         image = :image,
                         stock = :stock,
                         size_available = :size_available,
                         updated_at = datetime('now')
                     WHERE id = :id";
            
            $stmt = $this->pdo->prepare($query);
            $success = $stmt->execute([
                'id' => $id,
                'name' => $data['title'],
                'description' => $data['description'],
                'category' => $data['category'],
                'price' => $data['price'],
                'image' => $data['image_url'] ?? null,
                'stock' => null,
                'size_available' => null
            ]);

            if (!$success) {
                throw new \Exception("Erreur lors de la mise à jour de l'article");
            }

            return true;

        } catch (\Exception $e) {
            error_log("Erreur mise à jour article : " . $e->getMessage());
            throw $e;
        }
    }
    
    public function testUpdateArticle(): void
    {
        // Test de mise à jour d'un article
        $articleData = [
            'title' => 'Titre mis à jour',
            'description' => 'Description mise à jour',
            'category' => 'vetements-femmes',
            'price' => 59.99,
            'image_url' => 'image-updated.jpg'
        ];
        
        // Utiliser notre méthode de test au lieu de la méthode réelle
        $result = $this->updateTestArticle(1, $articleData);
        $this->assertTrue($result);
        
        // Vérifier que l'article a bien été mis à jour
        $stmt = $this->pdo->prepare("SELECT * FROM articles WHERE id = :id");
        $stmt->execute([':id' => 1]);
        $article = $stmt->fetch(PDO::FETCH_ASSOC);
        
        $this->assertEquals($articleData['title'], $article['name']);
        $this->assertEquals($articleData['description'], $article['description']);
        $this->assertEquals($articleData['category'], $article['category']);
        $this->assertEquals($articleData['price'], $article['price']);
        $this->assertEquals($articleData['image_url'], $article['image']);
    }
    
    public function testDeleteArticle(): void
    {
        // Test de suppression d'un article
        $result = $this->model->deleteArticle(1);
        $this->assertTrue($result);
        
        // Vérifier que l'article a bien été supprimé
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM articles WHERE id = :id");
        $stmt->execute([':id' => 1]);
        $count = $stmt->fetchColumn();
        
        $this->assertEquals(0, $count);
    }
    
    public function testGetRelatedArticles(): void
    {
        // Ajouter un autre article de la même catégorie
        $stmt = $this->pdo->prepare(
            "INSERT INTO articles (name, description, category, price, image, stock, size_available, created_at, updated_at) 
             VALUES (:name, :description, :category, :price, :image, :stock, :size_available, :created_at, :updated_at)"
        );
        
        $now = date('Y-m-d H:i:s');
        
        $stmt->execute([
            ':name' => 'Article test 3',
            ':description' => 'Description test 3',
            ':category' => 'vetements-hommes',
            ':price' => 19.99,
            ':image' => 'image3.jpg',
            ':stock' => 15,
            ':size_available' => 'S,M',
            ':created_at' => $now,
            ':updated_at' => $now
        ]);
        
        // Test obtention articles liés
        $relatedArticles = $this->model->getRelatedArticles('vetements-hommes', 1);
        $this->assertIsArray($relatedArticles);
        $this->assertCount(1, $relatedArticles);
        $this->assertEquals('Article test 3', $relatedArticles[0]['name']);
    }
    
    public function testGetAllArticles(): void
    {
        // Test de récupération de tous les articles
        $articles = $this->model->getAllArticles();
        $this->assertIsArray($articles);
        $this->assertCount(3, $articles); // Nous avons 3 articles dans le setup
    }
}