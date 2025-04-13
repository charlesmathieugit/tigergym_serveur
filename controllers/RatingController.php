<?php

namespace Controllers;

use Models\RatingModel;
use PDO;
use Twig\Environment;

class RatingController extends Controller {
    private $ratingModel;

    public function __construct(PDO $db, Environment $twig = null) {
        parent::__construct($db, $twig);
        $this->ratingModel = new RatingModel($db);
    }

    // Gérer la soumission d'une note via AJAX
    public function rate() {
        error_log("=== Début de la notation ===");
        
        // Vérifier si l'utilisateur est connecté
        if (!isset($_SESSION['user_id'])) {
            error_log("Utilisateur non connecté");
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => 'Vous devez être connecté pour noter']);
            return;
        }
        error_log("Utilisateur connecté : " . $_SESSION['user_id']);

        // Vérifier si c'est une requête AJAX
        if (!isset($_SERVER['HTTP_X_REQUESTED_WITH']) || strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) !== 'xmlhttprequest') {
            error_log("Requête non-AJAX");
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Requête invalide']);
            return;
        }
        error_log("Requête AJAX valide");

        // Vérifier les données reçues
        error_log("POST data : " . print_r($_POST, true));
        $articleId = filter_input(INPUT_POST, 'article_id', FILTER_VALIDATE_INT);
        $rating = filter_input(INPUT_POST, 'rating', FILTER_VALIDATE_INT);
        error_log("Article ID : " . ($articleId ?: 'null'));
        error_log("Note : " . ($rating ?: 'null'));

        if (!$articleId || !$rating || $rating < 1 || $rating > 5) {
            error_log("Données invalides");
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Données invalides']);
            return;
        }

        // Enregistrer la note
        if ($this->ratingModel->rateArticle($articleId, $_SESSION['user_id'], $rating)) {
            // Récupérer les données mises à jour
            $average = $this->ratingModel->getArticleRating($articleId);
            $count = $this->ratingModel->getArticleRatingCount($articleId);
            $userRating = $this->ratingModel->getUserRating($articleId, $_SESSION['user_id']);
            
            error_log("Note enregistrée avec succès");
            error_log("Nouvelle moyenne : " . $average);
            error_log("Nombre total : " . $count);
            error_log("Note utilisateur : " . $userRating);
            
            echo json_encode([
                'success' => true,
                'message' => 'Note enregistrée avec succès',
                'average_rating' => round($average, 1),
                'rating_count' => $count,
                'user_rating' => $userRating
            ]);
        } else {
            error_log("Erreur lors de l'enregistrement de la note");
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Erreur lors de l\'enregistrement de la note']);
            error_log('Erreur lors de l\'enregistrement de la note');
        }
    }

    // Obtenir les données de notation pour un article
    public function getArticleRatingData($articleId) {
        $average = $this->ratingModel->getArticleRating($articleId);
        $count = $this->ratingModel->getArticleRatingCount($articleId);
        $userRating = isset($_SESSION['user_id']) ? $this->ratingModel->getUserRating($articleId, $_SESSION['user_id']) : null;
        
        error_log("=== Récupération des notes pour l'article $articleId ===");
        error_log("Note moyenne: " . $average);
        error_log("Nombre de notes: " . $count);
        error_log("Note de l'utilisateur: " . ($userRating ?? 'non noté'));
        
        return [
            'average_rating' => round((float)$average, 1),
            'rating_count' => (int)$count,
            'user_rating' => $userRating ? (int)$userRating : null
        ];
    }
}