<?php
namespace Controllers;

use PDO;
use Models\UserModel;
use Twig\Environment;

class AuthController extends Controller {
    private $userModel;

    public function __construct(PDO $db, Environment $twig = null) {
        parent::__construct($db, $twig);
        $this->userModel = new UserModel($db);
    }

    public function showLoginForm() {
        if (isset($_SESSION['user_id'])) {
            $this->redirectBasedOnRole();
            exit;
        }

        echo $this->twig->render('auth/connection.html.twig', [
            'title' => 'Connexion - TigerGym',
            'h1' => 'Connexion',
            'error' => $_SESSION['login_error'] ?? null
        ]);
        unset($_SESSION['login_error']);
    }

    public function login() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
            $password = $_POST['password'] ?? '';

            try {
                $user = $this->userModel->getUserByEmail($email);

                if ($user && password_verify($password, $user['password'])) {
                    // Debug - Afficher les informations de l'utilisateur
                    error_log("=== Connexion réussie ===");
                    error_log("User ID: " . $user['id']);
                    error_log("Username: " . $user['username']);
                    error_log("Role: " . $user['role']);

                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['username'] = $user['username'];
                    $_SESSION['role'] = $user['role'];
                    $_SESSION['is_logged_in'] = true;

                    // Debug - Vérifier les variables de session
                    error_log("=== Variables de session après connexion ===");
                    error_log("Session user_id: " . $_SESSION['user_id']);
                    error_log("Session username: " . $_SESSION['username']);
                    error_log("Session role: " . $_SESSION['role']);
                    error_log("Session is_logged_in: " . $_SESSION['is_logged_in']);

                    // Redirection basée sur le rôle
                    if ($user['role'] === 'admin') {
                        header('Location: /admin/articles');
                    } else {
                        header('Location: /');
                    }
                    exit;
                } else {
                    throw new \Exception('Email ou mot de passe incorrect');
                }
            } catch (\Exception $e) {
                $_SESSION['login_error'] = $e->getMessage();
                header('Location: /connexion');
                exit;
            }
        }

        echo $this->twig->render('auth/connection.html.twig', [
            'error' => $_SESSION['login_error'] ?? null
        ]);
        unset($_SESSION['login_error']);
    }

    private function redirectBasedOnRole() {
        if ($_SESSION['role'] === 'admin') {
            header('Location: /admin/articles');
        } else {
            header('Location: /');
        }
    }

    public function showRegisterForm() {
        echo $this->twig->render('auth/inscription.html.twig', [
            'title' => 'Inscription - TigerGym',
            'h1' => 'Inscription',
            'error' => $_SESSION['error'] ?? null
        ]);
        
        // Effacer le message d'erreur après l'avoir affiché
        unset($_SESSION['error']);
    }

    public function register() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
            $password = $_POST['password'] ?? '';
            $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING);

            error_log("=== Données d'inscription ===");
            error_log("Email: " . $email);
            error_log("Username: " . $username);
            error_log("Password présent: " . (!empty($password) ? 'oui' : 'non'));

            if (empty($email) || empty($password) || empty($username)) {
                $_SESSION['error'] = 'Veuillez remplir tous les champs';
                header('Location: /inscription');
                exit();
            }

            // Vérifier si l'email existe déjà
            if ($this->userModel->getUserByEmail($email)) {
                $_SESSION['error'] = 'Cet email est déjà utilisé';
                header('Location: /inscription');
                exit();
            }

            // Créer l'utilisateur avec le rôle admin si c'est le premier utilisateur
            $isFirstUser = !$this->userModel->hasUsers();
            $role = $isFirstUser ? 'admin' : 'user';

            if ($this->userModel->register($email, $password, $username, $role)) {
                $_SESSION['success'] = 'Inscription réussie ! Vous pouvez maintenant vous connecter.';
                header('Location: /connexion');
                exit();
            } else {
                $_SESSION['error'] = 'Une erreur est survenue lors de l\'inscription';
                header('Location: /inscription');
                exit();
            }
        }
    }

    public function logout() {
        // Supprimer toutes les variables de session
        session_unset();
        
        // Détruire la session
        session_destroy();
        
        // Rediriger vers la page d'accueil
        header('Location: ');
        exit();
    }
}