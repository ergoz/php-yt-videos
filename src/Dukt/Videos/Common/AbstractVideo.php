<?php

namespace Dukt\Videos\Common;

// use Symfony\Component\HttpFoundation\ParameterBag;

abstract class AbstractVideo implements VideoInterface
{
    public $id;
    public $url;
    public $service;
    public $date;
    public $plays;
    public $duration;
    public $authorName;
    public $authorUrl;
    public $authorUsername;
    public $thumbnail;
    public $thumbnailLarge;
    public $thumbnails;
    public $title;
    public $description;

    public function getDate($format = false)
    {
        if($format)
        {
            return strftime($format, $this->date);
        }
        return $this->date;
    }


    public function getAuthorName()
    {
        return $this->authorName;
    }
    public function getAuthorUrl()
    {
        return $this->authorUrl;
    }

    public function getAuthorUsername()
    {
        return $this->authorUsername;
    }

    public function getThumbnail()
    {
        return $this->thumbnail;
    }
    public function getThumbnailLarge()
    {
        return $this->thumbnailLarge;
    }
    public function getThumbnails()
    {
        return $this->thumbnails;
    }

    // ------------------------------------------------------------------------------

    /**
     * Duration from seconds to h:m:s
     *
     * @access  public
     * @return  array
     */
    public function getDuration($format = false)
    {
        $sec = $this->duration;
        $padHours = true;

        $hms = "";
        $hours = intval(intval($sec) / 3600);

        $hms .= ($padHours)
        ? str_pad($hours, 2, "0", STR_PAD_LEFT). ":"
        : $hours. ":";

        $minutes = intval(($sec / 60) % 60);

        $hms .= str_pad($minutes, 2, "0", STR_PAD_LEFT). ":";

        $seconds = intval($sec % 60);

        $hms .= str_pad($seconds, 2, "0", STR_PAD_LEFT);

        if($format)
        {
            $r = $format;

            $r = str_replace("%h", str_pad($hours, 2, 0, STR_PAD_LEFT), $r);
            $r = str_replace("%m", str_pad($minutes, 2, 0, STR_PAD_LEFT), $r);
            $r = str_replace("%s", str_pad($seconds, 2, 0, STR_PAD_LEFT), $r);
        }
        else
        {
            $r = $this->duration;
        }

        return $r;
    }

    public function getEmbed($options = array())
    {
        $queryMark = '?';

        if(strpos($this->embedUrl, "?") !== false) {
            $queryMark = "&";
        }

        $extraParameters = "";

        if(isset($options['width']))
        {
            $width = $options['width'];
            $extraParameters .= 'width="'.$width.'" ';
            unset($options['width']);
        }

        if(isset($options['height']))
        {
            $height = $options['height'];
            $extraParameters .= 'height="'.$height.'" ';
            unset($options['height']);
        }

        $options = http_build_query($options);

        $format = '<iframe src="'.$this->embedUrl.$queryMark.$options.'" '.$extraParameters.' frameborder="0" allowfullscreen="true" allowscriptaccess="true"></iframe>';

        $embed = sprintf($format, $this->id);

        return $embed;
    }
}
