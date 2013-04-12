<?php

namespace Dukt\Videos\Common;

use Guzzle\Http\ClientInterface;
use Symfony\Component\HttpFoundation\Request as HttpRequest;
use Symfony\Component\HttpFoundation\ParameterBag;

abstract class AbstractService implements ServiceInterface
{

    protected $parameters;

    public $provider;

    public function __construct(Provider $provider = null)
    {
       $this->provider = $provider;
    }

    public function initialize(array $parameters = array())
    {
        $this->parameters = new ParameterBag;

        // set default parameters
        foreach ($this->getDefaultParameters() as $key => $value) {
            if (is_array($value)) {
                $this->parameters->set($key, reset($value));
            } else {
                $this->parameters->set($key, $value);
            }
        }

        Helper::initialize($this, $parameters);

        return $this;
    }

    public function isAuthenticated()
    {
        try {
            $r = $this->favorites();

            if($r)
            {
                return true;
            }

            return false;

        } catch(\Exception $e)
        {
            return false;
        }
    }

    public function requestParameters($method)
    {
        $defaults = array(
            'search'              => array('q' => "", 'page' => 1, 'perPage' => 20),
            'favorites'        => array('page' => 1, 'perPage' => 20),
            'isFavorite'          => array('id' => ""),
            'favoriteAdd'         => array('id' => ""),
            'favoriteRemove'      => array('id' => ""),
            'uploads'          => array('page' => 1, 'perPage' => 20),
            'playlistVideos'            => array('id' => "", 'page' => 1, 'perPage' => 20),
            'playlistCreate'      => array('title' => "", 'description' => "", 'videoId' => ""),
            'playlistDelete'      => array('id' => ""),
            'video'            => array('id' => ""),
            'videoFromUrl'            => array('url' => ""),
            'playlistAddVideo'    => array('collectionId' => "", 'videoId' => ""),
            'playlistRemoveVideo' => array('collectionId' => "", 'videoId' => "", 'collectionEntryId' => ""),
        );

        $array = array();

        if(isset($defaults[$method]))
        {
            $array = $defaults[$method];
        }

        return $array;
    }

    public function videoFromUrl($url)
    {
        $url = $url['url'];

        $videoId = $this->getVideoId($url);
        
        if(!$videoId)
        {
            throw new \Exception('Video not found with url given');
        }

        $params['id'] = $videoId;

        $video = $this->video($params);
        
        return $video;
    }

    public function getShortName()
    {
        $sn = Helper::getServiceShortName(get_class($this));
        
        return $sn;
    }
    
    public function getProviderClass()
    {
        return $this->providerClass;
    }

    public function getDefaultParameters()
    {
        return array();
    }

    public function supports($method)
    {
        return method_exists($this, $method);
    }




    public function get_video($video_url)
    {
        
    }

    public function get_embed($video_id, $user_embed_opts)
    {

    }

}
