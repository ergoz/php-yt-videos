<?php

namespace Dukt\Videos\YouTube;

use Dukt\Videos\Common\AbstractVideo;

class Video extends AbstractVideo
{
    public $title;

    public function __construct($xml)
    {
        $this->title = (string) $xml->title;;
    }
}
