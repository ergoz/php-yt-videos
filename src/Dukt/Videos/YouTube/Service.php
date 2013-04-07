<?php

namespace Dukt\Videos\YouTube;

use Dukt\Videos\Common\AbstractService;

class Service extends AbstractService
{
    public $providerClass = "YouTube";

    // --------------------------------------------------------------------

    public function getName()
    {
        return 'YouTube';
    }

    // --------------------------------------------------------------------

    public function getDefaultParameters()
    {
        return array(
            'id' => "",
            'secret' => "",
            'developerKey' => "",
            'token' => ""
        );
    }

    // --------------------------------------------------------------------

    public function userInfos()
    {
        // authentication required

        if(!$this->provider) {
            return NULL;
        }


        // request

        $url = 'https://www.googleapis.com/oauth2/v1/userinfo?alt=json&'.http_build_query(array(
            'access_token' => $this->provider->token->access_token,

        ));

        $user = @json_decode(file_get_contents($url), true);
        
        if(!$user)
        {
            throw new \Exception('Invalid Token');
        }

        return array(
            'uid' => $this->provider->token->uid,
            'name' => $user['name'],
            'email' => $user['email'],
            'location' => null,
            'image' => isset($user['picture']) ? $user['picture'] : null,
            'description' => null,
            'urls' => array(),
        );
    }

    // --------------------------------------------------------------------

    public function video($opts)
    {
        // authentication required
        
        if(!$this->provider) {
            return NULL;
        }

        if(empty($opts['id']))
        {
            throw new \Exception('The video ID is required. (empty found)');
        }

        $query = array();

        if(isset($params['page']) && isset($params['perPage']))
        {
            $query = array(
                'start-index' => $params['page'],
                'max-results' => $params['perPage'],
            );
        }

        $r = $this->apiCall('videos/'.$opts['id'], $query);


        $video = new Video();

        $video->instantiate($r);  
        

        return $video;
    }

    
    // --------------------------------------------------------------------
    
    /**
     * Add favorite
     *
     * @access  public
     * @param   string
     * @return  void
     */
    function favoriteAdd($params)
    {
        // authentication required
        
        if(!$this->provider) {
            return NULL;
        }

        $query = '<?xml version="1.0" encoding="UTF-8"?><entry xmlns="http://www.w3.org/2005/Atom"><id>'.$params['id'].'</id></entry>';


        $r = $this->apiCall('users/default/favorites', $query, 'post');
    }
    
    // --------------------------------------------------------------------
    
    /**
     * Remove favorite
     *
     * @access  public
     * @param   string
     * @return  void
     */
    function favoriteRemove($params)
    {
        // authentication required
        
        if(!$this->provider) {
            return NULL;
        }

        // get favorites

        $r = $this->apiCall('users/default/favorites');
        
        foreach($r->entry as $v)
        {
            $video = new Video();
            $video->instantiate($v);

            if($video->id == $params['id'])
            {
                // favorite found, let's remove it

                $yt = $v->children('http://gdata.youtube.com/schemas/2007');

                $favorite_id = (string) $yt->favoriteId;

                $query = '';

                $r = $this->apiCall('users/default/favorites/'.$favorite_id, $query, 'delete');

            }
        }
    }

    // --------------------------------------------------------------------

    public function favorites($params = array())
    {
        // authentication required
        
        if(!$this->provider) {
            return NULL;
        }

        $query = array();

        if(isset($params['page']) && isset($params['perPage']))
        {
            $startIndex = $params['page'];

            if($startIndex > 1)
            {
                $startIndex = (($params['page'] - 1) * $params['perPage']) + 1;
            }

            $query = array(
                'start-index' => $startIndex,
                'max-results' => $params['perPage'],
            );
        }

        $r = $this->apiCall('users/default/favorites', $query);
        
        return $this->extractVideos($r);
    }

    // --------------------------------------------------------------------

    public function uploads($params = array())
    {
        // authentication required
        
        if(!$this->provider) {
            return NULL;
        }

        $startIndex = $params['page'];

        if($startIndex > 1)
        {
            $startIndex = (($params['page'] - 1) * $params['perPage']) + 1;
        }

        $query = array(
            'start-index' => $startIndex,
            'max-results' => $params['perPage']
        );

        $r = $this->apiCall('users/default/uploads', $query);
    
        return $this->extractVideos($r);
    }

    // --------------------------------------------------------------------
        

    public function search($params = array())
    {
        // authentication required
        
        if(!$this->provider) {
            return NULL;
        }

        $startIndex = $params['page'];

        if($startIndex > 1)
        {
            $startIndex = (($params['page'] - 1) * $params['perPage']) + 1;
        }

        $query = array(
            'q' => $params['q'],
            'start-index' => $startIndex,
            'max-results' => $params['perPage'],
        );

        $r = $this->apiCall('videos', $query);

        return $this->extractVideos($r);
    }


    // --------------------------------------------------------------------

