# Dukt Videos

**Universal video library for Vimeo & YouTube APIs and PHP 5.3+**

[![Build Status](https://travis-ci.org/dukt/videos.png?branch=master)](https://travis-ci.org/dukt/videos)

## Installation

Dukt Videos is installed via [Composer](http://getcomposer.org/). To install, simply add it
to your `composer.json` file:

```json
{
    "require": {
        "dukt/videos": "dev-master"
    }
}
```


And run composer to update your dependencies:

    $ curl -s http://getcomposer.org/installer | php
    $ php composer.phar update

## Video Services

The following video services are implemented :

* Vimeo
* YouTube

Creating a video  web site is very easy, all you need to do is : use the ServiceFactory in order to get a Service instance, and play with the service as you like. You can also set an OAuth provider if you want to make authenticated call to a service.

```php
$service = \Dukt\Videos\Common\ServiceFactory::create('YouTube', $provider);

$url = "http://www.youtube.com/watch?v=0ZUvQ5h-TCA";

$videoId = $service->getVideoId($url);

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
```

## Service Methods

The main methods implemented by video services are:

* `video($params)` - Get video from its ID
* `search($params)` - Search videos
* `uploads($params)` - Get uploaded videos
* `userInfos($params)` - Get current user infos

### Favorites

* `favorites($params)` - Get favorite videos
* `favoriteAdd($params)` - Add video to favorites
* `favoriteRemove($params)` - Remove Video from favorites
* `isFavorite($params)` - Checks if a video is a favorite

### Playlists

* `playlists($params)` - Get playlists
* `playlist($params)` - Get videos for given playlist
* `playlistCreate($params)` - Create a playlist
* `playlistDelete($params)` - Delete a playlist
* `playlistAddVideo($params)` - Add a video to a playlist
* `playlistRemoveVideo($params)` - Remove a video from a playlist

## Video Methods

The main methods implemented by video are:

* `getEmbed($options)` - Get the video embed

## OAuth Providers

For authenticated calls, video service need an OAuth provider. Each video service has its own provider :

**Vimeo**

* Service : Vimeo
* OAuth Provider : Vimeo

**YouTube**

* Service : YouTube
* OAuth Provider : Google

Here is how to create an OAuth provider and use it with a service :

```php

// Create the OAuth provider

$provider = \OAuth\OAuth::provider('Google', array(
    'id' => CLIENT_ID,
    'secret' => CLIENT_SECRET,
    'redirect_url' => REDIRECT_URL
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


// Create video service

$service = Dukt\Videos\Common\ServiceFactory::create('YouTube');
$service->setProvider($provider);


// Retrieve token and store it somewhere safe :

$token = $provider->token();
$token = base64_encode(serialize($token));
```

If you want to initialize the OAuth provider with an existing token, you will want to do something like this :

```php

// Retrieve token

$token = unserialize(base64_decode(STORED_TOKEN));


// Create the OAuth provider

$provider = \OAuth\OAuth::provider('Google', array(
    'id' => CLIENT_ID,
    'secret' => CLIENT_SECRET,
    'redirect_url' => REDIRECT_URL
));    

$provider->setToken($token);


// Create video service

$service = Dukt\Videos\Common\ServiceFactory::create('YouTube');
$service->setProvider($provider);
```

Dukt Videos relies on [dukt/oauth](https://github.com/dukt/oauth), which is a fork of [chrisnharvey/oauth](https://github.com/chrisnharvey/oauth) to which we added Vimeo OAuth provider. We plan to use chrisnharvey's version as soon as our Vimeo pull request is accepted.


## Example Application

An example application is provided in the `example` directory. You can run it using PHP's built in
web server (PHP 5.4+):

    $ php composer.phar update --dev
    $ php -S localhost:8000 -t example/


## Feedback

**Please provide feedback!** We want to make this library useful in as many projects as possible.
Please raise a Github issue, and point out what you do and don't like, or fork the project and make
suggestions. **No issue is too small.**

This plugin is actively maintained by [Benjamin David](https://github.com/benjamindavid), from [Dukt](http://dukt.net/).
