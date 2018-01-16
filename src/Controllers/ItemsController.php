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

    public function deleteCommentaire($idCommentaire, Application $app) {
        // $this->storage->removeElement($index);
        $em = $app['em'];
        $itemToRemove = $em->find('DUT\\Models\\Commentaire', $idCommentaire);
        $em->remove($itemToRemove);
        $em->flush();

        $url = $app['url_generator']->generate('admin');

        return $app->redirect($url);
    }

    public function modererCommentaire($idCommentaire,$raison, Application $app) {
        // $this->storage->removeElement($index);
        $em = $app['em'];
        $itemToModify = $em->find('DUT\\Models\\Commentaire', $idCommentaire);
        
        
        if ($raison=='0') {
            $itemToModify->setContenuCommentaire("Ce commentaire à été modéré par l'administrateur.");
        }
        if ($raison=='1') {
            $itemToModify->setContenuCommentaire("Ce commentaire à été modéré par l'administrateur car il à été jugé inutil.");
        }
        
        
        if ($raison=='2') {
            $itemToModify->setContenuCommentaire("Ce commentaire à été modéré par l'administrateur car il comportait des propos injurieux et/ou discriminatoire.");
        }
        
        $em->persist($itemToModify);
        $em->flush();

        $url = $app['url_generator']->generate('admin');

        return $app->redirect($url);
    }

    public function modererCitations(Request $request, Application $app) {
        $entityManager = $app['em'];
        $repository = $entityManager->getRepository('DUT\\Models\\Citation');
       
        $citations= $repository->findAll();

        $idCitation = $request->get('idCitation');

        if(!is_null($idCitation)){
            $citation=$repository->findOneBy(["idCitation"=>$idCitation]);
            $entityManager->remove($citation);
            $entityManager->flush();

            $url = $app['url_generator']->generate('moderer_citations');
             return $app->redirect($url); 

        }
      
        
         return $app['twig']->render('modererCitation.twig',['citations'=>$citations]);
        }

    public function modifier($idArticle, Application $app) {
        // $this->storage->removeElement($index);
        $em = $app['em'];
        $itemToReturn = $em->find('DUT\\Models\\Article', $idArticle);


        return $app['twig']->render('modifierArticle.twig',['article'=>$itemToReturn]);


    }   



    public function modifierContenuArticle($idArticle, Request $request, Application $app) {
        $entityManager = $app['em'];

        //On récupère l'article correspondant à l'Id 
        $articleAModifier = $entityManager->find('DUT\\Models\\Article', $idArticle);

        $repository = $entityManager->getRepository('DUT\\Models\\Commentaire');
        $commentaires = $repository->findBy(['idArticle' => $idArticle]);
        //var_dump($commentaireAssocie);

        //On récupère les données du formulaire
        $contenuArticleModifie = $request->get('contenuArticle', null);

        if(!is_null($contenuArticleModifie)){
            //On regarde si le contenu de l'article est inférieur à 65000 caratères (taille max de la base de données)
            if(strlen($contenuArticleModifie)<65000){
                $articleAModifier->setContenuArticle($contenuArticleModifie);

                $entityManager->persist($articleAModifier);
                $entityManager->flush();

                $url = $app['url_generator']->generate('admin');
                return $app->redirect($url); 
            }

        }

        return $app['twig']->render('modifierArticle.twig', ['article' => $articleAModifier, 'commentaires' => $commentaires]);

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

    public function ajoutCitation(Request $request, Application $app){
        $entityManager = $app['em'];

        //On récupère les champs du formulaire
        $contenuCitation = $request->get('contenuCitation', null);
        $lienVideoCitation = $request->get('lienVideoCitation', null);

        if(!is_null($contenuCitation) && !is_null($lienVideoCitation)){
            //On sécurise les données récupérées
            $contenuCitation = htmlspecialchars($contenuCitation);
            $lienVideoCitation = htmlspecialchars($lienVideoCitation);

            //On récupère l'id de la vidéo youtube (méthode simple sans vérification d'url)
            $lienVideoCitation = explode('=', $lienVideoCitation);
            $lienVideoCitation = "https://www.youtube.com/embed/".$lienVideoCitation[1];

            //On crée une nouvelle citation
            $nouvelleCitation = new Citation(null,$contenuCitation,$lienVideoCitation,0);

            //On l'ajoute à la base de données
            $entityManager->persist($nouvelleCitation);
            $entityManager->flush();

            $url = $app['url_generator']->generate('citations');
            return $app->redirect($url);
        }


        return $app['twig']->render('ajouterCitation.twig');
    }


}
