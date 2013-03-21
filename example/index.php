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

    $token = $service->getToken();

    if(empty($token))
    {
        return $app->redirect($app['request']->getPathInfo().'/authorize');
    }   
    else
    {
        return $app->redirect($app['request']->getPathInfo());
    } 
    
});

// create service revoke
$app->get('/services/{name}/revoke', function($name) use ($app) {

    echo $app['request']->getPathInfo();

    // retrieve service with params

    $service = Dukt\Videos\Common\ServiceFactory::create($name);
    $sessionVar = 'dukt.videos.'.$service->getShortName();
    $parameters = $app['session']->get($sessionVar);
    $service->initialize((array) $parameters);

    $parameters['token'] = '';

    $app['session']->set($sessionVar, $parameters);

    return $app->redirect('/services/'.$name);

});


// create service authorize
$app->get('/services/{name}/authorize', function($name) use ($app) {

    // retrieve service with params

    $service = Dukt\Videos\Common\ServiceFactory::create($name);
    $sessionVar = 'dukt.videos.'.$service->getShortName();
    $parameters = $app['session']->get($sessionVar);
    $service->initialize((array) $parameters);

    $provider = \OAuth\OAuth::provider($service->getProviderClass(), array(
        'id' => $parameters['id'],
        'secret' => $parameters['secret'],
        'redirect_url' => $app['request']->getSchemeAndHttpHost().$app['request']->getBaseUrl().$app['request']->getPathInfo()
    ));

    $provider = $provider->process(function($url, $token = null) {

        if ($token) {
            $_SESSION['token'] = base64_encode(serialize($token));
        }

        header("Location: {$url}");

        exit;

    }, function() {
        return unserialize(base64_decode($_SESSION['token']));
    });

    // save token

    $parameters['token'] = $provider->token();
    $parameters['token'] = base64_encode(serialize($parameters['token']));

    $app['session']->set($sessionVar, $parameters);


    // redirect to service
    $redirect = $app['request']->getPathInfo();
    $redirect = substr($redirect, 0,  - (strlen($redirect) - strrpos($redirect, "/")) );
    
    return $app->redirect($redirect);

});





// create service getUserInfos
$app->get('/services/{name}/getUserInfos', function($name) use ($app) {
    $service = Dukt\Videos\Common\ServiceFactory::create($name);
    $sessionVar = 'dukt.videos.'.$service->getShortName();
    $service->initialize((array) $app['session']->get($sessionVar));

    $params = $app['session']->get($sessionVar.'.getUserInfos', array());

    return $app['twig']->render('request.twig', array(
        'service' => $service,
        'method' => 'getUserInfos',
        'params' => $params,
    ));
});


// post service getUserInfos
$app->post('/services/{name}/getUserInfos', function($name) use ($app) {
    
    $service = Dukt\Videos\Common\ServiceFactory::create($name);
    $sessionVar = 'dukt.videos.'.$service->getShortName();
    $sessionData = $app['session']->get($sessionVar);
    $service->initialize((array) $sessionData);
    
    $authorize_url = $app['request']->getSchemeAndHttpHost()
        .$app['request']->getBaseUrl()
        .'/services/'
        .$service->getShortName()
        .'/authorize';

    $provider = \OAuth\OAuth::provider($service->getProviderClass(), array(
        'id' => $sessionData['id'],
        'secret' => $sessionData['secret'],
        'redirect_url' => $authorize_url
    ));

    $token = unserialize(base64_decode($sessionData['token']));

    $provider->setToken($token);

    $service->setProvider($provider);

    // load POST data
    $params = $app['request']->get('params');

    // save POST data into session
    $app['session']->set($sessionVar.'.getUserInfos', $params);

    $response = $service->getUserInfos($params);

    return $app['twig']->render('response.twig', array(
        'service' => $service,
        'response' => $response,
    ));
});





// create service getVideo
$app->get('/services/{name}/getVideo', function($name) use ($app) {
    $service = Dukt\Videos\Common\ServiceFactory::create($name);
    $sessionVar = 'dukt.videos.'.$service->getShortName();
    $service->initialize((array) $app['session']->get($sessionVar));

    $params = $app['session']->get($sessionVar.'.getVideo', array());

    return $app['twig']->render('request.twig', array(
        'service' => $service,
        'method' => 'getVideo',
        'params' => $params,
    ));
});


// post service getVideo
$app->post('/services/{name}/getVideo', function($name) use ($app) {
    
    $service = Dukt\Videos\Common\ServiceFactory::create($name);
    $sessionVar = 'dukt.videos.'.$service->getShortName();
    $sessionData = $app['session']->get($sessionVar);
    $service->initialize((array) $sessionData);
    
    $authorize_url = $app['request']->getSchemeAndHttpHost()
        .$app['request']->getBaseUrl()
        .'/services/'
        .$service->getShortName()
        .'/authorize';

    $provider = \OAuth\OAuth::provider($service->getProviderClass(), array(
        'id' => $sessionData['id'],
        'secret' => $sessionData['secret'],
        'redirect_url' => $authorize_url
    ));

    $token = unserialize(base64_decode($sessionData['token']));

    $provider->setToken($token);

    $service->setProvider($provider);

    // load POST data
    $params = $app['request']->get('params');

    // save POST data into session
    $app['session']->set($sessionVar.'.getVideo', $params);

    $response = $service->getVideo($params);

    return $app['twig']->render('response.twig', array(
        'service' => $service,
        'response' => $response,
    ));
});

$app->run();