<?php

namespace Models;

use PDO;

class RatingModel {
    private $db;

    public function __construct(PDO $db) {
        $this->db = $db;
    }

    public function rateArticle($articleId, $userId, $rating) {
        try {
            // Vérifier si l'utilisateur a déjà noté cet article
            $stmt = $this->db->prepare("SELECT id FROM ratings WHERE article_id = :article_id AND user_id = :user_id");
            $stmt->execute(['article_id' => $articleId, 'user_id' => $userId]);
            $existingRating = $stmt->fetch();

            if ($existingRating) {
                // Mettre à jour la note existante
                $stmt = $this->db->prepare("
                    UPDATE ratings 
                    SET rating = :rating, updated_at = NOW() 
                    WHERE article_id = :article_id AND user_id = :user_id
                ");
            } else {
                // Créer une nouvelle note
                $stmt = $this->db->prepare("
                    INSERT INTO ratings (article_id, user_id, rating, created_at) 
                    VALUES (:article_id, :user_id, :rating, NOW())
                ");
            }

            $success = $stmt->execute([
                'article_id' => $articleId,
                'user_id' => $userId,
                'rating' => $rating
            ]);

            if (!$success) {
                error_log("Erreur SQL lors de la notation : " . implode(", ", $stmt->errorInfo()));
            }

            return $success;
        } catch (\PDOException $e) {
            error_log("Erreur lors de la notation : " . $e->getMessage());
            return false;
        }
    }

    public function getArticleRating($articleId) {
        try {
            $stmt = $this->db->prepare("
                SELECT ROUND(AVG(rating), 1) as average
                FROM ratings 
                WHERE article_id = :article_id
            ");
            $stmt->execute(['article_id' => $articleId]);
            return $stmt->fetchColumn() ?: 0;
        } catch (\PDOException $e) {
            error_log("Erreur lors de la récupération de la note moyenne : " . $e->getMessage());
            return 0;
        }
    }

    public function getArticleRatingCount($articleId) {
        try {
            $stmt = $this->db->prepare("
                SELECT COUNT(*) 
                FROM ratings 
                WHERE article_id = :article_id
            ");
            $stmt->execute(['article_id' => $articleId]);
            return (int)$stmt->fetchColumn();
        } catch (\PDOException $e) {
            error_log("Erreur lors de la récupération du nombre de notes : " . $e->getMessage());
            return 0;
        }
    }

    public function getUserRating($articleId, $userId) {
        if (!$userId) return null;
        
        try {
            $stmt = $this->db->prepare("
                SELECT rating 
                FROM ratings 
                WHERE article_id = :article_id AND user_id = :user_id
            ");
            $stmt->execute([
                'article_id' => $articleId,
                'user_id' => $userId
            ]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result ? (int)$result['rating'] : null;
        } catch (\PDOException $e) {
            error_log("Erreur lors de la récupération de la note utilisateur : " . $e->getMessage());
            return null;
        }
    }
}