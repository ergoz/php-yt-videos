<?php

namespace Dukt\Videos\Common;

/**
 * Helper class
 */
class Helper
{
    public static function getGatewayClassName($shortName)
    {
        if (0 === strpos($shortName, '\\')) {
            return $shortName;
        }

        // replace underscores with namespace marker, PSR-0 style
        $shortName = str_replace('_', '\\', $shortName);
        if (false === strpos($shortName, '\\')) {
            $shortName .= '\\';
        }

        return '\\Dukt\\Videos\\'.$shortName.'Service';
    }
}
