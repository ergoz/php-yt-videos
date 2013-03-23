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

    public function getVideoId($url)
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
}
