<?php

namespace Controllers;

use PDO;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use Twig\Extension\DebugExtension;

abstract class Controller
{
    protected $db;
    protected $twig;

    public function __construct(PDO $db, Environment $twig = null)
    {
        $this->db = $db;
        
        if ($twig === null) {
            $loader = new FilesystemLoader(__DIR__ . '/../views');
            $this->twig = new Environment($loader, [
                'debug' => true,
                'cache' => false
            ]);
            $this->twig->addExtension(new DebugExtension());
        } else {
            $this->twig = $twig;
        }
        
        // Debug - Afficher les variables de session
        error_log("=== Variables de session dans Controller ===");
        error_log("Session user_id: " . ($_SESSION['user_id'] ?? 'non défini'));
        error_log("Session username: " . ($_SESSION['username'] ?? 'non défini'));
        error_log("Session role: " . ($_SESSION['role'] ?? 'non défini'));
        
        // Ajouter une variable globale pour l'état de connexion
        $this->twig->addGlobal('user', [
            'is_logged_in' => isset($_SESSION['user_id']),
            'id' => $_SESSION['user_id'] ?? null,
            'email' => $_SESSION['email'] ?? null,
            'username' => $_SESSION['username'] ?? null,
            'role' => $_SESSION['role'] ?? 'user'
        ]);

        // Debug - Afficher les variables globales Twig
        error_log("=== Variables globales Twig ===");
        error_log("User is_logged_in: " . (isset($_SESSION['user_id']) ? 'true' : 'false'));
        error_log("User role: " . ($_SESSION['role'] ?? 'user'));
    }

    protected function render($template, $data = [])
    {
        // Debug - Afficher les variables de session avant le rendu
        error_log("=== Variables de session avant rendu de $template ===");
        error_log("Session user_id: " . ($_SESSION['user_id'] ?? 'non défini'));
        error_log("Session username: " . ($_SESSION['username'] ?? 'non défini'));
        error_log("Session role: " . ($_SESSION['role'] ?? 'non défini'));
        error_log("Session is_logged_in: " . ($_SESSION['is_logged_in'] ?? 'non défini'));

        // Ajouter les variables utilisateur à chaque rendu
        $data['user'] = [
            'is_logged_in' => isset($_SESSION['user_id']),
            'id' => $_SESSION['user_id'] ?? null,
            'username' => $_SESSION['username'] ?? null,
            'role' => $_SESSION['role'] ?? null
        ];

        // Debug - Afficher les variables passées au template
        error_log("=== Variables passées au template ===");
        error_log("User is_logged_in: " . ($data['user']['is_logged_in'] ? 'true' : 'false'));
        error_log("User role: " . ($data['user']['role'] ?? 'non défini'));

        return $this->twig->render($template, $data);
    }
}
