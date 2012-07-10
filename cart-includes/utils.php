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
	{
            $parsed = array();
	    parse_str($args, $parsed);
	    return $parsed;
	}
        return $args;
    }
	
	/**
	 * Determines if the specified class is a child of the specified parent class.
	 * @param string $class Name of the child class.
	 * @param string $parent Name of the parent class.
	 * @return bool
	*/
	public static function classExtends($class, $parent)
	{
		if (!class_exists($class))
			return false;
		if (!class_exists($parent))
			return false;
		$testClass = $class;
		while (($testParent = get_parent_class($testClass)) != false)
		{
			if (strtolower($testParent) == strtolower($parent))
			{
				return true;
			}
			$testClass = $testParent;
		}
		return false;
	}
}