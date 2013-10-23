<?php

namespace Dukt\Videos\YouTube;

use Dukt\Videos\Common\AbstractVideo;

class Video extends AbstractVideo
{
    protected $embedFormat =  "http://www.youtube.com/embed/%s?wmode=transparent";
    var $boolParameters = array('autohide', 'cc_load_policy', 'controls', 'disablekb', 'fs', 'modestbranding', 'rel', 'showinfo');

    public function instantiate($xml)
    {
        // extract videoId

        $this->systemId = (string) $xml->id;

        if(empty($this->systemId)) {
            return null;
        }

        $videoUrl = false;

        $links = $xml->link;

        foreach($links as $link) {
            if($link['rel'] == 'alternate') {
                $videoUrl = (string) $link['href'];
            }
        }

        $videoId = substr($videoUrl, (strrpos($videoUrl, '?v=') + 3));

        if(strpos($videoId, "&") !== false) {
            $videoId = substr($videoId, 0, strpos($videoId, "&"));
        }

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

        //$videoId = Service::getVideoId($videoUrl);

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

        if(isset($yt->duration))
        {
            $durationAttributes = $yt->duration->attributes();

            $duration = $durationAttributes;

            $this->durationSeconds = (int) $duration;

            $this->duration = $this->getDuration("%m:%s");
        }

        // author

        $author = $xml->author;

        // ----------

        // basics

        $this->id              = (string) $videoId;
        $this->url             = 'http://youtu.be/'.$videoId;
        $this->service         = "YouTube";
        $this->serviceSlug     = "youtube";
        $this->serviceClass    = "YouTube";
        $this->serviceName     = "YouTube";
        $this->date            = strtotime($xml->published);
        $this->plays           = (int) $statistics_view_count;



        // author

        $this->authorId      = (string) $author->uri;
        $this->authorId     = substr($this->authorId, strrpos($this->authorId, '/') + 1);

        $this->authorName      = (string) $author->name;
        $this->authorUrl       = "http://youtube.com/user/".$author->name;
        $this->authorUsername  = (string) $author->name;


        // thumbnails
        if(count($media->group->thumbnail) > 0)
        {
            $this->thumbnail       = (string) $media->group->thumbnail[1]->attributes();
            $this->thumbnailLarge  = (string) $media->group->thumbnail[2]->attributes();

            $this->thumbnails = array(
                    (string) $media->group->thumbnail[0]->attributes(),
                    (string) $media->group->thumbnail[1]->attributes(),
                    (string) $media->group->thumbnail[2]->attributes(),
                    (string) $media->group->thumbnail[3]->attributes(),
                );

        }

        $this->title           = (string) $xml->title;
        $this->description     = (string) $media->group->description[0];


        $this->embedUrl = $this->getEmbedUrl();
        $this->embedHtml = $this->getEmbedHtml();
    }
}
