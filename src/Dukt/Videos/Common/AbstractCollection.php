<?php

namespace Dukt\Videos\Common;

// use Symfony\Component\HttpFoundation\ParameterBag;

abstract class AbstractCollection
{
    public $id;
    public $title;

    public function getTitle()
    {
        return $this->title;
    }
}
