<?php

namespace Dukt\Videos\YouTube;

use Dukt\Videos\Common\AbstractVideo;

class Video extends AbstractVideo
{
    var $embedUrl =  "http://www.youtube.com/embed/%s?wmode=transparent";
    
    public function instantiate($xml)
    {
        // extract videoId

        $videoId = substr($xml->id, strrpos($xml->id, ":") + 1);


        $yt = $xml->children('http://gdata.youtube.com/schemas/2007');
        $media = $xml->children('http://search.yahoo.com/mrss/');
        $player = $media->group->player->attributes();
        
        
        // statistics
        
        $statistics_view_count =  0;
        
        if($yt->statistics)
        {
            $statistics = $yt->statistics->attributes();
                
            if(isset($statistics['viewCount']))
            {
                $statistics_view_count = $statistics['viewCount'];
            }
        }
        

        // duration
        
        $media = $xml->children('http://search.yahoo.com/mrss/');         
        
        $yt = $media->children('http://gdata.youtube.com/schemas/2007');
        
        $duration = $yt->duration->attributes();


        // author
        
        $author = $xml->author;

        // ----------

        $this->id = (string) $videoId;
        $this->url = 'http://youtu.be/'.$videoId;

        $this->title = (string) $xml->title;
        $this->description = (string) $media->group->description[0];
        $this->plays = (int) $statistics_view_count;
        $this->duration = (int) $yt->duration->attributes();

    }
}
