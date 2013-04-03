<?php

namespace Dukt\Videos\YouTube;

use Dukt\Videos\Common\AbstractVideo;

class Video extends AbstractVideo
{
    protected $embedUrl =  "http://www.youtube.com/embed/%s?wmode=transparent";
    
    public function instantiate($xml)
    {
        // extract videoId

        $videoUrl = (string) $xml->link[0]->attributes()->href[0];
        
        $this->systemId = (string) $xml->id;

        $playlistEntryId = $this->systemId;

        $playlistEntryId = substr($playlistEntryId, strpos($playlistEntryId, 'playlist:') + 9);

        if(strpos($playlistEntryId, ":"))
        {
            $playlistEntryId = substr($playlistEntryId, strpos($playlistEntryId, ':') + 1);
            $this->playlistEntryId = $playlistEntryId;
        }
        else
        {
            $playlistEntryId = NULL;
        }

        

        $videoId = Service::getVideoId($videoUrl);

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
        
        // basics

        $this->id              = (string) $videoId;
        $this->url             = 'http://youtu.be/'.$videoId;
        $this->service         = "YouTube";
        $this->date            = strtotime($xml->published);
        $this->plays           = (int) $statistics_view_count;
        $this->duration        = (int) $duration;


        // author

        $this->authorName      = (string) $author->name;
        $this->authorUrl       = "http://youtube.com/user/".$author->name;
        $this->authorUsername  = (string) $author->name;
        

        // thumbnails

        $this->thumbnail       = (string) $media->group->thumbnail[1]->attributes();
        $this->thumbnailLarge  = (string) $media->group->thumbnail[0]->attributes();

        $this->thumbnails = array(
                (string) $media->group->thumbnail[0]->attributes(),
                (string) $media->group->thumbnail[1]->attributes(),
                (string) $media->group->thumbnail[2]->attributes(),
                (string) $media->group->thumbnail[3]->attributes(),
            );

        $this->title           = (string) $xml->title;
        $this->description     = (string) $media->group->description[0];
    }
}
