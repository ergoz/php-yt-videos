<?php

namespace Dukt\Videos\Common;

// use Symfony\Component\HttpFoundation\ParameterBag;

abstract class AbstractVideo implements VideoInterface
{
    public function getTitle()
    {
        return $this->title;
    }
}
