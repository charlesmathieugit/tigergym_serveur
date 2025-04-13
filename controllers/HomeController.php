<?php

namespace Controllers;

use Models\ArticleModel;

class HomeController {
    private $db;
    private $twig;
    private $articleModel;

    public function __construct($db, $twig) {
        $this->db = $db;
        $this->twig = $twig;
        $this->articleModel = new ArticleModel($db);
    }

    public function index() {
        $featuredArticles = $this->articleModel->getFeaturedArticles();
        $latestArticles = $this->articleModel->getLatestArticles();
        
        echo $this->twig->render('home.html.twig', [
            'featuredArticles' => $featuredArticles,
            'latestArticles' => $latestArticles,
            'pageTitle' => 'TigerGym - Votre guide du matériel sportif',
            'app' => [
                'request' => [
                    'pathInfo' => '/'
                ]
            ]
        ]);
    }
}
