<?php

try {
    $db = new PDO('mysql:host=127.0.0.1;port=3306;dbname=charles_db1', 'root', '');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "Vérification de la table ratings :\n";
    
    // Vérifier si la table existe
    $tables = $db->query("SHOW TABLES LIKE 'ratings'")->fetchAll();
    if (empty($tables)) {
        echo "La table ratings n'existe pas, création...\n";
        $sql = file_get_contents(__DIR__ . '/ratings.sql');
        $db->exec($sql);
        echo "Table ratings créée !\n";
    }
    
    // Afficher la structure
    echo "\nStructure de la table :\n";
    $columns = $db->query("DESCRIBE ratings")->fetchAll(PDO::FETCH_ASSOC);
    foreach ($columns as $col) {
        echo "- {$col['Field']} ({$col['Type']})";
        if ($col['Key'] === 'PRI') echo " [PRIMARY KEY]";
        if ($col['Key'] === 'UNI') echo " [UNIQUE]";
        if ($col['Null'] === 'NO') echo " [NOT NULL]";
        echo "\n";
    }
    
    // Vérifier s'il y a des notes
    $count = $db->query("SELECT COUNT(*) as count FROM ratings")->fetch(PDO::FETCH_ASSOC)['count'];
    echo "\nNombre de notes : $count\n";
    
    // Vérifier les contraintes
    echo "\nContraintes :\n";
    $constraints = $db->query("
        SELECT CONSTRAINT_NAME, COLUMN_NAME, REFERENCED_TABLE_NAME, REFERENCED_COLUMN_NAME
        FROM information_schema.KEY_COLUMN_USAGE
        WHERE TABLE_SCHEMA = 'tigergym'
        AND TABLE_NAME = 'ratings'
        AND REFERENCED_TABLE_NAME IS NOT NULL
    ")->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($constraints as $constraint) {
        echo "- {$constraint['CONSTRAINT_NAME']}: {$constraint['COLUMN_NAME']} -> {$constraint['REFERENCED_TABLE_NAME']}({$constraint['REFERENCED_COLUMN_NAME']})\n";
    }
    
} catch (PDOException $e) {
    die("Erreur : " . $e->getMessage() . "\n");
}
