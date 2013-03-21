<?php

namespace Dukt\Videos\Vimeo;

use Dukt\Videos\Common\AbstractService;

class Service extends AbstractService
{
    public function getName()
    {
        return 'Vimeo';
    }

    public function metadata($video_id)
    {

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
    
}
