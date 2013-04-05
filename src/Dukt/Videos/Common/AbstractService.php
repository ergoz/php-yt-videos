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

    public function isAuthenticated()
    {
        try {
            $this->favorites();
            return true;
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

    public function videoFromUrl($url)
    {
        $url = $url['url'];

        $videoId = $this->getVideoId($url);

        if(!$videoId)
        {
            throw new \Exception('Video not found with url given');
        }

        $params['id'] = $videoId;

        return $this->video($params);
    }



    public function get_video($video_url)
    {
        
    }

    public function get_embed($video_id, $user_embed_opts)
    {

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
        return array(
            'id' => "",
            'secret' => "",
            'token' => ""
        );
    }

    public function getId()
    {
        return $this->getParameter('id');
    }

    public function setId($id)
    {
        return $this->setParameter('id', $id);
    }

    public function getSecret()
    {
        return $this->getParameter('secret');
    }

    public function setSecret($secret)
    {
        return $this->setParameter('secret', $secret);
    }

    public function getToken()
    {
        return $this->getParameter('token');
    }

    public function setToken($token)
    {
        return $this->setParameter('token', $token);
    }

    public function getDeveloperKey()
    {
        return $this->getParameter('developerKey');
    }

    public function setDeveloperKey($value)
    {
        return $this->setParameter('developerKey', $value);
    }



    public function getParameters()
    {
        return $this->parameters->all();
    }

    protected function getParameter($key)
    {
        return $this->parameters->get($key);
    }

    protected function setParameter($key, $value)
    {
        $this->parameters->set($key, $value);

        return $this;
    }

    public function supports($method)
    {
        return method_exists($this, $method);
    }

}
