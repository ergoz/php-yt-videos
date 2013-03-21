<?php

require __DIR__.'/../vendor/autoload.php';

// create basic Silex application
$app = new Silex\Application();
$app->register(new Silex\Provider\SessionServiceProvider());
$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => __DIR__.'/views',
));


// enable Silex debugging
$app['debug'] = true;


// root route
$app->get('/', function() use ($app) {

    $services = array_map(function($className) {
        return Dukt\Videos\Common\ServiceFactory::create($className);
    }, Dukt\Videos\Common\ServiceFactory::find());

    return $app['twig']->render('index.twig', array(
        'services' => $services,
    ));

});

$app->run();