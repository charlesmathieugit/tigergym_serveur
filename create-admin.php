<?php
require_once __DIR__ . '/vendor/autoload.php';
use Database\Database;
use Models\UserModel;

try {
    $db = Database::getInstance();
    $userModel = new UserModel($db);

    // Vérifier si l'admin existe déjà
    $admin = $userModel->getUserByEmail('admin@tigergym.fr');
    if ($admin) {
        echo "Un compte admin existe déjà avec l'email admin@tigergym.fr";
    } else {
        // Créer le compte admin
        $result = $userModel->register(
            'admin@tigergym.fr',
            'admin123',
            'Administrateur',
            'admin'
        );

        if ($result) {
            echo "Compte admin créé avec succès !<br>";
            echo "Email : admin@tigergym.fr<br>";
            echo "Mot de passe : admin123";
        } else {
            echo "Erreur lors de la création du compte admin";
        }
    }
} catch (Exception $e) {
    echo "Erreur : " . $e->getMessage();
}
?>
