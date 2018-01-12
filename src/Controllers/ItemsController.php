<?php

namespace DUT\Controllers;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Silex\Application;
use DUT\Services\SessionStorage;



use DUT\Models\Commentaire;
use DUT\Models\Article;

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

     public function deleteAction($idArticle, Application $app) {
       // $this->storage->removeElement($index);
        $em = $app['em'];
        $itemToRemove = $em->find('DUT\\Models\\Article', $idArticle);
        $em->remove($itemToRemove);
        $em->flush();

        $url = $app['url_generator']->generate('admin');

        return $app->redirect($url);
    }


     public function modifier($idArticle, Application $app) {
       // $this->storage->removeElement($index);
        $em = $app['em'];
        $itemToReturn = $em->find('DUT\\Models\\Article', $idArticle);
        

        return $app['twig']->render('modifierArticle.twig',['article'=>$itemToReturn]);

        
    }

     public function modifierContenuArticle(Request $request, Application $app) {
        $em = $app['em'];
        $url = $app['url_generator']->generate('admin');
        $idArticle=$request->get("idArticle");
        $itemToModify = $em->find('DUT\\Models\\Article', $idArticle);
        $newContenuArticle=$request->get("contenuArticle");

        var_dump($idArticle);

        if (!is_null($request)) {
          if (strlen($newContenuArticle)<65000) {
            $itemToModify->setContenuArticle($newContenuArticle);
        
            $em->persist($itemToModify);
            $em->flush();

            return $app->redirect($url);
                
           }
            
        }
    }


}
