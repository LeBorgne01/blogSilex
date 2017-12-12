<?php

namespace DUT\Controllers;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Silex\Application;
use DUT\Services\SessionStorage;
use DUT\Models\Article;

use DUT\Models\Article;
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

    public function createAction(Request $request, Application $app) {
        $name = $request->get('name', null);
        $url = $app['url_generator']->generate('home');

        if (!is_null($name)) {
            $this->storage->addElement($name);

            return $app->redirect($url);
        }

        $html = '<h2>Ajouter</h2><form action="create" method="post">';
        $html .= '<label for="input">Nom</label><input id="input" type="text" name="name">';
        $html .= '<button>Valider</button></form>';

        return new Response($html);
    }

    public function deleteAction($index, Application $app) {
        $this->storage->removeElement($index);

        $url = $app['url_generator']->generate('home');

        return $app->redirect($url);
    }

    public function afficheAdminPage(Application $app){
         $entityManager = $app['em'];
        $items = $entityManager->getRepository('DUT\\Models\\Article');
        $resultat = $items->findAll();

          return $app['twig']->render('admin.twig', ['articles' => $resultat]);

    }
}
