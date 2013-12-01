<?php

namespace Dukt\Videos\YouTube;

use Dukt\Videos\Common\AbstractService;

class Service extends AbstractService
{
    public $providerClass = "YouTube";
    public $name          = "YouTube";
    public $handle        = "youtube";

    /**
     * Parameters
     */
    public function getDefaultParameters()
    {
        $parentSettings = parent::getDefaultParameters();

        $settings = array(
                'developerKey' => array(
                        'required' => true,
                        'label'    => 'Developer Key',
                        'default'    => ''
                    )
            );

        return array_merge($parentSettings, $settings);
    }

    public function getDeveloperKey()
    {
        return $this->getParameter('developerKey');
    }

    public function setDeveloperKey($value)
    {
        return $this->setParameter('developerKey', $value);
    }

    /**
     * Set Provider
     */
    public function setProvider(\OAuth\Provider\YouTube $provider)
    {
        $this->provider = $provider;
    }

    /**
    * API
    */
    protected function api($url, $params = array(), $method='get')
    {
        $developerKey = $this->getDeveloperKey();

        if(is_array($params)) {
            $params['access_token'] = $this->provider->token->access_token;
            $params['key'] = $developerKey;
            $params['v'] = 2;
        }

        $url = 'https://gdata.youtube.com/feeds/api/'.$url;

        if($method=="get") {
            $url .= '?'.http_build_query($params);
        }

        $curl = curl_init();

        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt ($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array(
                'Authorization:Bearer '.$this->provider->token->access_token,
                'Content-Type:application/atom+xml',
                'X-GData-Key:key='.$developerKey
            ));

        if($method=="post") {
            curl_setopt ($curl, CURLOPT_POST, true);
            curl_setopt ($curl, CURLOPT_POSTFIELDS, $params);
        }

        if($method=='delete') {
            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'DELETE');
        }

        $result = curl_exec($curl);
        $curlInfo = curl_getinfo($curl);

        curl_close ($curl);

        if($curlInfo['http_code'] !== 200) {
            $error = trim(strip_tags($result));

            throw new \Exception($error);
        }

        if($method != 'delete') {
            $xml_obj = simplexml_load_string($result);

            if(isset($xml_obj->error)) {
                throw new \Exception($xml_obj->error->internalReason);
            }

            return $xml_obj;
        }

        return true;
    }

    /**
    * Get Video ID
    */
    public static function getVideoId($url)
    {
        // check if url works with this service and extract video_id

        $video_id = false;

        $regexp = array('/^https?:\/\/(www\.youtube\.com|youtube\.com|youtu\.be).*\/(watch\?v=)?(.*)/', 3);



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
        if(!$this->provider) {
            return NULL;
        }

        if(empty($id)) {
            throw new \Exception('The video ID is required. (empty found)');
        }

        $query = $this->_queryFromParams($params);

        $r = $this->api('videos/'.$id, $query);

        $video = new Video();

        $video->instantiate($r);

        return $video;
    }

    /**
    * Get Videos
    */
    public function _queryFromParams($params = array())
    {
        $query = array();

        if(isset($params['page']) && isset($params['perPage'])) {
            $startIndex = $params['page'];

            if($startIndex > 1) {
                $startIndex = (($params['page'] - 1) * $params['perPage']) + 1;
            }

            $query = array(
                'start-index' => $startIndex,
                'max-results' => $params['perPage'],
            );
        }

        return $query;
    }

    public function getVideosSearch($params = array())
    {
        $query = $this->_queryFromParams($params);

        $query['q'] = $params['q'];

        return $this->_getVideosRequest('videos', $query);
    }

    public function getVideosFavorites($params = array())
    {
        $query = $this->_queryFromParams($params);

        return $this->_getVideosRequest('users/default/favorites', $query);
    }

    public function getVideosUploads($params = array())
    {
        $query = $this->_queryFromParams($params);

        return $this->_getVideosRequest('users/default/uploads', $query);
    }

    public function getVideosExplore($params = array())
    {
        $query = $this->_queryFromParams($params);

        return $this->_getVideosRequest('standardfeeds/most_popular', $query);
    }

    public function getVideosHistory($params = array())
    {
        $query = $this->_queryFromParams($params);

        return $this->_getVideosRequest('users/default/watch_history', $params, false);
    }

    public function getVideosPlaylist($params = array())
    {
        $query = $this->_queryFromParams($params);

        return $this->_getVideosRequest('playlists/'.$params['id'], $query);
    }

    /**
    * Get Collections
    */
    public function getCollectionsPlaylists($params = array())
    {

        if(!$this->provider) {
            return NULL;
        }

        $query = array();

        $r = $this->api('users/default/playlists', $query);

        return $this->extractCollections($r);
    }

    /**
    * Get UserInfos
    */
    public function getUserInfos()
    {
        if(!$this->provider) {
            return NULL;
        }

        $r = $this->api('users/default');

        $userInfos = new UserInfos();

        $userInfos->instantiate($r);

        return $userInfos;

        return new UserInfos($response);
    }

    /**
    * Extract Objects
    */
    protected function extractVideos($r)
    {
        $videos = array();

        foreach($r->entry as $v) {

            $video = new Video();
            $video->instantiate($v);

            array_push($videos, $video);
        }

        return $videos;
    }

    protected function extractCollections($r)
    {
        $collections = array();

        foreach($r->entry as $v) {
            $collection = new Collection();
            $collection->instantiate($v);

            array_push($collections, $collection);
        }

        return $collections;
    }

    /**
    * Supports
    */
    public function supportsRefresh()
    {
        return true;
    }

    public function supportsOwnVideoLike()
    {
        return true;
    }
}
