# Architecture de TigerGym

TigerGym est une application e-commerce de vente d'équipements de fitness suivant une
architecture MVC (Modèle-Vue-Contrôleur).

## Structure du Projet

```

tigergym/
├── assets/ # Ressources statiques (CSS, JS, images)
├── controllers/ # Contrôleurs de l'application
├── models/ # Modèles de données
├── views/ # Templates Twig
│ ├── admin/ # Interface d'administration
│ ├── auth/ # Pages d'authentification
│ └── shop/ # Pages de la boutique
├── middlewares/ # Middlewares (ex: authentification)
├── forms/ # Gestion des formulaires
├── vendor/ # Dépendances (Composer)
└── index.php # Point d'entrée de l'application
```

## Composants Principaux

### 1. Base de Données
- Système: MySQL
- Base: `tigergym`
- Tables principales:

- `products`: Gestion des produits
- Autres tables (users, orders, etc.)

### 2. Contrôleurs
- `HomeController`: Gestion de la page d'accueil et affichage des produits populaires
- `ProductController`: Gestion des produits (CRUD)
- `AuthController`: Gestion de l'authentification
- `ProfileController`: Gestion des profils utilisateurs
- `AdminController`: Interface d'administration

### 3. Modèles
- `Model.php`: Classe de base pour l'interaction avec la base de données
- `Product.php`: Gestion des produits
- `User.php`: Gestion des utilisateurs
- `Review.php`: Gestion des avis
- `Comment.php`: Gestion des commentaires

### 4. Vues (Templates Twig)
- `base.html.twig`: Template de base
- Pages principales:
- `home.html.twig`: Page d'accueil
- `shop.html.twig`: Catalogue de produits
- `product.html.twig`: Détail d'un produit
- `profile.html.twig`: Profil utilisateur

### 5. Middlewares
- `AuthMiddleware.php`: Vérification de l'authentification

## Flux de l'Application

1. L'utilisateur accède à une URL
2. `index.php` initialise l'application
3. Le routeur dirige la requête vers le contrôleur approprié
4. Le contrôleur:
- Traite la requête
- Interagit avec les modèles si nécessaire
- Passe les données au template Twig
5. La vue est rendue et renvoyée à l'utilisateur

## Fonctionnalités Principales

1. **Boutique**
- Catalogue de produits
- Filtrage par catégories
- Détails des produits
- Panier d'achat

2. **Utilisateurs**
- Inscription/Connexion
- Gestion du profil
- Historique des commandes

3. **Administration**
- Gestion des produits
- Gestion des utilisateurs

- Suivi des commandes

## Technologies Utilisées

- PHP 8.x
- MySQL
- Twig (Templates)
- Composer (Gestion des dépendances)
- HTML5/CSS3
- JavaScript