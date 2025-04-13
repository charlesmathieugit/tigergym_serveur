<?php
session_start();
require_once __DIR__ . '/../vendor/autoload.php';
use Database\Database;

echo "<h1>Informations de débogage</h1>";

echo "<h2>Variables de session :</h2>";
echo "<pre>";
print_r($_SESSION);
echo "</pre>";

echo "<h2>Utilisateurs dans la base de données :</h2>";
try {
    $db = Database::getInstance();
    $stmt = $db->query("SELECT id, email, username, role FROM users");
    echo "<pre>";
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        print_r($row);
    }
    echo "</pre>";
} catch (Exception $e) {
    echo "Erreur : " . $e->getMessage();
}
?>
