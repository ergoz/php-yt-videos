<?php

require_once('../vendor/autoload.php');

$service = \Dukt\Videos\Common\ServiceFactory::create('YouTube');

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