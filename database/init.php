<?php

try {
    $db = new PDO('mysql:host=127.0.0.1;port=3306;charset=utf8', 'root', '');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Créer la base de données si elle n'existe pas
    $db->exec("CREATE DATABASE IF NOT EXISTS tigergym");
    $db->exec("USE tigergym");
    
    echo "Base de données tigergym créée ou sélectionnée\n";
    
    // Lire et exécuter les fichiers SQL
    $sqlFiles = ['base.sql', 'users.sql', 'comments.sql', 'ratings.sql'];
    
    foreach ($sqlFiles as $file) {
        if (file_exists(__DIR__ . '/' . $file)) {
            $sql = file_get_contents(__DIR__ . '/' . $file);
            echo "Exécution de $file...\n";
            $db->exec($sql);
            echo "OK\n";
        } else {
            echo "ATTENTION: Le fichier $file n'existe pas\n";
        }
    }
    
    // Vérifier que les tables ont été créées
    $tables = $db->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
    echo "\nTables créées :\n";
    foreach ($tables as $table) {
        echo "- $table\n";
        // Afficher la structure de la table
        $columns = $db->query("DESCRIBE $table")->fetchAll(PDO::FETCH_ASSOC);
        foreach ($columns as $col) {
            echo "  * {$col['Field']} ({$col['Type']})\n";
        }
    }
    
} catch (PDOException $e) {
    die("Erreur : " . $e->getMessage() . "\n");
}
