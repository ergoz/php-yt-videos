<?php

namespace Dukt\Videos\Vimeo;

use Dukt\Videos\Common\AbstractVideo;

class Video extends AbstractVideo
{
    public $title;

    public function __construct($video)
    {
        $this->title = $video->title;
    }
}
