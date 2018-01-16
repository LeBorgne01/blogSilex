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
    private $routeAbsolue;

    public function __construct() {
        $this->storage = new SessionStorage();
        $this->routeAbsolue = substr(__DIR__, 0,24);
    }

    //
    // Pages du site visiteur
    //
    
    
    //Fonction pour afficher la homePage
    public function afficheHomePage(Application $app) {
        $entityManager = $app['em'];

        //On va chercher les articles dans la base de données
        $research = $entityManager->getRepository('DUT\\Models\\Article');
        $articles = $research->findAll();

        //On affiche la page 'home' avec ces articles
        return $app['twig']->render('home.twig', ['articles' => $articles]);
    }

    
    //Fonction pour afficher la page d'un article
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

            //On redirige sur la page de ce même article
            return $app->redirect($idArticle);
        }

        return $app['twig']->render('article.twig', ['article' => $article, 'commentaires' => $commentaires]);
    }

    
    //Fonction pour ajouter un article
    public function ajoutArticle(Request $request, Application $app){
        $entityManager = $app['em'];

        //On récupère les champs du formulaire
        $titreArticle = $request->get("titreArticle", null);
        $contenuArticle = $request->get("contenuArticle", null);
        $tagArticle = $request->get("tagArticle", null);
        

        //On déclare une erreur pour le fichier du formulaire
        $erreur = "";

        if(!is_null($titreArticle) && !is_null($contenuArticle)){
            //On sécurise les données récupérées
            $titreArticle = htmlspecialchars($titreArticle);
            $contenuArticle = htmlspecialchars($contenuArticle);
            $tagArticle = htmlspecialchars($tagArticle);
            $fichier = $_FILES['lienPhoto'];

            //On vérifie le fichier
            //On vérifie si un fichier a été uploadé
            if(!$fichier['error'] == 4){

                //On vérifie les erreurs de transfert
                if($fichier['error'] > 0){
                    $erreur = "Erreur lors du transfert de la photo";
                }
                else{

                    //On vérifie la taille du fichier (doit être inférieure à 10 Mo)
                    if($fichier['size'] > 10000000){
                        $erreur = "Le fichier est trop gros";
                    }
                    else{

                        //On vérifie si le fichier est bien une image
                        $extensionsValides = array('jpg', 'jpeg', 'gif', 'png');

                        //strrchr renvoie l'extension du fichier avec le point
                        //substr ignore le point (1er caractère de la chaine)
                        //strtolower met l'extension en minuscule
                        $extensionUpload = strtolower( substr( strrchr($fichier['name'], '.'), 1));

                        if(!in_array($extensionUpload, $extensionsValides)){
                            $erreur = "Votre fichier n'est pas une image";
                        }
                        else{
                            //On déplace le fichier dans le dossier pictures du site

                            //On met un nom pseudo aléatoire comme nom du fichier (Façon simple de mettre un nom unique sur chaque image)
                            $nomFichier = substr($fichier['tmp_name'], 17);
                            $nomFichier = explode('.',$nomFichier);
                            $nomFichier = $nomFichier[0];

                            //Déplacement du fichier
                            $nomFichierComplet  = $nomFichier.'.'.$extensionUpload;
                            $nomFichierBase = "pictures/".$nomFichierComplet;
                            $nomDestination = $this->routeAbsolue.'src\\Views\\pictures\\'.$nomFichier.'.'.$extensionUpload;


                            $resultatTransfert = move_uploaded_file($fichier['tmp_name'],$nomDestination);

                            if(!$resultatTransfert){
                                $erreur = "Echec du transfert";
                            }
                            else{
                                //On crée un nouvel article avec photo
                                $article = new Article(null, $titreArticle, $contenuArticle, $tagArticle, $nomFichierBase);

                                //On l'insère dans la base
                                $entityManager->persist($article);
                                $entityManager->flush();
                            }                            
                        }
                    }
                }
            }
            else{
                //On crée un nouvel article
                $article = new Article(null, $titreArticle, $contenuArticle, $tagArticle, null);

                //On l'insère dans la base
                $entityManager->persist($article);
                $entityManager->flush();

                //On redirige vers la page 'home'
                $url = $app['url_generator']->generate('home');
                return $app->redirect($url);
            }
            
        }

        return $app['twig']->render('ajouterArticle.twig', ['erreur' => $erreur]);
    }

    
    //Fonction pour afficher la page 'citations'
    public function afficheCitationPage(Request $request, Application $app){
        $entityManager = $app['em'];

        //On récupère les citations de la base de données
        $repository = $entityManager->getRepository('DUT\\Models\\Citation');
        //$citations = $repository->findAll();
        //On récupère les citations par ordre decroissant de 'j'aime'
        $citations = $repository->findBy([], ['nombreAime' => 'desc']);

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

            //On redirige sur la page 'citations'
            $url = $app['url_generator']->generate('citations');
            return $app->redirect($url);
        }

        return $app['twig']->render('citation.twig', ['citations' => $citations]);
    }

    
    //Fonction pour ajouter une citation
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

            //On redirige sur la page 'citations'
            $url = $app['url_generator']->generate('citations');
            return $app->redirect($url);
        }

        return $app['twig']->render('ajouterCitation.twig');
    }

    //
    //Fin pages site visiteur
    //

    //
    //Pages administrations
    //


    //Fonction pour afficher la page 'admin' avec les articles
    public function afficheAdminPage(Application $app){
        $entityManager = $app['em'];

        //On va chercher les articles dans la base de données
        $repository = $entityManager->getRepository('DUT\\Models\\Article');
        $resultat = $repository->findAll();

        //On charge la page avec ces articles
        return $app['twig']->render('admin.twig', ['articles' => $resultat]);
    }


    //Fonction pour supprimer un article
    public function deleteAction($idArticle, Application $app) {
        $entityManager = $app['em'];

        //On va chercher l'article correspondant dans la base
        $articleToRemove = $entityManager->find('DUT\\Models\\Article', $idArticle);

        //On supprime la photo si il y en a une
        $lienPhoto = $articleToRemove->getLienPhoto();

        if(!is_null($lienPhoto)){
            unlink($this->routeAbsolue.'src/Views/'.$lienPhoto);
        }

        //On supprime cet article
        $entityManager->remove($articleToRemove);

        //On va chercher les commentaires correspondant à l'article
        $repository = $entityManager->getRepository('DUT\\Models\\Commentaire');
        $commentairesASupprimer = $repository->findBy(['idArticle' => $idArticle]);

        //On supprime ces commentaires
        foreach ($commentairesASupprimer as $commentaireASupprimer) {
            $entityManager->remove($commentaireASupprimer);
        }
        
        //On met à jour la base de données
        $entityManager->flush();

        //On redirige vers la page admin
        $url = $app['url_generator']->generate('admin');
        return $app->redirect($url);
    }


    //Fonction pour supprimer un commentaire
    public function deleteCommentaire($idCommentaire, Application $app) {
        $entityManager = $app['em'];

        //On récupère le commentaire correspondant dans la base de données
        $itemToRemove = $entityManager->find('DUT\\Models\\Commentaire', $idCommentaire);

        //On supprime ce commentaire de la base de données
        $entityManager->remove($itemToRemove);
        $entityManager->flush();

        //On redirige vers la page "admin"
        $url = $app['url_generator']->generate('admin');
        return $app->redirect($url);
    }


    //Fonction pour modérer les commentaires
    public function modererCommentaire($idCommentaire,$raison, Application $app) {
        $entityManager = $app['em'];

        //On va chercher dans la base le commentaire à modérer
        $itemToModify = $entityManager->find('DUT\\Models\\Commentaire', $idCommentaire);
        
        //Si on a choisi l'option "modéré" => 0
        //Si on a choisi l'option "commentaire inutile" => 1
        //Si on a choisi l'option "commentaire raciste" => 2
        //On modifie le commentaire en conséquence
        if ($raison=='0') {
            $itemToModify->setContenuCommentaire("Ce commentaire à été modéré par l'administrateur.");
        }
        if ($raison=='1') {
            $itemToModify->setContenuCommentaire("Ce commentaire à été modéré par l'administrateur car il à été jugé inutil.");
        }
        if ($raison=='2') {
            $itemToModify->setContenuCommentaire("Ce commentaire à été modéré par l'administrateur car il comportait des propos injurieux et/ou discriminatoire.");
        }
        
        //On modifie le commentaire dans la base de données
        $entityManager->persist($itemToModify);
        $entityManager->flush();

        //On redirige vers la page "admin"
        $url = $app['url_generator']->generate('admin');
        return $app->redirect($url);
    }


    //Fonction pour modérer les citations (les supprimées ici car on n'a que ce choix)
    public function modererCitations(Request $request, Application $app) {
        $entityManager = $app['em'];

        //On récupère toutes les citations
        $repository = $entityManager->getRepository('DUT\\Models\\Citation');
        $citations= $repository->findAll();

        //On récupère l'id de la citation à supprimée
        $idCitation = $request->get('idCitation');

        if(!is_null($idCitation)){
            //On va chercher dans la base la citation correspondante
            $citation=$repository->findOneBy(["idCitation"=>$idCitation]);

            //On supprime la citation de la base de données
            $entityManager->remove($citation);
            $entityManager->flush();

            //On redirige sur cette même page
            $url = $app['url_generator']->generate('moderer_citations');
            return $app->redirect($url); 
        }
      
        return $app['twig']->render('modererCitation.twig',['citations'=>$citations]);
    }


    //Fonction ??
    public function modifier($idArticle, Application $app) {
        // $this->storage->removeElement($index);
        $em = $app['em'];
        $itemToReturn = $em->find('DUT\\Models\\Article', $idArticle);


        return $app['twig']->render('modifierArticle.twig',['article'=>$itemToReturn]);


    }   


    //Fonction pour modifier le contenu d'un article 
    public function modifierContenuArticle($idArticle, Request $request, Application $app) {
        $entityManager = $app['em'];

        //On récupère l'article correspondant à l'Id 
        $articleAModifier = $entityManager->find('DUT\\Models\\Article', $idArticle);

        //On récupère tous les commentaires liés à l'article récupéré
        $repository = $entityManager->getRepository('DUT\\Models\\Commentaire');
        $commentaires = $repository->findBy(['idArticle' => $idArticle]);
        
        //On récupère les données du formulaire
        $contenuArticleModifie = $request->get('contenuArticle', null);

        if(!is_null($contenuArticleModifie)){
            //On regarde si le contenu de l'article est inférieur à 65000 caratères (taille max de la base de données)
            if(strlen($contenuArticleModifie)<65000){
                //Si oui
                //On modifie le contenu de l'article 
                $articleAModifier->setContenuArticle($contenuArticleModifie);

                //On met à jour la Base de données
                $entityManager->persist($articleAModifier);
                $entityManager->flush();

                //On charge la page admin
                $url = $app['url_generator']->generate('admin');
                return $app->redirect($url); 
            }

        }

        return $app['twig']->render('modifierArticle.twig', ['article' => $articleAModifier, 'commentaires' => $commentaires]);
    }

    //
    //Fin page administration
    //
}
