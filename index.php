<?php

require __DIR__ . '/vendor/autoload.php';

use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;

$app = new Silex\Application();
$app->register(new Silex\Provider\UrlGeneratorServiceProvider());
$app->register(new Silex\Provider\TwigServiceProvider(), ['twig.path' => __DIR__.'/src/Views',]);

$app['connection'] = [
    'driver' => 'pdo_mysql',
    'host' => 'localhost',
    'user' => 'root',
    'password' => '',
    'dbname' => 'blogsilex'
];

$app['doctrine_config'] = Setup::createYAMLMetadataConfiguration([__DIR__ . '/config'], true);

$app['em'] = function ($app) {
    return EntityManager::create($app['connection'], $app['doctrine_config']);
};

$app->get('/persons', function () use ($app) {
    $entityManager = $app['em'];
    $repository = $entityManager->getRepository('DUT\\Models\\Person');
});

/**
 * ROUTES
 */
$app->get('/', 'DUT\\Controllers\\ItemsController::afficheHomePage')
    ->bind('home');

$app->get('/admin', 'DUT\\Controllers\\ItemsController::afficheAdminPage')
	->bind('admin');

$app->get('/article/{idArticle}', 'DUT\\Controllers\\ItemsController::afficheArticlePage');
$app->post('/article/{idArticle}', 'DUT\\Controllers\\ItemsController::afficheArticlePage');


$app->get('/remove/{idArticle}', 'DUT\\Controllers\\ItemsController::deleteAction');


$app->get('/ajout_article', 'DUT\\Controllers\\ItemsController::ajoutArticle')
    ->bind('ajout_article');
$app->post('/ajout_article', 'DUT\\Controllers\\ItemsController::ajoutArticle');

$app->get('/modifier/{idArticle}', 'DUT\\Controllers\\ItemsController::modifier');

/*$app->get('/modifier/{idArticle}', function()use($app){
     return $app['twig']->render('modifierArticle.twig');

});*/

$app->post('/modifier/{idArticle}', 'DUT\\Controllers\\ItemsController::modifierContenuArticle');


$app['debug'] = true;
$app->run();
