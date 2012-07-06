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
        $cats = self::get($args);
        echo '<ul class="categories">';
        foreach($cats as $cat)
        {
            
        }
        echo '</ul>';
    }
    
    /**
     * Gets an array of categories.
     * @return array
    */
    public static function get($args = array())
    {
        $args = Utils::getArgs($args);
        $ret = array();
        return $ret;
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