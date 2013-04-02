<?php

namespace Dukt\Videos\Vimeo;

use Dukt\Vimeo;
use Dukt\Videos\Common\AbstractService;


class Service extends AbstractService
{
    protected $providerClass = "Vimeo";

    // --------------------------------------------------------------------

    public function getName()
    {
        return 'Vimeo';
    }

    // --------------------------------------------------------------------

    public function getVideo($opts)
    {
        // authentication required

        if(!$this->provider) {
            return NULL;
        }

        $api = $this->api();
        
        $method = 'vimeo.videos.getInfo';

        $params = array();
        $params['video_id'] = $opts['id'];

        $r = $api->call($method, $params);

        $video = $r->video;

        $video = new Video();
        $video->instantiate($r->video[0]);

        return $video;
    }

    // --------------------------------------------------------------------

    public function getFavorites($params)
    {
        // authentication required

        if(!$this->provider) {
            return NULL;
        }

        $api = $this->api();
        
        $method = 'vimeo.videos.getLikes';

        $query = array();
        $query['full_response'] = 1;

        if(isset($params['page']))
        {
            $query['page'] = $params['page'];    
        }

        if(isset($params['perPage']))
        {
            $query['per_page'] = $params['perPage'];
        }

        $r = $api->call($method, $query);

        return $this->extractVideos($r);      
    }

    // --------------------------------------------------------------------

    public function getUploads($params = array())
    {
        // authentication required

        if(!$this->provider) {
            return NULL;
        }

        $api = $this->api();
        
        $method = 'vimeo.videos.getUploaded';

        $query = array();
        $query['full_response'] = 1;
        $query['page'] = $params['page'];
        $query['per_page'] = $params['perPage'];

        $r = $api->call($method, $query);

        return $this->extractVideos($r);       
    }
    
    // --------------------------------------------------------------------
    
    public function search($params = array())
    {
        // authentication required

        if(!$this->provider) {
            return NULL;
        }

        $api = $this->api();
        
        $method = 'vimeo.videos.search';

        $query = array();
        $query['full_response'] = 1;
        $query['page'] = $params['page'];
        $query['per_page'] = $params['perPage'];
        $query['query'] = $params['q'];

        $r = $api->call($method, $query);

        return $this->extractVideos($r);   
    }

    // --------------------------------------------------------------------

    public function getUserInfos()
    {
        // authentication required

        if(!$this->provider) {
            return NULL;
        }

        $api = $this->api();
        
        $method = 'vimeo.people.getInfo';

        $params = array();

        $r = $api->call($method, $params);

        return $r;
    }

    // --------------------------------------------------------------------

    public static function getVideoId($url)
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
    
    // --------------------------------------------------------------------

    public function setProvider(\OAuth\Provider\Vimeo $provider)
    {
        $this->provider = $provider;
    }

    // --------------------------------------------------------------------

    public function metadata($video_id)
    {

    }
    
    // --------------------------------------------------------------------

    function isFavorite($params)
    {
        // authentication required

        if(!$this->provider) {
            return NULL;
        }

        $api = $this->api();
        
        $method = 'vimeo.videos.getInfo';

        $params['video_id'] = $params['id'];

        $r = $api->call($method, $params);

        if($r->video[0]->is_like == 1)
        {
            return true;
        }

        return false;
    }
    // --------------------------------------------------------------------

    private function api()
    {
        $consumer_key = $this->provider->consumer->client_id;
        $consumer_secret = $this->provider->consumer->secret;

        $token = $this->provider->token->access_token;
        $token_secret = $this->provider->token->secret;

        $vimeo = new Vimeo($consumer_key, $consumer_secret);
        $vimeo->setToken($token, $token_secret);

        return $vimeo;
    }

    // --------------------------------------------------------------------

    private function extractVideos($r)
    {
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
}
