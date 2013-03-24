<?php

namespace Dukt\Videos\Vimeo;

use Dukt\Vimeo;
use Dukt\Videos\Common\AbstractService;


class Service extends AbstractService
{
    protected $providerClass = "Vimeo";

    public function getName()
    {
        return 'Vimeo';
    }


    public function getVideo($opts)
    {
        // authentication required

        if(!$this->provider) {
            return NULL;
        }


        $consumer_key = $this->provider->consumer->client_id;
        $consumer_secret = $this->provider->consumer->secret;

        $token = $this->provider->token->access_token;
        $token_secret = $this->provider->token->secret;

        $vimeo = new Vimeo($consumer_key, $consumer_secret);
        $vimeo->setToken($token, $token_secret);
        
        $method = 'vimeo.videos.getInfo';

        $params = array();
        $params['video_id'] = $opts['id'];

        $r = $vimeo->call($method, $params);

        $video = $r->video;

        $video = new Video();
        $video->instantiate($r->video[0]);

        return $video;
    }

    public function getFavorites()
    {
        // authentication required

        if(!$this->provider) {
            return NULL;
        }

        $consumer_key = $this->provider->consumer->client_id;
        $consumer_secret = $this->provider->consumer->secret;

        $token = $this->provider->token->access_token;
        $token_secret = $this->provider->token->secret;

        $vimeo = new Vimeo($consumer_key, $consumer_secret);
        $vimeo->setToken($token, $token_secret);
        
        $method = 'vimeo.videos.getLikes';

        $params = array();
        $params['full_response'] = 1;

        $r = $vimeo->call($method, $params);

        $responseVideos = $r->videos->video;

        $videos = array();


        foreach($responseVideos as $responseVideo)
        {
            $video = new Video();
            $video->instantiate($responseVideo);

            array_push($videos, $video);
        }

        return $videos;        
    }

    public function getUploads()
    {
        // authentication required

        if(!$this->provider) {
            return NULL;
        }

        $consumer_key = $this->provider->consumer->client_id;
        $consumer_secret = $this->provider->consumer->secret;

        $token = $this->provider->token->access_token;
        $token_secret = $this->provider->token->secret;

        $vimeo = new Vimeo($consumer_key, $consumer_secret);
        $vimeo->setToken($token, $token_secret);
        
        $method = 'vimeo.videos.getUploaded';

        $params = array();
        $params['full_response'] = 1;

        $r = $vimeo->call($method, $params);

        $responseVideos = $r->videos->video;

        $videos = array();


        foreach($responseVideos as $responseVideo)
        {
            $video = new Video();
            $video->instantiate($responseVideo);

            array_push($videos, $video);
        }

        return $videos;        
    }

    public function getUserInfos()
    {
        // authentication required

        if(!$this->provider) {
            return NULL;
        }


        $consumer_key = $this->provider->consumer->client_id;
        $consumer_secret = $this->provider->consumer->secret;

        $token = $this->provider->token->access_token;
        $token_secret = $this->provider->token->secret;

        $vimeo = new Vimeo($consumer_key, $consumer_secret);
        $vimeo->setToken($token, $token_secret);

        
        $method = 'vimeo.people.getInfo';

        $params = array();

        $r = $vimeo->call($method, $params);


        return $r;
    }

    public function getVideoId($url)
    {

        // check if url works with this service and extract video_id
        $video_id = false;

        $regexp = array('/^https?:\/\/(www\.)?vimeo\.com\/([0-9]*)/', 2);


        if(preg_match($regexp[0], $url, $matches, PREG_OFFSET_CAPTURE) > 0)
        {

            // regexp match key

            $match_key = $regexp[1];


            // define video id

            $video_id = $matches[$match_key][0];


            // Fixes the youtube &feature_gdata bug

            if(strpos($video_id, "&"))
            {
                $video_id = substr($video_id, 0, strpos($video_id, "&"));
            }
        }

        // here we should have a valid video_id or false if service not matching

        return $video_id;
    }
    
    public function setProvider(\OAuth\Provider\Vimeo $provider)
    {
        $this->provider = $provider;
    }

    public function metadata($video_id)
    {

    }
    
}
