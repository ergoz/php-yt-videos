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


$app->get('/tests/unauthenticated', function() use ($app) {

    $service = \Dukt\Videos\Common\ServiceFactory::create('YouTube');

    $url = "http://www.youtube.com/watch?v=0ZUvQ5h-TCA";

    $videoId = $service->getUserInfos($url);

    if($videoId) {
        ?>
        <h1>Videos Infos</h1>
        <ul>
            <li>url : <?php echo $url?></li>
            <li>videoId : <?php echo $videoId?></li>
        </ul>
        <?php
    }
    else
    {
        ?>
        <h1>Error</h1>
        <p>Invalid Video URL</p>
        <?php
    }
    
    die();
});

$app->get('/tests/embed', function() use ($app) {

    $id = "0ZUvQ5h-TCA";
    $id = "JtawDJtcRg8";
    $url = "http://www.youtube.com/watch?v=".$id;

    $service = \Dukt\Videos\Common\ServiceFactory::create('YouTube');
    $videoObj = new \Dukt\Videos\YouTube\Video(array('id' => $id));
    $videoObj->id = $id;

    $options = array(
        'width' => 500,
        'height' => 300,
        'autoplay' => 1
    );

    $video = $videoObj->getEmbed($options);

    if($video) {
        var_dump(($video));
    }
    else
    {
        ?>
        <h1>Error</h1>
        <?php
    }
    
    die();
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
    return appGet($name, $app, 'getUserInfos');
});


// post service getUserInfos
$app->post('/services/{name}/getUserInfos', function($name) use ($app) {
    return appPost($name, $app, 'getUserInfos', 'response.twig');
});


// create service getVideo
$app->get('/services/{name}/getVideo', function($name) use ($app) {
    return appGet($name, $app, 'getVideo');
});


// post service getVideo
$app->post('/services/{name}/getVideo', function($name) use ($app) {
    return appPost($name, $app, 'getVideo', 'responseVideo.twig');
});



// create service search
$app->get('/services/{name}/isFavorite', function($name) use ($app) {
    return appGet($name, $app, 'isFavorite');
});


// post service search
$app->post('/services/{name}/isFavorite', function($name) use ($app) {
    return appPost($name, $app, 'isFavorite', 'responseCollection.twig');
});



// create service getFavorites
$app->get('/services/{name}/getFavorites', function($name) use ($app) {
    return appGet($name, $app, 'getFavorites');
});


// post service getFavorites
$app->post('/services/{name}/getFavorites', function($name) use ($app) {
    return appPost($name, $app, 'getFavorites', 'responseCollection.twig');
});


// create service getUploads
$app->get('/services/{name}/getUploads', function($name) use ($app) {
    return appGet($name, $app, 'getUploads');
});


// post service getUploads
$app->post('/services/{name}/getUploads', function($name) use ($app) {
    return appPost($name, $app, 'getUploads', 'responseCollection.twig');
});


// create service search
$app->get('/services/{name}/search', function($name) use ($app) {
    return appGet($name, $app, 'search');
});


// post service search
$app->post('/services/{name}/search', function($name) use ($app) {
    return appPost($name, $app, 'search', 'responseCollection.twig');
});



function appGet($name, $app, $method) {
    $service = Dukt\Videos\Common\ServiceFactory::create($name);
    $sessionVar = 'dukt.videos.'.$service->getShortName();
    $service->initialize((array) $app['session']->get($sessionVar));

    $params = $app['session']->get($sessionVar.'.'.$method, array());

    return $app['twig']->render('request.twig', array(
        'service' => $service,
        'method' => $method,
        'params' => $params,
    ));
}

function appPost($name, $app, $method, $template)
{
    $service = Dukt\Videos\Common\ServiceFactory::create($name);
    $sessionVar = 'dukt.videos.'.$service->getShortName();
    $sessionData = $app['session']->get($sessionVar);
    $service->initialize((array) $sessionData);
    
    $authorize_url = $app['request']->getSchemeAndHttpHost()
        .$app['request']->getBaseUrl()
        .'/services/'
        .$service->getShortName()
        .'/authorize';

    $providerOptions = array();
    $providerOptions['id'] = $sessionData['id'];
    $providerOptions['secret'] = $sessionData['secret'];    
    $providerOptions['redirect_url'] = $authorize_url;

    if(isset($sessionData['developerKey']))
    {
        $providerOptions['developerKey'] = $sessionData['developerKey'];    
    }

    $provider = \OAuth\OAuth::provider($service->getProviderClass(), $providerOptions);

    $token = unserialize(base64_decode($sessionData['token']));

    // refresh token if needed ?

    $provider->setToken($token);

    $service->setProvider($provider);

    // load POST data
    $params = $app['request']->get('params');

    // save POST data into session
    $app['session']->set($sessionVar.'.'.$method, $params);

    $response = $service->{$method}($params);

    return $app['twig']->render($template, array(
        'service' => $service,
        'response' => $response,
    ));
}


$app->run();