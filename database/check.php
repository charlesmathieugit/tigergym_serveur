<?php

try {
    $db = new PDO('mysql:host=127.0.0.1;port=3306;dbname=tigergym', 'root', '');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "Vérification de la table users :\n";
    
    // Vérifier si la table existe
    $tables = $db->query("SHOW TABLES LIKE 'users'")->fetchAll();
    if (empty($tables)) {
        die("La table users n'existe pas !\n");
    }
    
    // Afficher la structure
    echo "\nStructure de la table :\n";
    $columns = $db->query("DESCRIBE users")->fetchAll(PDO::FETCH_ASSOC);
    foreach ($columns as $col) {
        echo "- {$col['Field']} ({$col['Type']})";
        if ($col['Key'] === 'PRI') echo " [PRIMARY KEY]";
        if ($col['Key'] === 'UNI') echo " [UNIQUE]";
        if ($col['Null'] === 'NO') echo " [NOT NULL]";
        echo "\n";
    }
    
    // Vérifier s'il y a des utilisateurs
    $count = $db->query("SELECT COUNT(*) as count FROM users")->fetch(PDO::FETCH_ASSOC)['count'];
    echo "\nNombre d'utilisateurs : $count\n";
    
} catch (PDOException $e) {
    die("Erreur : " . $e->getMessage() . "\n");
}
