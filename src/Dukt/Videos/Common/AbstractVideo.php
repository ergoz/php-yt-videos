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
}
