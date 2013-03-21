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

// service settings
$app->get('/services/{name}', function($name) use ($app) {
    $service = Dukt\Videos\Common\ServiceFactory::create($name);
    $sessionVar = 'dukt.videos.'.$service->getShortName();

    $service->initialize((array) $app['session']->get($sessionVar));

    return $app['twig']->render('service.twig', array(
        'service' => $service,
        'settings' => $service->getParameters(),
    ));
});

// post service settings
$app->post('/services/{name}', function($name) use ($app) {
    $service = Dukt\Videos\Common\serviceFactory::create($name);
    $sessionVar = 'dukt.videos.'.$service->getShortName();
    $service->initialize((array) $app['request']->get('service'));

    // save service settings in session
    $app['session']->set($sessionVar, $service->getParameters());

    // redirect back to service settings page
    $app['session']->getFlashBag()->add('success', 'Service settings updated!');

    return $app->redirect($app['request']->getPathInfo());
});

$app->run();