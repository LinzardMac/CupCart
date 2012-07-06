<?php

/**
 * Various helper methods.
*/
class Utils
{
    /**
     * Gets an array of arguments from a passed array or string.
     * Strings can be in the query string format.
    */
    public static function getArgs($args = array())
    {
        if (!is_array($args))
            return parse_str($args);
        return $args;
    }
}