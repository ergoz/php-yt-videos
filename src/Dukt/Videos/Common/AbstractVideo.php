<?php

namespace Dukt\Videos\Common;

// use Symfony\Component\HttpFoundation\ParameterBag;

abstract class AbstractVideo implements VideoInterface
{
    public $id;
    public $url;
    public $service;
    public $serviceSlug;
    public $serviceClass;
    public $serviceName;
    public $date;
    public $plays;
    public $duration;
    public $durationSeconds;
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
        $sec = $this->durationSeconds;

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
            $r = $this->durationSeconds;
        }

        return $r;
    }

    public function getEmbedUrl()
    {
        $url = sprintf($this->embedFormat, $this->id);

        return $url;
    }

    public function getEmbedHtml($options = array())
    {
        $boolParameters = array('disable_size', 'autoplay', 'loop');

        $boolParameters = array_merge($boolParameters, $this->boolParameters);

        foreach($options as $k => $o) {
            foreach($boolParameters as $k2) {
                if($k == $k2) {
                    if($o === 1 || $o === "1" || $o === true || $o === "yes") {
                        $options[$k] = 1;
                    }

                    if($o === 0 || $o === "0" || $o === false || $o === "no") {
                        $options[$k] = 0;
                    }
                }
            }
        }

        $queryMark = '?';

        if(strpos($this->embedFormat, "?") !== false) {
            $queryMark = "&";
        }

        $extraParameters = "";

        $disableSize = false;

        if(isset($options['disable_size'])) {
            $disableSize = $options['disable_size'];
        }

        if(!$disableSize)
        {
            if(isset($options['width'])) {
                $width = $options['width'];
                $extraParameters .= 'width="'.$width.'" ';
                unset($options['width']);
            }

            if(isset($options['height'])) {
                $height = $options['height'];
                $extraParameters .= 'height="'.$height.'" ';
                unset($options['height']);
            }
        }

        $options = http_build_query($options);

        $format = '<iframe src="'.$this->embedFormat.$queryMark.$options.'" '.$extraParameters.' frameborder="0" allowfullscreen="true" allowscriptaccess="true"></iframe>';

        $embed = sprintf($format, $this->id);

        return $embed;
    }
}
