<?php

namespace Dukt\Videos\Common;

/**
 * Payment gateway interface
 */
interface ServiceInterface
{
    public function getVideoId($url);
    public function metadata($video);
    // public function connect($lib, $app);
    // public function connect_callback($lib, $app);   
}
