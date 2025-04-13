<?php

namespace Controllers;

use Models\CommentModel;
use PDO;
use Twig\Environment;

class CommentsController extends Controller {
    private $commentModel;

    public function __construct(PDO $db, Environment $twig = null) {
        parent::__construct($db, $twig);
        $this->commentModel = new CommentModel($db);
    }

    public function add() {
        // Vérifier si l'utilisateur est connecté
        if (!isset($_SESSION['user_id'])) {
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => 'Vous devez être connecté pour commenter']);
            return;
        }

        // Vérifier si c'est une requête AJAX
        if (!isset($_SERVER['HTTP_X_REQUESTED_WITH']) || strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) !== 'xmlhttprequest') {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Requête invalide']);
            return;
        }

        $articleId = filter_input(INPUT_POST, 'article_id', FILTER_VALIDATE_INT);
        $content = trim(htmlspecialchars(filter_input(INPUT_POST, 'content'), ENT_QUOTES, 'UTF-8'));

        if (!$articleId || empty($content)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Données invalides']);
            return;
        }

        if ($this->commentModel->addComment($articleId, $_SESSION['user_id'], $content)) {
            $comments = $this->commentModel->getArticleComments($articleId);
            $count = $this->commentModel->getCommentCount($articleId);
            
            echo json_encode([
                'success' => true,
                'message' => 'Commentaire ajouté avec succès',
                'comments' => $comments,
                'count' => $count
            ]);
        } else {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Erreur lors de l\'ajout du commentaire']);
        }
    }

    public function update() {
        // Vérifier si l'utilisateur est connecté
        if (!isset($_SESSION['user_id'])) {
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => 'Vous devez être connecté pour modifier un commentaire']);
            return;
        }

        // Vérifier si c'est une requête AJAX
        if (!isset($_SERVER['HTTP_X_REQUESTED_WITH']) || strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) !== 'xmlhttprequest') {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Requête invalide']);
            return;
        }

        $commentId = filter_input(INPUT_POST, 'comment_id', FILTER_VALIDATE_INT);
        $content = trim(htmlspecialchars(filter_input(INPUT_POST, 'content'), ENT_QUOTES, 'UTF-8'));

        if (!$commentId || empty($content)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Données invalides']);
            return;
        }

        // Vérifier que l'utilisateur est bien l'auteur du commentaire
        $comment = $this->commentModel->getUserComment($commentId, $_SESSION['user_id']);
        if (!$comment) {
            http_response_code(403);
            echo json_encode(['success' => false, 'message' => 'Vous n\'êtes pas autorisé à modifier ce commentaire']);
            return;
        }

        if ($this->commentModel->updateComment($commentId, $_SESSION['user_id'], $content)) {
            $comments = $this->commentModel->getArticleComments($comment['article_id']);
            
            echo json_encode([
                'success' => true,
                'message' => 'Commentaire mis à jour avec succès',
                'comments' => $comments
            ]);
        } else {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Erreur lors de la mise à jour du commentaire']);
        }
    }

    public function delete() {
        // Vérifier si l'utilisateur est connecté
        if (!isset($_SESSION['user_id'])) {
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => 'Vous devez être connecté pour supprimer un commentaire']);
            return;
        }

        // Vérifier si c'est une requête AJAX
        if (!isset($_SERVER['HTTP_X_REQUESTED_WITH']) || strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) !== 'xmlhttprequest') {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Requête invalide']);
            return;
        }

        $commentId = filter_input(INPUT_POST, 'comment_id', FILTER_VALIDATE_INT);

        if (!$commentId) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Données invalides']);
            return;
        }

        // Vérifier que l'utilisateur est bien l'auteur du commentaire
        $comment = $this->commentModel->getUserComment($commentId, $_SESSION['user_id']);
        if (!$comment) {
            http_response_code(403);
            echo json_encode(['success' => false, 'message' => 'Vous n\'êtes pas autorisé à supprimer ce commentaire']);
            return;
        }

        if ($this->commentModel->deleteComment($commentId, $_SESSION['user_id'])) {
            $comments = $this->commentModel->getArticleComments($comment['article_id']);
            $count = $this->commentModel->getCommentCount($comment['article_id']);
            
            echo json_encode([
                'success' => true,
                'message' => 'Commentaire supprimé avec succès',
                'comments' => $comments,
                'count' => $count
            ]);
        } else {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Erreur lors de la suppression du commentaire']);
        }
    }

    public function getArticleCommentData($articleId) {
        $comments = $this->commentModel->getArticleComments($articleId);
        $count = $this->commentModel->getCommentCount($articleId);
        
        error_log("=== Récupération des commentaires pour l'article $articleId ===");
        error_log("Nombre de commentaires: " . $count);
        error_log("Commentaires: " . print_r($comments, true));
        
        // S'assurer que les commentaires sont bien un tableau
        if (!is_array($comments)) {
            error_log("ERREUR: Les commentaires ne sont pas un tableau!");
            $comments = [];
        }
        
        // Formater les dates pour chaque commentaire
        foreach ($comments as &$comment) {
            if (isset($comment['created_at'])) {
                $date = new \DateTime($comment['created_at']);
                $comment['created_at'] = $date->format('Y-m-d H:i:s');
            }
            if (isset($comment['updated_at'])) {
                $date = new \DateTime($comment['updated_at']);
                $comment['updated_at'] = $date->format('Y-m-d H:i:s');
            }
        }
        
        return [
            'comments' => $comments,
            'comment_count' => (int)$count
        ];
    }
}
