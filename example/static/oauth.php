<?php

$api = array();
$api['id'] = "";
$api['secret'] = "";
$api['redirect_url'] = "";

// retrieve your saved $api['token'], usuallu 

$api['token'] = "xxx";
$api['token'] = unserialize(base64_decode($api['token']));


// create provider

$provider = \OAuth\OAuth::provider($providerClass, array(
    'id' => $api['id'],
    'secret' => $api['secret'],
    'redirect_url' => UrlHelper::getActionUrl('oauth/connectCallback')
));

$provider = $provider->setToken($api['token']);


// create service

$service = \Dukt\Videos\Common\ServiceFactory::create('YouTube', $provider);