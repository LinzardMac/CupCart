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
        self::_printCategories($cats, 0, $args);
    }
    
    private static function _printCategories($cats, $parent, $args)
    {
        $first = true;
        foreach($cats as $cat)
        {
            if ($first)
            {
                echo '<ul class="'.arr::get($args,'ul_class').'">';
                $first = false;
            }
            if ($cat->parent == $parent)
            {
                echo '<li class="'.arr::get($args,'li_class').'"><a href="#">'.$cat->name.'</a>';
                self::_printCategories($cats, $cat->guid, $args);
                echo '</li>';
            }
        }
        if (!$first)
            echo '</ul>';
    }
    
    /**
     * Gets an array of categories.
     * @return array
    */
    public static function get($args = array())
    {
        $args = Utils::getArgs($args);
        return Entity::getByMeta("taxonomyGuid", self::getTaxonomy()->guid, 0, 0, 'TaxonomyTerm');
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