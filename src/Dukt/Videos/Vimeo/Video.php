<?php

namespace Dukt\Videos\Vimeo;

use Dukt\Videos\Common\AbstractVideo;

class Video extends AbstractVideo
{
    var $embedUrl =  "http://player.vimeo.com/video/%s";

    public function instantiate($response)
    {
        // basics

        $this->id              = $response->id;
        $this->url             = 'http://vimeo.com/'.$response->id;
        $this->service         = "Vimeo";
        $this->serviceSlug     = "vimeo";
        $this->serviceClass    = "Vimeo";
        $this->serviceName     = "Vimeo";
        $this->date            = (string) strtotime($response->upload_date);
        $this->plays           = $response->number_of_plays;
        $this->durationSeconds = $response->duration;
        $this->duration        = $this->getDuration("%m:%s");
        $this->title           = $response->title;
        $this->description     = $response->description;


        // author

        $this->authorName      = (string) $response->owner->display_name;
        $this->authorUrl       = (string) $response->owner->profileurl;
        $this->authorUsername  = (string) $response->owner->username;


        // thumbnails

        $this->thumbnail       = (string) $response->thumbnails->thumbnail[0]->_content;
        $this->thumbnailLarge  = end($response->thumbnails->thumbnail)->_content;

        $this->thumbnails = array();

        foreach($response->thumbnails->thumbnail as $k => $thumbnail)
        {
            array_push($this->thumbnails, $thumbnail->_content);
        }
    }
}
