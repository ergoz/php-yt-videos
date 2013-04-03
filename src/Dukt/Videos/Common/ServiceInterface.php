<?php

namespace Dukt\Videos\Common;

/**
 * Video Service interface
 */
interface ServiceInterface
{
    public function userInfos();
    public static function getVideoId($url);
    public function video($options);

    // public function connect($lib, $app);
    // public function connect_callback($lib, $app);   
}
