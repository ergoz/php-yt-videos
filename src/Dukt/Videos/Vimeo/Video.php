<?php

namespace Dukt\Videos\Vimeo;

use Dukt\Videos\Common\AbstractVideo;

class Video extends AbstractVideo
{
    var $embedUrl =  "http://player.vimeo.com/video/%s";
    
    public function __construct($videoResponse)
    {
        $this->id = $videoResponse->id;
        $this->url = 'http://vimeo.com/'.$videoResponse->id;
        $this->title = $videoResponse->title;
        $this->description = $videoResponse->description;
        $this->plays = $videoResponse->number_of_plays;
        $this->duration = $videoResponse->duration;
    }
}
