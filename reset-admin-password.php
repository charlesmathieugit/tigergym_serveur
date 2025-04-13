<?php
require_once __DIR__ . '/vendor/autoload.php';
use Database\Database;

try {
    $db = Database::getInstance();
    $newPassword = 'admin123';
    $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
    
    $stmt = $db->prepare("UPDATE users SET password = :password WHERE email = 'admin@tigergym.fr'");
    $result = $stmt->execute(['password' => $hashedPassword]);
    
    if ($result) {
        echo "Le mot de passe du compte admin a été réinitialisé avec succès !<br>";
        echo "Email : admin@tigergym.fr<br>";
        echo "Nouveau mot de passe : admin123";
    } else {
        echo "Erreur lors de la réinitialisation du mot de passe";
    }
} catch (Exception $e) {
    echo "Erreur : " . $e->getMessage();
}
?>
