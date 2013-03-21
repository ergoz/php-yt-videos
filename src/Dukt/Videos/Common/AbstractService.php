<?php

namespace Dukt\Videos\Common;


abstract class AbstractService implements ServiceInterface
{

    protected $provider;

    public function __construct()
    {
       // $this->provider = $provider;
    }


    public function getShortName()
    {
        $sn = Helper::getServiceShortName(get_class($this));
        
        return $sn;
    }

    public function get_video($video_url)
    {
        
    }

    public function get_embed($video_id, $user_embed_opts)
    {

    }


}
