<?php

/**
 * Array helper class.
*/
class Arr
{
    /**
     * Gets values from an array using the given key.
     * @param array $array The array.
     * @param mixed $key The key.
     * @param mixed $defaultValue Default value.
     * @return mixed
    */
    public static function get($array, $key, $defaultValue = null)
    {
        if (is_array($array) && array_key_exists($key, $array))
            return $array[$key];
        return $defaultValue;
    }
}