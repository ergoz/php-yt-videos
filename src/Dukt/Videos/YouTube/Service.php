<?php

namespace Dukt\Videos\YouTube;

use Dukt\Videos\Common\AbstractService;

class Service extends AbstractService
{
    protected $providerClass = "YouTube";

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

    public function getUserInfos()
    {
        // authentication required

        if(!$this->provider) {
            return NULL;
        }


        // request

        $url = 'https://www.googleapis.com/oauth2/v1/userinfo?alt=json&'.http_build_query(array(
            'access_token' => $this->provider->token->access_token,

        ));

        $user = json_decode(file_get_contents($url), true);

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

    public function getVideo($opts)
    {
        // authentication required
        
        if(!$this->provider) {
            return NULL;
        }

        $url = 'https://gdata.youtube.com/feeds/api/videos/'.$opts['id'].'?v=2&'.http_build_query(array(
            'refresh_token' => $this->provider->token->refresh_token,
        ));

        $result = file_get_contents($url);
        $xml_obj = simplexml_load_string($result);   

        $video = new Video();
        $video->instantiate($xml_obj);

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
    function addFavorite($params)
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
    function removeFavorite($params)
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

    public function getFavorites($params = array())
    {
        // authentication required
        
        if(!$this->provider) {
            return NULL;
        }

        $query = array();

        if(isset($params['page']) && isset($params['perPage']))
        {
            $query = array(
                'start-index' => $params['page'],
                'max-results' => $params['perPage'],
            );
        }

        $r = $this->apiCall('users/default/favorites', $query);
        
        return $this->extractVideos($r);
    }

    // --------------------------------------------------------------------

    public function getUploads($params = array())
    {
        // authentication required
        
        if(!$this->provider) {
            return NULL;
        }

        $query = array(
            'start-index' => $params['page'],
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


        $query = array(
            'q' => $params['q'],
            'start-index' => $params['page'],
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
            'q' => $params['q'],
            'start-index' => $params['page'],
            'max-results' => $params['perPage'],
        );

        $r = $this->apiCall('users/default/playlists', $query);

        return $this->extractCollections($r);
        
        //return $r;
    }

    // --------------------------------------------------------------------

    public function playlist($params = array())
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

        return $this->extractCollections($r);
        
        //return $r;
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
        }

        $xml_obj = simplexml_load_string($result); 

        return $xml_obj;
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
