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

    public function afficheArticlePage($idArticle, Request $request, Application $app){
        $entityManager = $app['em'];

        //On va d'abord chercher l'article en question dans la BD
        $research = $entityManager->getRepository('DUT\\Models\\Article');
        $article = $research->findBy(['idArticle' => $idArticle]);
        $article = $article[0];

        //Ensuite on va aller chercher tous les commentaires sur cet article
        $research = $entityManager->getRepository('DUT\\Models\\Commentaire');
        $commentaires = $research->findBy(['idArticle' => $idArticle]);


        //On va chercher les champs du formulaire
        $nomEditeur = $request->get('nomEditeur', null);
        $contenuCommentaire = $request->get('contenuCommentaire',null);


        //On traite le formulaire
        if(!is_null($nomEditeur) && !is_null($contenuCommentaire)){
            //On sécurise les deux champs du formulaire
            $nomEditeur = htmlspecialchars($nomEditeur);
            $contenuCommentaire = htmlspecialchars($contenuCommentaire);

            //On vérifie la tailles des champs
            if(strlen($nomEditeur)<=50 && strlen($contenuCommentaire)<=10000){
                $commentaireToAdd = new Commentaire(null,$idArticle,$nomEditeur,$contenuCommentaire);
                $entityManager->persist($commentaireToAdd);
                $entityManager->flush();

                //On actualise les variables $article et $commentaires
               
                $research = $entityManager->getRepository('DUT\\Models\\Article');
                $article = $research->findBy(['idArticle' => $idArticle]);
                $article = $article[0];

                $research = $entityManager->getRepository('DUT\\Models\\Commentaire');
                $commentaires = $research->findBy(['idArticle' => $idArticle]);
            }
        }

        
        return $app['twig']->render('article.twig', ['article' => $article , 'commentaires' => $commentaires]);
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

    public function modifierContenuArticle($idArticle, Application $app) {
       // $this->storage->removeElement($index);
        $em = $app['em'];
        $itemToReturn = $em->find('DUT\\Models\\Article', $idArticle);

        
        

        return $app['twig']->render('modifierArticle.twig',['article'=>$itemToReturn]);

        
    }


}
