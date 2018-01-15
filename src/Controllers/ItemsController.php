<?php

namespace DUT\Controllers;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Silex\Application;
use DUT\Services\SessionStorage;



use DUT\Models\Commentaire;
use DUT\Models\Article;
use DUT\Models\Citation;

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
        $repository = $entityManager->getRepository('DUT\\Models\\Commentaire');

        //On va chercher dans la base l'article correspondant ainsi que ses commentaires
        $article = $entityManager->find('DUT\\Models\\Article', $idArticle);
        $commentaires = $repository->findBy(['idArticle' => $idArticle]);

        //Pour le formulaire
        //On stock les variables des champs
        $nomEditeur = $request->get('nomEditeur', null);
        $contenuCommentaire = $request->get('contenuCommentaire', null);

        if(!is_null($nomEditeur) && !is_null($contenuCommentaire)){
            //On sécurise les données des champs
            $nomEditeur = htmlspecialchars($nomEditeur);
            $contenuCommentaire = htmlspecialchars($contenuCommentaire);

            //On crée un nouveau commentaire
            $commentaire = new Commentaire(null, $idArticle, $nomEditeur, $contenuCommentaire);

            //On enregistre ce commentaire dans la base de données
            $entityManager->persist($commentaire);
            $entityManager->flush();

            return $app->redirect($idArticle);
        }
        
        return $app['twig']->render('article.twig', ['article' => $article, 'commentaires' => $commentaires]);
<<<<<<< HEAD

}

    }

=======
    }
>>>>>>> d67e90d287acde5fed5928696ad8aee4360f98f8


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

    public function ajoutArticle(Request $request, Application $app){
        $entityManager = $app['em'];

        //On récupère les champs du formulaire
        $titreArticle = $request->get("titreArticle", null);
        $contenuArticle = $request->get("contenuArticle", null);
        $tagArticle = $request->get("tagArticle", null);

        if(!is_null($titreArticle) && !is_null($contenuArticle)){
            //On sécurise les données récupérées
            $titreArticle = htmlspecialchars($titreArticle);
            $contenuArticle = htmlspecialchars($contenuArticle);
            $tagArticle = htmlspecialchars($tagArticle);

            //On crée un nouvel article
            $article = new Article(null, $titreArticle, $contenuArticle, $tagArticle, null);
            
            //On l'insère dans la base
            $entityManager->persist($article);
            $entityManager->flush();

            $url = $app['url_generator']->generate('home');

            return $app->redirect($url);
        }

        return $app['twig']->render('ajouterArticle.twig');
    }

    public function afficheCitationPage(Request $request, Application $app){
        $entityManager = $app['em'];

        //On récupère les citations de la base de données
        $repository = $entityManager->getRepository('DUT\\Models\\Citation');
        $citations = $repository->findAll();

        //On récupère l'Id de la citation
        $idCitation = $request->get("idCitation");

        if(!is_null($idCitation)){
            //On récupère la citation en question
            $citationModifiee = $repository->findOneBy(['idCitation' => $idCitation]);
            
            //On lui ajoute un j'aime
            $citationModifiee->ajouterUnAime();

            //On met à jour la base de données
            $entityManager->persist($citationModifiee);
            $entityManager->flush();

            $url = $app['url_generator']->generate('citations');
            return $app->redirect($url);
        }


       return $app['twig']->render('citation.twig', ['citations' => $citations]);
    }


}
