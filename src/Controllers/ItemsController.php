<?php

namespace DUT\Controllers;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Silex\Application;
use DUT\Services\SessionStorage;



use DUT\Models\Commentaire;

class ItemsController {

    protected $storage;
    protected $entityManager;

    public function __construct() {
        $this->storage = new SessionStorage();
    }

    public function afficheHomePage(Application $app) {
        $entityManager = $app['em'];
        $research = $entityManager->getRepository('DUT\\Models\\Article');
        $articles = $research->findAll();

        return $app['twig']->render('home.twig', ['articles' => $articles]);
    }

    public function afficheAdminPage(Application $app){
         $entityManager = $app['em'];
        $items = $entityManager->getRepository('DUT\\Models\\Article');
        $resultat = $items->findAll();

          return $app['twig']->render('admin.twig', ['articles' => $resultat]);

    }

    public function afficheArticlePage(Application $app){
        
        return $app['twig']->render('article.twig');
    }
}