    public function playlists($params = array())
    {
        // authentication required
        
        if(!$this->provider) {
            return NULL;
        }


        $query = array(
            //'q' => $params['q'],
            //'start-index' => $params['page'],
            //'max-results' => $params['perPage'],
        );

        $r = $this->apiCall('users/default/playlists', $query);

        return $this->extractCollections($r);
        
        //return $r;
    }

    // --------------------------------------------------------------------

    public function playlistVideos($params = array())
    {
        // authentication required
        
        if(!$this->provider) {
            return NULL;
        }


        $query = array(
            'start-index' => $params['page'],
            'max-results' => $params['perPage'],
        );

        $r = $this->apiCall('playlists/'.$params['id'], $query);

        return $this->extractVideos($r);
        
        // return $r;
    }

    // --------------------------------------------------------------------

    public function playlistCreate($params = array())
    {

        // authentication required
        
        if(!$this->provider) {
            return NULL;
        }

        $query = '<?xml version="1.0" encoding="UTF-8"?>
<entry xmlns="http://www.w3.org/2005/Atom"
    xmlns:yt="http://gdata.youtube.com/schemas/2007">
  <title type="text">'.$params['title'].'</title>
  <summary>'.$params['description'].'</summary>
</entry>';


        $r = $this->apiCall('users/default/playlists', $query, 'post');
    }


    // --------------------------------------------------------------------

    public function playlistDelete($params = array())
    {
        // authentication required
        
        if(!$this->provider) {
            return NULL;
        }


        $query = array();

        $r = $this->apiCall('users/default/playlists/'.$params['id'], $query, 'delete');
        
        return $r;
    }

    // --------------------------------------------------------------------

    public function playlistAddVideo($params = array())
    {

        // authentication required
        
        if(!$this->provider) {
            return NULL;
        }

        $query = '<?xml version="1.0" encoding="UTF-8"?>
<entry xmlns="http://www.w3.org/2005/Atom"
    xmlns:yt="http://gdata.youtube.com/schemas/2007">
  <id>'.$params['videoId'].'</id>
  <yt:position>1</yt:position>
</entry>';


        $r = $this->apiCall('playlists/'.$params['collectionId'], $query, 'post');

        return $r;
    }


    // --------------------------------------------------------------------

    public function playlistRemoveVideo($params = array())
    {
        // authentication required
        
        if(!$this->provider) {
            return NULL;
        }


        $query = array();

        $r = $this->apiCall('playlists/'.$params['collectionId'].'/'.$params['collectionEntryId'], $query, 'delete');
        
        return $r;
    }


    // --------------------------------------------------------------------

    public static function getVideoId($url)
    {
        // check if url works with this service and extract video_id
        
        $video_id = false;

        $regexp = array('/^https?:\/\/(www\.youtube\.com|youtube\.com|youtu\.be).*\/(watch\?v=)?(.*)/', 3);
        


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

    public function setProvider(\OAuth\Provider\YouTube $provider)
    {
        $this->provider = $provider;
    }

    // --------------------------------------------------------------------

    private function apiCall($url, $params = array(), $method='get')
    {

        $developerKey = $this->provider->developerKey;

        if(is_array($params))
        {
            $params['access_token'] = $this->provider->token->access_token;
            $params['key'] = $developerKey;
            $params['v'] = 2;
        }


        $url = 'https://gdata.youtube.com/feeds/api/'.$url;

        if($method=="get")
        {
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
        
        if($method=="post")
        {
            curl_setopt ($curl, CURLOPT_POST, true);
            curl_setopt ($curl, CURLOPT_POSTFIELDS, $params);
        }

        if($method=='delete')
        {
            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'DELETE');
        }

        $result = curl_exec($curl);
        $curlInfo = curl_getinfo($curl);
        curl_close ($curl);


        if($curlInfo['http_code'] == 401 && strpos($result, "Token invalid") !== false)
        {
            // refresh token
            // $providerParams = array('grant_type' => 'refresh_token');
            // $code = $provider
            // $this->provider->access($code, $providerParams);
            // var_dump($this->provider);
            throw new \Exception('Provider Invalid Token');
        }

        if($method != 'delete')
        {
            $xml_obj = simplexml_load_string($result); 

            if(isset($xml_obj->error))
            {
                throw new \Exception($xml_obj->error->internalReason);
            }

            return $xml_obj;
        }

        return true;
    }

    // --------------------------------------------------------------------

    private function extractVideos($r)
    {
        $videos = array();
        
        foreach($r->entry as $v)
        {
            $video = new Video();
            $video->instantiate($v);

            array_push($videos, $video);
        }

        return $videos;
    }


    // --------------------------------------------------------------------

    private function extractCollections($r)
    {
        $collections = array();
        
        foreach($r->entry as $v)
        {
            $collection = new Collection();
            $collection->instantiate($v);

            array_push($collections, $collection);
        }

        return $collections;
    }

    // --------------------------------------------------------------------

    function isFavorite($params)
    {
        $videos = $this->getFavorites($params);
        
        if(!$videos)
        {
            return false;
        }

        foreach($videos as $v)
        {
            if($v->id == $params['id'])
            {
                return true;
            }
        }

        return false;
    }
}
