-- --------------------------------------------------------
-- Hôte:                         127.0.0.1
-- Version du serveur:           8.0.30 - MySQL Community Server - GPL
-- SE du serveur:                Win64
-- HeidiSQL Version:             12.1.0.6537
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


-- Listage de la structure de la base pour tigergym
DROP DATABASE IF EXISTS `tigergym`;
CREATE DATABASE IF NOT EXISTS `tigergym` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;
USE `tigergym`;

-- Listage de la structure de table tigergym. articles
DROP TABLE IF EXISTS `articles`;
CREATE TABLE IF NOT EXISTS `articles` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `category` varchar(50) NOT NULL,
  `stock` int DEFAULT NULL,
  `size_available` varchar(50) DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Listage des données de la table tigergym.articles : ~12 rows (environ)
DELETE FROM `articles`;
INSERT INTO `articles` (`id`, `name`, `description`, `price`, `image`, `category`, `stock`, `size_available`, `created_at`, `updated_at`) VALUES
	(1, 'T-Shirt Performance', 'T-shirt technique respirant pour l\'entraînement', 29.99, '/tigergym/assets/images/products/t shirt.png', 'vetements-hommes', 50, 'S,M,L,XL', '2025-03-22 09:22:26', '2025-03-22 09:22:26'),
	(2, 'Short Training', 'Short léger et confortable pour la musculation', 24.99, '/assets/images/products/t shirt.png', 'vetements-hommes', 40, 'S,M,L,XL', '2025-03-22 09:22:26', '2025-03-22 09:22:26'),
	(3, 'Débardeur Muscle', 'Débardeur pour la musculation et le cardio', 19.99, '/assets/images/products/t shirt.png', 'vetements-hommes', 30, 'S,M,L,XL', '2025-03-22 09:22:26', '2025-03-22 09:22:26'),
	(4, 'Legging Fitness', 'Legging haute performance avec compression', 39.99, '/assets/images/products/t shirt.png', 'vetements-femmes', 45, 'S,M,L', '2025-03-22 09:22:26', '2025-03-22 09:22:26'),
	(5, 'Brassière Sport Pro', 'Brassière maintien maximum pour tous types d\'activités', 34.99, '/assets/images/products/t shirt.png', 'vetements-femmes', 35, 'S,M,L', '2025-03-22 09:22:26', '2025-03-22 09:22:26'),
	(6, 'Top Training', 'Top respirant pour le fitness et le yoga', 29.99, '/assets/images/products/t shirt.png', 'vetements-femmes', 40, 'S,M,L', '2025-03-22 09:22:26', '2025-03-22 09:22:26'),
	(7, 'Tapis de Course Pro', 'Tapis de course professionnel avec inclinaison automatique', 999.99, '/tigergym/assets/images/products/haltere.jpg', 'machines', 5, NULL, '2025-03-22 09:22:26', '2025-03-22 09:22:26'),
	(8, 'Vélo Elliptique Elite', 'Vélo elliptique silencieux avec 12 programmes', 799.99, '/tigergym/assets/images/products/haltere.jpg', 'machines', 3, NULL, '2025-03-22 09:22:26', '2025-03-22 09:22:26'),
	(9, 'Rameur Performance', 'Rameur professionnel avec résistance magnétique', 699.99, '/tigergym/assets/images/products/haltere.jpg', 'machines', 4, NULL, '2025-03-22 09:22:26', '2025-03-22 09:22:26'),
	(10, 'Whey Protein Gold', 'Protéine premium saveur vanille - 2kg', 39.99, '/tigergym/assets/images/products/gelule.png', 'complements', 100, NULL, '2025-03-22 09:22:26', '2025-03-22 09:22:26'),
	(11, 'BCAA 2:1:1', 'Acides aminés essentiels - 100 gélules', 29.99, '/tigergym/assets/images/products/gelule.png', 'complements', 80, NULL, '2025-03-22 09:22:26', '2025-03-22 09:22:26'),
	(12, 'Pre-Workout Boost', 'Booster d\'énergie saveur fruits rouges - 300g', 34.99, '/tigergym/assets/images/products/gelule.png', 'complements', 60, NULL, '2025-03-22 09:22:26', '2025-03-22 09:22:26');

-- Listage de la structure de table tigergym. comments
DROP TABLE IF EXISTS `comments`;
CREATE TABLE IF NOT EXISTS `comments` (
  `id` int NOT NULL AUTO_INCREMENT,
  `article_id` int NOT NULL,
  `user_id` int NOT NULL,
  `content` text NOT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `article_id` (`article_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `comments_ibfk_1` FOREIGN KEY (`article_id`) REFERENCES `articles` (`id`) ON DELETE CASCADE,
  CONSTRAINT `comments_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Listage des données de la table tigergym.comments : ~0 rows (environ)
DELETE FROM `comments`;

-- Listage de la structure de table tigergym. ratings
DROP TABLE IF EXISTS `ratings`;
CREATE TABLE IF NOT EXISTS `ratings` (
  `id` int NOT NULL AUTO_INCREMENT,
  `article_id` int NOT NULL,
  `user_id` int NOT NULL,
  `rating` tinyint NOT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `article_id` (`article_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `ratings_ibfk_1` FOREIGN KEY (`article_id`) REFERENCES `articles` (`id`) ON DELETE CASCADE,
  CONSTRAINT `ratings_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `ratings_rating_check` CHECK ((`rating` between 1 and 5))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Listage des données de la table tigergym.ratings : ~0 rows (environ)
DELETE FROM `ratings`;

-- Listage de la structure de table tigergym. users
DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nom` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `prenom` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` enum('user','admin') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'user',
  `created_at` datetime NOT NULL,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
-- Listage des données de la table tigergym.users : ~0 rows (environ)
DELETE FROM `users`;

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
