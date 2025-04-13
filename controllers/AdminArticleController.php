<?php

namespace Controllers;

use PDO;
use Models\ArticleModel;
use Twig\Environment;

class AdminArticleController extends Controller {
    private $articleModel;

    public function __construct(PDO $db, Environment $twig = null) {
        parent::__construct($db, $twig);
        $this->articleModel = new ArticleModel($db);
    }

    private function checkAdminAccess() {
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
            header('Location: /connexion');
            exit;
        }
    }

    public function index() {
        $this->checkAdminAccess();
        
        $articles = $this->articleModel->getAllArticles();
        
        echo $this->twig->render('admin/articles.html.twig', [
            'articles' => $articles,
            'success' => $_SESSION['admin_success'] ?? null,
            'error' => $_SESSION['admin_error'] ?? null
        ]);

        unset($_SESSION['admin_success'], $_SESSION['admin_error']);
    }

    public function showCreateForm() {
        $this->checkAdminAccess();
        
        echo $this->twig->render('admin/article-form.html.twig', [
            'error' => $_SESSION['admin_error'] ?? null
        ]);

        unset($_SESSION['admin_error']);
    }

    public function create() {
        $this->checkAdminAccess();

        try {
            $data = $this->validateArticleData($_POST);
            
            $articleId = $this->articleModel->createArticle($data);
            
            if ($articleId) {
                $_SESSION['admin_success'] = "L'article a été créé avec succès";
                header('Location: /admin/articles');
                exit;
            } else {
                throw new \Exception("Une erreur est survenue lors de la création de l'article");
            }

        } catch (\Exception $e) {
            $_SESSION['admin_error'] = $e->getMessage();
            header('Location: /admin/articles/nouveau');
            exit;
        }
    }

    public function showEditForm($id) {
        $this->checkAdminAccess();
        
        $article = $this->articleModel->getArticleById($id);
        
        if (!$article) {
            $_SESSION['admin_error'] = "Article non trouvé";
            header('Location: /admin/articles');
            exit;
        }

        echo $this->twig->render('admin/article-form.html.twig', [
            'article' => $article,
            'error' => $_SESSION['admin_error'] ?? null
        ]);

        unset($_SESSION['admin_error']);
    }

    public function update($id) {
        $this->checkAdminAccess();

        try {
            $article = $this->articleModel->getArticleById($id);
            
            if (!$article) {
                throw new \Exception("Article non trouvé");
            }

            $data = $this->validateArticleData($_POST);
            
            if ($this->articleModel->updateArticle($id, $data)) {
                $_SESSION['admin_success'] = "L'article a été mis à jour avec succès";
                header('Location: /admin/articles');
                exit;
            } else {
                throw new \Exception("Une erreur est survenue lors de la mise à jour de l'article");
            }

        } catch (\Exception $e) {
            $_SESSION['admin_error'] = $e->getMessage();
            header('Location: /admin/articles/modifier/' . $id);
            exit;
        }
    }

    public function delete($id) {
        $this->checkAdminAccess();

        try {
            $article = $this->articleModel->getArticleById($id);
            
            if (!$article) {
                throw new \Exception("Article non trouvé");
            }

            if ($this->articleModel->deleteArticle($id)) {
                $_SESSION['admin_success'] = "L'article a été supprimé avec succès";
            } else {
                throw new \Exception("Une erreur est survenue lors de la suppression de l'article");
            }

        } catch (\Exception $e) {
            $_SESSION['admin_error'] = $e->getMessage();
        }

        header('Location: /admin/articles');
        exit;
    }

    private function validateArticleData($data) {
        $required = ['title', 'description', 'category', 'price'];
        $validated = [];

        // Valider les champs requis
        foreach ($required as $field) {
            if (!isset($data[$field]) || empty(trim($data[$field]))) {
                throw new \Exception("Le champ '$field' est requis");
            }
            $validated[$field] = trim($data[$field]);
        }

        // Validation du prix
        if (!is_numeric($validated['price']) || $validated['price'] < 0) {
            throw new \Exception("Le prix doit être un nombre positif");
        }

        // Validation des URLs optionnelles
        if (!empty($data['image_url'])) {
            if (!filter_var($data['image_url'], FILTER_VALIDATE_URL)) {
                throw new \Exception("L'URL de l'image n'est pas valide");
            }
            $validated['image_url'] = trim($data['image_url']);
        } else {
            $validated['image_url'] = null;
        }

        if (!empty($data['external_link'])) {
            if (!filter_var($data['external_link'], FILTER_VALIDATE_URL)) {
                throw new \Exception("Le lien externe n'est pas valide");
            }
            $validated['external_link'] = trim($data['external_link']);
        } else {
            $validated['external_link'] = null;
        }

        // Validation de la catégorie
        $validCategories = ['machines', 'vetements-hommes', 'vetements-femmes', 'complements'];
        if (!in_array($validated['category'], $validCategories)) {
            throw new \Exception("La catégorie n'est pas valide");
        }

        return $validated;
    }
}
