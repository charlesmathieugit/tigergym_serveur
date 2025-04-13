<?php

namespace App\Middlewares;

class AuthMiddleware {
    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public function isAuthenticated(): bool {
        return isset($_SESSION['user_id']);
    }

    public function isAdmin(): bool {
        return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
    }

    public function requireAuth() {
        if (!$this->isAuthenticated()) {
            header('Location: /connexion');
            exit();
        }
    }

    public function requireAdmin() {
        if (!$this->isAdmin()) {
            header('Location: /tigergym/');
            exit();
        }
    }

    public function getUserId() {
        return $_SESSION['user_id'] ?? null;
    }

    public function getUserRole() {
        return $_SESSION['user_role'] ?? null;
    }
}
