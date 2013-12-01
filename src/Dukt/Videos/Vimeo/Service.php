<?php

namespace Dukt\Videos\Vimeo;

use Dukt\Vimeo;
use Dukt\Videos\Common\AbstractService;

class Service extends AbstractService
{
    public $providerClass = "Vimeo";
    public $name          = "Vimeo";
    public $handle        = "vimeo";

    /**
    * Set Provider
    */
    public function setProvider(\OAuth\Provider\Vimeo $provider)
    {
        $this->provider = $provider;
    }

    /**
    * API
    */
    protected function api($method, $params)
    {
        $consumer_key = $this->provider->consumer->client_id;
        $consumer_secret = $this->provider->consumer->secret;

        $token = $this->provider->token->access_token;
        $token_secret = $this->provider->token->secret;

        $vimeo = new Vimeo($consumer_key, $consumer_secret);

        $vimeo->setToken($token, $token_secret);

        $r = $vimeo->call($method, $params);

        return $r;
    }

    /**
    * Get Video ID
    */
    public static function getVideoId($url)
    {
        // check if url works with this service and extract video_id

        $video_id = false;

        $regexp = array('/^https?:\/\/(www\.)?vimeo\.com\/([0-9]*)/', 2);

        if(preg_match($regexp[0], $url, $matches, PREG_OFFSET_CAPTURE) > 0) {

            // regexp match key

            $match_key = $regexp[1];


            // define video id

            $video_id = $matches[$match_key][0];


            // Fixes the youtube &feature_gdata bug

            if(strpos($video_id, "&")) {
                $video_id = substr($video_id, 0, strpos($video_id, "&"));
            }
        }

        // here we should have a valid video_id or false if service not matching

        return $video_id;
    }

    /**
    * Get Video
    */
    public function getVideo($id, $params = array())
    {
        // authentication required

        if(!$this->provider) {
            return NULL;
        }

        $method = 'vimeo.videos.getInfo';

        $params = array();
        $params['video_id'] = $id;

        $response = $this->api($method, $params);

        $video = new Video();

        $video->instantiate($response->video[0]);

        return $video;
    }

    /**
    * Get Videos
    */
    public function _queryFromParams($params = array())
    {
        $query = array();

        $query['full_response'] = 1;

        if(isset($params['page'])) {
            $query['page'] = $params['page'];
        }

        if(isset($params['perPage'])) {
            $query['per_page'] = $params['perPage'];
        }

        if(!empty($params['q'])) {
            $query['query'] = $params['q'];
        }

        return $query;
    }

    public function getVideosSearch($params = array())
    {
        $query = $this->_queryFromParams($params);

        return $this->_getVideosRequest('vimeo.videos.search', $query);
    }

    public function getVideosFavorites($params = array())
    {
        $query = $this->_queryFromParams($params);

        return $this->_getVideosRequest('vimeo.videos.getLikes', $query);
    }

    public function getVideosUploads($params = array())
    {
        $query = $this->_queryFromParams($params);

        return $this->_getVideosRequest('vimeo.videos.getUploaded', $query);
    }

    public function getVideosChannel($params = array())
    {
        $query = $this->_queryFromParams($params);

        $query['channel_id'] = $params['id'];

        return $this->_getVideosRequest('vimeo.channels.getVideos', $query);
    }

    public function getVideosAlbum($params = array())
    {
        $query = $this->_queryFromParams($params);

        $query['album_id'] = $params['id'];

        return $this->_getVideosRequest('vimeo.albums.getVideos', $query);
    }

    /**
    * Get Collections
    */
    public function getCollectionsChannels($params = array())
    {

        // authentication required

        if(!$this->provider) {
            return NULL;
        }

        $api = $this->api();

        $method = 'vimeo.channels.getAll';

        $query = $this->_queryFromParams($params);

        $r = $api->call($method, $query);


        $collections = $this->extractCollections($r->channels->channel, 'channel');

        return $collections;
    }

    public function getCollectionsAlbums($params = array())
    {

        // authentication required

        if(!$this->provider) {
            return NULL;
        }

        $api = $this->api();

        $method = 'vimeo.albums.getAll';

        $query = $this->_queryFromParams();

        $r = $api->call($method, $query);

        return $this->extractCollections($r->albums->album, 'album');
        //return $r;
    }

    /**
    * Get UserInfos
    */
    public function getUserInfos()
    {
        if(!$this->provider) {
            return NULL;
        }

        $api = $this->api();

        $method = 'vimeo.people.getInfo';

        $params = array();

        $r = $api->call($method, $params);

        return $this->extractUserInfos($r);
    }

    /**
    * Extract Objects
    */
    protected function extractVideos($r)
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

    protected function extractCollections($r, $type='album')
    {
        $responseCollections = $r;

        $collections = array();


        if(count($responseCollections) == 1) {
            $responseCollections = array($responseCollections);
        }
        foreach($responseCollections as $responseCollection)
        {
            $collection = new Collection();

            $collection->{'instantiate'.ucwords($type)}($responseCollection);

            array_push($collections, $collection);
        }

        return $collections;
    }

    protected function extractUserInfos($r)
    {
        $response = $r->person;

        $userInfos = new UserInfos();
        $userInfos->instantiate($response);

        return $userInfos;
    }

    /**
    * Supports
    */
    public function supportsRefresh()
    {
        return false;
    }

    public function supportsOwnVideoLike()
    {
        return false;
    }
}

