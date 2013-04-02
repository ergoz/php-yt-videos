<?php

namespace Dukt\Videos\Common;

/**
 * Video Service interface
 */
interface ServiceInterface
{
    public function getUserInfos();
    public static function getVideoId($url);
    public function getVideo($options);

    // public function connect($lib, $app);
    // public function connect_callback($lib, $app);   
}
