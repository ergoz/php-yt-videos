# Dukt Videos

**Universal video library for Vimeo & YouTube APIs and PHP 5.3+**

[![Build Status](https://travis-ci.org/dukt/videos.png?branch=master)](https://travis-ci.org/adrianmacneil/omnipay)

## Creating a service

```
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

## Creating an OAuth Provider

```
$provider = $provider->process(function($url, $token = null) {

    if ($token) {
        $_SESSION['token'] = base64_encode(serialize($token));
    }

    header("Location: {$url}");

    exit;

}, function() {
    return unserialize(base64_decode($_SESSION['token']));
});


// retrieve token and store it somewhere safe

$token = $provider->token();
$token = base64_encode(serialize($token));
```

## Feedback

**Please provide feedback!** We want to make this library useful in as many projects as possible.
Please raise a Github issue, and point out what you do and don't like, or fork the project and make
suggestions. **No issue is too small.**
