<?php

/**
 * Template tags and help functions for categories.
*/
class Category
{
    /**
     * Print a list of categories to the browser.
    */
    public static function display($args = array())
    {
        $args = Utils::getArgs($args);
        if (arr::get($args,'taxonomy','') == '') $args['taxonomy'] = self::getTaxonomy()->guid;
		return Taxonomy::display($args);
    }
    
    /**
     * Gets an array of categories.
     * @return array
    */
    public static function get($args = array())
    {
        $args = Utils::getArgs($args);
		if (arr::get($args,'taxonomy','') == '') $args['taxonomy'] = self::getTaxonomy()->guid;
        return Taxonomy::get($args);
    }
    
    /**
     * Gets the categories taxonomy type.
     * @return Taxonomy
    */
    public static function getTaxonomy()
    {
        return Taxonomy::getFromCacheByName("category");
    }
}