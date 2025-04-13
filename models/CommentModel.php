<?php

namespace Models;

use PDO;

class CommentModel {
    private $db;

    public function __construct(PDO $db) {
        $this->db = $db;
    }

    public function addComment($articleId, $userId, $content) {
        try {
            $stmt = $this->db->prepare("
                INSERT INTO comments (article_id, user_id, content, created_at) 
                VALUES (:article_id, :user_id, :content, NOW())
            ");

            $success = $stmt->execute([
                'article_id' => $articleId,
                'user_id' => $userId,
                'content' => $content
            ]);

            if (!$success) {
                error_log("Erreur SQL lors de l'ajout du commentaire : " . implode(", ", $stmt->errorInfo()));
            }

            return $success;
        } catch (\PDOException $e) {
            error_log("Erreur lors de l'ajout du commentaire : " . $e->getMessage());
            return false;
        }
    }

    public function updateComment($commentId, $userId, $content) {
        try {
            $stmt = $this->db->prepare("
                UPDATE comments 
                SET content = :content, updated_at = NOW()
                WHERE id = :id AND user_id = :user_id
            ");

            $success = $stmt->execute([
                'id' => $commentId,
                'user_id' => $userId,
                'content' => $content
            ]);

            if (!$success) {
                error_log("Erreur SQL lors de la mise à jour du commentaire : " . implode(", ", $stmt->errorInfo()));
            }

            return $success;
        } catch (\PDOException $e) {
            error_log("Erreur lors de la mise à jour du commentaire : " . $e->getMessage());
            return false;
        }
    }

    public function deleteComment($commentId, $userId) {
        try {
            $stmt = $this->db->prepare("
                DELETE FROM comments 
                WHERE id = :id AND user_id = :user_id
            ");

            $success = $stmt->execute([
                'id' => $commentId,
                'user_id' => $userId
            ]);

            if (!$success) {
                error_log("Erreur SQL lors de la suppression du commentaire : " . implode(", ", $stmt->errorInfo()));
            }

            return $success;
        } catch (\PDOException $e) {
            error_log("Erreur lors de la suppression du commentaire : " . $e->getMessage());
            return false;
        }
    }

    public function getArticleComments($articleId) {
        try {
            $stmt = $this->db->prepare("
                SELECT c.*, u.username
                FROM comments c
                JOIN users u ON c.user_id = u.id
                WHERE c.article_id = :article_id
                ORDER BY c.created_at DESC
            ");

            $stmt->execute(['article_id' => $articleId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log("Erreur lors de la récupération des commentaires : " . $e->getMessage());
            return [];
        }
    }

    public function getCommentCount($articleId) {
        try {
            $stmt = $this->db->prepare("
                SELECT COUNT(*) 
                FROM comments 
                WHERE article_id = :article_id
            ");
            $stmt->execute(['article_id' => $articleId]);
            return (int)$stmt->fetchColumn();
        } catch (\PDOException $e) {
            error_log("Erreur lors de la récupération du nombre de commentaires : " . $e->getMessage());
            return 0;
        }
    }

    public function getUserComment($commentId, $userId) {
        try {
            $stmt = $this->db->prepare("
                SELECT * FROM comments 
                WHERE id = :id AND user_id = :user_id
            ");

            $stmt->execute([
                'id' => $commentId,
                'user_id' => $userId
            ]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log("Erreur lors de la récupération du commentaire : " . $e->getMessage());
            return null;
        }
    }
}
