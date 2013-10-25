<?php

namespace Dukt\Videos\Vimeo;

use Dukt\Vimeo;
use Dukt\Videos\Common\AbstractService;


class Service extends AbstractService
{
    public $providerClass = "Vimeo";
    public $name = "Vimeo";
    public $handle = "vimeo";

    // --------------------------------------------------------------------

    public function supportsRefresh()
    {
        return false;
    }

    // --------------------------------------------------------------------

    public function supportsOwnVideoLike()
    {
        return false;
    }

    // --------------------------------------------------------------------

    public function video($opts)
    {
        // authentication required

        if(!$this->provider) {
            return NULL;
        }

        $api = $this->api();

        $method = 'vimeo.videos.getInfo';

        $params = array();
        $params['video_id'] = $opts['id'];

        $video = new Video();


        $r = $api->call($method, $params);

        $video->instantiate($r->video[0]);


        return $video;
    }

    // --------------------------------------------------------------------

    public function channels($params = array())
    {

        // authentication required

        if(!$this->provider) {
            return NULL;
        }

        $api = $this->api();

        $method = 'vimeo.channels.getAll';

        $query = $this->queryFromParams($params);

        $r = $api->call($method, $query);


        $collections = $this->extractCollections($r->channels->channel, 'channel');

        return $collections;
    }

    // --------------------------------------------------------------------

    public function channel($params = array())
    {
        // authentication required

        if(!$this->provider) {
            return NULL;
        }

        $api = $this->api();

        $method = 'vimeo.channels.getVideos';

        $query = $this->queryFromParams($params);
        $query['channel_id'] = $params['id'];

        $r = $api->call($method, $query);

        return $this->extractVideos($r);
        //return $r;
    }

    // --------------------------------------------------------------------

    public function favorites($params = array())
    {
        // authentication required

        if(!$this->provider) {
            return NULL;
        }

        $api = $this->api();

        $method = 'vimeo.videos.getLikes';

        $query = $this->queryFromParams($params);

        $r = $api->call($method, $query);

        return $this->extractVideos($r);
    }

    // --------------------------------------------------------------------

    public function uploads($params = array())
    {
        // authentication required

        if(!$this->provider) {
            return NULL;
        }

        $api = $this->api();

        $method = 'vimeo.videos.getUploaded';


        $query = $this->queryFromParams($params);


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

        $query = $this->queryFromParams($params);

        $query['query'] = $params['q'];

        $r = $api->call($method, $query);

        return $this->extractVideos($r);
    }

    public function queryFromParams($params = array())
    {
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

        return $query;
    }

    // --------------------------------------------------------------------

    public function userInfos()
    {
        // authentication required

        if(!$this->provider) {
            return NULL;
        }

        $api = $this->api();

        $method = 'vimeo.people.getInfo';

        $params = array();

        $r = $api->call($method, $params);

        return $this->extractUserInfos($r);
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

        if (isset($r->video[0]->is_like)) {
            if ($r->video[0]->is_like == 1) {
                return true;
            }
        }

        return false;
    }

    // --------------------------------------------------------------------

    /**
     * Add favorite
     *
     * @access  public
     * @param   string
     * @return  void
     */
    public function favoriteAdd($params)
    {
        // authentication required

        if(!$this->provider) {
            return NULL;
        }

        $api = $this->api();

        $method = 'vimeo.videos.setLike';

        $params['video_id'] = $params['id'];
        $params['like'] = 1;

        $r = $api->call($method, $params);
    }

    // --------------------------------------------------------------------

    /**
     * Remove favorite
     *
     * @access  public
     * @param   string
     * @return  void
     */
    public function favoriteRemove($params)
    {
        // authentication required

        if(!$this->provider) {
            return NULL;
        }

        $api = $this->api();

        $method = 'vimeo.videos.setLike';

        $params['video_id'] = $params['id'];
        $params['like'] = 0;

        $r = $api->call($method, $params);
    }

    // --------------------------------------------------------------------

    public function albums($params = array())
    {

        // authentication required

        if(!$this->provider) {
            return NULL;
        }

        $api = $this->api();

        $method = 'vimeo.albums.getAll';

        $query = $this->queryFromParams();

        $r = $api->call($method, $query);

        return $this->extractCollections($r->albums->album, 'album');
        //return $r;
    }

    // --------------------------------------------------------------------

    public function playlists($params = array())
    {

        // authentication required

        if(!$this->provider) {
            return NULL;
        }

        $api = $this->api();

        $method = 'vimeo.albums.getAll';

        $query = $this->queryFromParams($params);

        $r = $api->call($method, $query);

        return $this->extractCollections($r->albums->album, 'album');
        //return $r;
    }


    // --------------------------------------------------------------------

    public function album($params = array())
    {
        // authentication required

        if(!$this->provider) {
            return NULL;
        }

        $api = $this->api();

        $method = 'vimeo.albums.getVideos';

        $query = $this->queryFromParams($params);

        $query['album_id'] = $params['id'];

        $r = $api->call($method, $query);

        return $this->extractVideos($r);
        //return $r;
    }

    // --------------------------------------------------------------------

    public function playlistCreate($params = array())
    {
        // authentication required

        if(!$this->provider) {
            return NULL;
        }

        $api = $this->api();

        $method = 'vimeo.albums.create';

        $query = array();
        $query['title'] = $params['title'];
        $query['description'] = $params['description'];
        $query['video_id'] = $params['videoId'];

        $r = $api->call($method, $query);

        return $r;
    }

    // --------------------------------------------------------------------

    public function playlistDelete($params = array())
    {
        // authentication required

        if(!$this->provider) {
            return NULL;
        }

        $api = $this->api();

        $method = 'vimeo.albums.delete';

        $query = array();
        $query['album_id'] = $params['id'];

        $r = $api->call($method, $query);

        return $r;
    }


    // --------------------------------------------------------------------

    public function playlistAddVideo($params = array())
    {
        // authentication required

        if(!$this->provider) {
            return NULL;
        }

        $api = $this->api();

        $method = 'vimeo.albums.addVideo';

        $query = array();
        $query['album_id'] = $params['collectionId'];
        $query['video_id'] = $params['videoId'];

        $r = $api->call($method, $query);

        return $r;
    }


    // --------------------------------------------------------------------

    public function playlistRemoveVideo($params = array())
    {
        // authentication required

        if(!$this->provider) {
            return NULL;
        }

        $api = $this->api();

        $method = 'vimeo.albums.removeVideo';

        $query = array();
        $query['album_id'] = $params['collectionId'];
        $query['video_id'] = $params['videoId'];

        $r = $api->call($method, $query);

        return $r;
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

    // --------------------------------------------------------------------

    private function extractCollections($r, $type='album')
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

    // --------------------------------------------------------------------

    private function extractUserInfos($r)
    {
        $response = $r->person;

        $userInfos = new UserInfos();
        $userInfos->instantiate($response);

        return $userInfos;
    }
}

