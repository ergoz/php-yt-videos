<?php

namespace Dukt\Videos\Common;

// use Symfony\Component\HttpFoundation\ParameterBag;

abstract class AbstractVideo implements VideoInterface
{
    public $id;
    public $url;
    public $title;
    public $plays;
    public $duration;
    public $description;

    public function getTitle()
    {
        return $this->title;
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
