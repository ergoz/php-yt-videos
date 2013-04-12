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

$params['url'] = "http://www.youtube.com/watch?v=0ZUvQ5h-TCA";

$video = $service->videoFromUrl($params);

echo $video->getTitle();
echo $video->getEmbed();
```

## Service Methods

The main methods implemented by video services are:

* `video($params)` - Get video from its ID _(params : id)_
* `videoFromUrl($params)` - Get video from its URL _(params : url)_
* `search($params)` - Search videos _(params : q, page, perPage)_
* `uploads($params)` - Get uploaded videos _(params : page, perPage)_
* `userInfos()` - Get current user infos

### Favorites

* `favorites($params)` - Get favorite videos _(params : page, perPage)_
* `favoriteAdd($params)` - Add video to favorites _(params : id)_
* `favoriteRemove($params)` - Remove Video from favorites _(params : id)_
* `isFavorite($params)` - Checks if a video is a favorite _(params : id)_

### Playlists

* `playlists($params)` - Get playlists _(params : id)_
* `playlistVideos($params)` - Get videos for given playlist _(params : id)_
* `playlistCreate($params)` - Create a playlist _(params : title, description, videoId)_
* `playlistDelete($params)` - Delete a playlist _(params : id)_
* `playlistAddVideo($params)` - Add a video to a playlist _(params : collectionId, videoId)_
* `playlistRemoveVideo($params)` - Remove a video from a playlist _(params : collectionId, videoId, collectionEntryId)_

## Video Methods

The main methods implemented by video are:

* `getEmbed($options)` - Get the video embed

## OAuth Providers

Here is how to create an OAuth provider and use it with a service :

```php

// Create the OAuth provider

$providerClass = 'YouTube';

$provider = \OAuth\OAuth::provider($providerClass, array(
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

$service = Dukt\Videos\Common\ServiceFactory::create($providerClass);
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

$providerClass = 'YouTube';

$provider = \OAuth\OAuth::provider($providerClass, array(
    'id' => CLIENT_ID,
    'secret' => CLIENT_SECRET,
    'redirect_url' => REDIRECT_URL
));    

$provider->setToken($token);


// Create video service

$service = Dukt\Videos\Common\ServiceFactory::create($providerClass);
$service->setProvider($provider);
```

Dukt Videos relies on [dukt/oauth](https://github.com/dukt/oauth), which is a fork of [chrisnharvey/oauth](https://github.com/chrisnharvey/oauth) to which we added Vimeo OAuth provider and some other tweaks. We will be using chrisnharvey's version as soon as we get out of alpha.


## Example Application

The documentation is not complete yet but you can use the example application provided in this package in order to understand how Dukt Videos works. The example application handles OAuth authentication for you. Once authenticated, you'll be able to run queries on YouTube and Vimeo.

You can run it using PHP's built in web server (PHP 5.4+):

    $ php composer.phar update --dev
    $ php -S localhost:8000 -t example/


## Add-Ons

There are already add-ons that you can download which are using dukt/videos library as one of their core features. Feel free to add your project to the list and ask for a pull request. Add-ons available :

- [Dukt Videos for ExpressionEngine](http://dukt.net/add-ons/expressionengine/videos)
- Dukt Videos for Craft *(Coming soon)*

## Feedback

**Please provide feedback!** We want to make this library useful in as many projects as possible.
Please raise a Github issue, and point out what you do and don't like, or fork the project and make
suggestions. **No issue is too small.**

This plugin is actively maintained by [Benjamin David](https://github.com/benjamindavid), from [Dukt](http://dukt.net/).
