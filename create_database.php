<?php

try {
    $db = new PDO('mysql:host=localhost;charset=utf8', 'root', '');
    
    // Supprimer la base de données si elle existe
    $db->exec('DROP DATABASE IF EXISTS tigergym');
    
    // Créer la base de données
    $db->exec('CREATE DATABASE tigergym');
    $db->exec('USE tigergym');
    
    // Créer la table users
    $db->exec('CREATE TABLE users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        email VARCHAR(255) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        firstname VARCHAR(100) NOT NULL,
        lastname VARCHAR(100) NOT NULL,
        address TEXT,
        phone VARCHAR(20),
        role ENUM("user", "admin") NOT NULL DEFAULT "user",
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP
    )');

    // Insérer les utilisateurs de test
    $db->exec("INSERT INTO users (id, email, password, firstname, lastname, role, created_at) VALUES
        (1, 'admin@tigergym.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Admin', 'TigerGym', 'admin', '2025-03-22 09:22:26'),
        (2, 'login@test.com', '$2y$10$yP4Cv9acKMeEyH8yu2yKU.9gaqn8eI7n0fD4OZuFIBgCpSTXPL8qK', 'Charles', 'Mathieu', 'user', '2025-03-30 17:29:49')
    ");
    
    // Créer la table articles
    $db->exec('CREATE TABLE articles (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        description TEXT NOT NULL,
        price DECIMAL(10,2) NOT NULL,
        image VARCHAR(255),
        category VARCHAR(50) NOT NULL,
        stock INT,
        size_available VARCHAR(50),
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )');

    // Créer la table comments
    $db->exec('CREATE TABLE comments (
        id INT AUTO_INCREMENT PRIMARY KEY,
        article_id INT NOT NULL,
        user_id INT NOT NULL,
        content TEXT NOT NULL,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        updated_at DATETIME DEFAULT NULL,
        FOREIGN KEY (article_id) REFERENCES articles(id) ON DELETE CASCADE,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    )');

    // Créer la table ratings
    $db->exec('CREATE TABLE ratings (
        id INT AUTO_INCREMENT PRIMARY KEY,
        article_id INT NOT NULL,
        user_id INT NOT NULL,
        rating TINYINT NOT NULL,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        updated_at DATETIME DEFAULT NULL,
        FOREIGN KEY (article_id) REFERENCES articles(id) ON DELETE CASCADE,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
        CONSTRAINT ratings_rating_check CHECK (rating BETWEEN 1 AND 5)
    )');

    // Insérer les données de test
    $db->exec("INSERT INTO articles (id, name, description, price, image, category, stock, size_available, created_at, updated_at) VALUES
        (1, 'T-Shirt Performance', 'T-shirt technique respirant pour l\'entraînement', 29.99, '/tigergym/assets/images/tshirt.svg', 'vetements-hommes', 50, 'S,M,L,XL', '2025-03-22 09:22:26', '2025-03-22 09:22:26'),
        (2, 'Short Training', 'Short léger et confortable pour la musculation', 24.99, '/tigergym/assets/images/tshirt.svg', 'vetements-hommes', 40, 'S,M,L,XL', '2025-03-22 09:22:26', '2025-03-22 09:22:26'),
        (3, 'Débardeur Muscle', 'Débardeur pour la musculation et le cardio', 19.99, '/tigergym/assets/images/tshirt.svg', 'vetements-hommes', 30, 'S,M,L,XL', '2025-03-22 09:22:26', '2025-03-22 09:22:26'),
        (4, 'Legging Fitness', 'Legging haute performance avec compression', 39.99, '/tigergym/assets/images/tshirt.svg', 'vetements-femmes', 45, 'S,M,L', '2025-03-22 09:22:26', '2025-03-22 09:22:26'),
        (5, 'Brassière Sport Pro', 'Brassière maintien maximum pour tous types d\'activités', 34.99, '/tigergym/assets/images/tshirt.svg', 'vetements-femmes', 35, 'S,M,L', '2025-03-22 09:22:26', '2025-03-22 09:22:26'),
        (6, 'Top Training', 'Top respirant pour le fitness et le yoga', 29.99, '/tigergym/assets/images/tshirt.svg', 'vetements-femmes', 40, 'S,M,L', '2025-03-22 09:22:26', '2025-03-22 09:22:26'),
        (7, 'Tapis de Course Pro', 'Tapis de course professionnel avec inclinaison automatique', 999.99, '/tigergym/assets/images/haltere.svg', 'machines', 5, NULL, '2025-03-22 09:22:26', '2025-03-22 09:22:26'),
        (8, 'Vélo Elliptique Elite', 'Vélo elliptique silencieux avec 12 programmes', 799.99, '/tigergym/assets/images/haltere.svg', 'machines', 3, NULL, '2025-03-22 09:22:26', '2025-03-22 09:22:26'),
        (9, 'Rameur Performance', 'Rameur professionnel avec résistance magnétique', 699.99, '/tigergym/assets/images/haltere.svg', 'machines', 4, NULL, '2025-03-22 09:22:26', '2025-03-22 09:22:26'),
        (10, 'Whey Protein Gold', 'Protéine premium saveur vanille - 2kg', 39.99, '/tigergym/assets/images/gelule.svg', 'complements', 100, NULL, '2025-03-22 09:22:26', '2025-03-22 09:22:26'),
        (11, 'BCAA 2:1:1', 'Acides aminés essentiels - 100 gélules', 29.99, '/tigergym/assets/images/gelule.svg', 'complements', 80, NULL, '2025-03-22 09:22:26', '2025-03-22 09:22:26'),
        (12, 'Pre-Workout Boost', 'Booster d\'énergie saveur fruits rouges - 300g', 34.99, '/tigergym/assets/images/gelule.svg', 'complements', 60, NULL, '2025-03-22 09:22:26', '2025-03-22 09:22:26')
    ");
    
    echo "Base de données et table créées avec succès!\n";
    echo "Données de test insérées avec succès!\n";
    
} catch (PDOException $e) {
    die("Erreur : " . $e->getMessage() . "\n");
}
