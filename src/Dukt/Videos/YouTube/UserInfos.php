<?php

namespace Dukt\Videos\YouTube;

use Dukt\Videos\Common\AbstractUserInfos;

class UserInfos extends AbstractUserInfos
{
    public function __construct($response)
    {
        $this->instantiate($response);
    }

    public function instantiate($response)
    {
        $this->id = (string) $response->id;
        $this->id = substr($this->id, (strpos($this->id, ":user:") + 6));

        $this->name = (string) $response->author->name;

        // $this->id = $response->id;
        // $this->url = $response->url[0];
        // $this->title = $response->title;
        // $this->totalVideos = $response->total_videos;
    }
}