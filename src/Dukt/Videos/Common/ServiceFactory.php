<?php

namespace Dukt\Videos\Common;

class ServiceFactory
{
    public static function create($class)
    {
        $class = Helper::getGatewayClassName($class);

        if (!class_exists($class)) {
            throw new \Exception("Class '$class' not found");
        }

        $service = new $class();

        return $service;
    }

    /**
     * Get a list of supported services
     */
    public static function find()
    {

    }
}
