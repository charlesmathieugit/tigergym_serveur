    <?php
session_start();
require 'vendor/autoload.php';

use Controllers\HomeController;
use Controllers\AuthController;
use Controllers\ArticleController;
use Controllers\UserController;
use Controllers\RatingController;
use Controllers\CommentsController;
use Controllers\AdminArticleController;
use Controllers\AdminDashboardController;
use Controllers\ContactController; // Ajout de l'import du ContactController
use Database\Database;
use Middlewares\AuthMiddleware;

// Configuration de Twig
$loader = new \Twig\Loader\FilesystemLoader(__DIR__ . '/views');
$twig = new \Twig\Environment($loader, [
    'cache' => false,
    'debug' => true
]);

// Créer une instance du routeur
$router = new AltoRouter();

// Définir le chemin de base
$router->setBasePath('');

// Définir les routes
$router->map('GET', '/', function () use ($twig) {
    $db = Database::getInstance();
    $homeController = new HomeController($db, $twig);
    $homeController->index();
});

// Routes d'authentification
$router->map('GET', '/connexion', function () use ($twig) {
    $db = Database::getInstance();
    $authController = new AuthController($db, $twig);
    $authController->showLoginForm();
});

$router->map('POST', '/connexion', function () use ($twig) {
    $db = Database::getInstance();
    $authController = new AuthController($db, $twig);
    $authController->login();
});

$router->map('GET', '/inscription', function () use ($twig) {
    $db = Database::getInstance();
    $authController = new AuthController($db, $twig);
    $authController->showRegisterForm();
});

$router->map('POST', '/inscription', function () use ($twig) {
    $db = Database::getInstance();
    $authController = new AuthController($db, $twig);
    $authController->register();
});

$router->map('GET', '/deconnexion', function () {
    $db = Database::getInstance();
    $authController = new AuthController($db);
    $authController->logout();
});

// Routes des articles
$router->map('GET', '/article/[i:id]', function ($id) use ($twig) {
    $db = Database::getInstance();
    $articleController = new ArticleController($db, $twig);
    $articleController->show($id);
});

$router->map('GET', '/categorie/[*:category]', function ($category) use ($twig) {
    $db = Database::getInstance();
    $articleController = new ArticleController($db, $twig);
    $articleController->category($category);
});

// route Admin
$router->map('GET', '/admin', function () use ($twig) {
    AuthMiddleware::auth();
    $db = Database::getInstance();
    $userController = new UserController($db, $twig);
    $userController->admin();
});

// Route du tableau de bord admin
$router->map('GET', '/admin/dashboard', function () use ($twig) {
    $db = Database::getInstance();
    $adminDashboardController = new AdminDashboardController($db, $twig);
    $adminDashboardController->index();
});

// Routes pour les catégories
$db = Database::getInstance();
$articleController = new ArticleController($db, $twig);

$router->map('GET', '/machines', function() use ($articleController) {
    $articleController->category('machines');
});

$router->map('GET', '/vetements', function() use ($articleController) {
    $articleController->category('vetements');
});

$router->map('GET', '/vetements-hommes', function() use ($articleController) {
    $articleController->category('vetements-hommes');
});

$router->map('GET', '/vetements-femmes', function() use ($articleController) {
    $articleController->category('vetements-femmes');
});

$router->map('GET', '/complements', function() use ($articleController) {
    $articleController->category('complements');
});

$router->map('GET', '/categories/[*:category]', function($category) use ($twig) {
    $db = Database::getInstance();
    $controller = new ArticleController($db, $twig);
    $controller->category($category);
});

// Routes pour les commentaires
$router->map('POST', '/comments/add', function() use ($db, $twig) {
    $controller = new CommentsController($db, $twig);
    $controller->add();
});

$router->map('POST', '/comments/update', function() use ($db, $twig) {
    $controller = new CommentsController($db, $twig);
    $controller->update();
});

$router->map('POST', '/comments/delete', function() use ($db, $twig) {
    $controller = new CommentsController($db, $twig);
    $controller->delete();
});

// Route pour la notation
$router->map('POST', '/ratings/rate', function () use ($twig) {
    $db = Database::getInstance();
    $ratingController = new RatingController($db, $twig);
    $ratingController->rate();
});

// Routes d'administration des articles
$router->map('GET', '/admin/articles', function () use ($twig) {
    $db = Database::getInstance();
    $adminArticleController = new AdminArticleController($db, $twig);
    $adminArticleController->index();
});

$router->map('GET', '/admin/articles/nouveau', function() use ($twig) {
    $db = Database::getInstance();
    $controller = new AdminArticleController($db, $twig);
    $controller->showCreateForm();
});

$router->map('POST', '/admin/articles/nouveau', function() use ($twig) {
    $db = Database::getInstance();
    $controller = new AdminArticleController($db, $twig);
    $controller->create();
});

$router->map('GET', '/admin/articles/modifier/[i:id]', function($id) use ($twig) {
    $db = Database::getInstance();
    $controller = new AdminArticleController($db, $twig);
    $controller->showEditForm($id);
});

$router->map('POST', '/admin/articles/modifier/[i:id]', function($id) use ($twig) {
    $db = Database::getInstance();
    $controller = new AdminArticleController($db, $twig);
    $controller->update($id);
});

$router->map('POST', '/admin/articles/supprimer/[i:id]', function($id) use ($twig) {
    $db = Database::getInstance();
    $controller = new AdminArticleController($db, $twig);
    $controller->delete($id);
});

// Matcher la route actuelle
$match = $router->match();

// Appeler la fonction de rappel correspondante
if ($match && is_callable($match['target'])) {
    call_user_func_array($match['target'], $match['params']);
} else {
    header($_SERVER['SERVER_PROTOCOL'] . ' 404 Not Found');
    echo '404 Page Not Found';
}