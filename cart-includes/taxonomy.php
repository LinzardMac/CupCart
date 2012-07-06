<?php

/**
 * Taxonomy type API.
*/
class Taxonomy extends Entity
{
    /**
     * @var array Loaded taxonomy types.
    */
    private static $types = array();
    
    /**
     * @var string Name of the taxonomy.
    */
    public $name;
    
    /**
     * Loads all taxonomies into memory.
    */
    public static function loadAll()
    {
        self::$types = Entity::getByType(0, 0, 'Taxonomy');
    }
    
    /**
     * Gets a taxonomy by name (must have been loaded first).
     * @param string $name Taxonomy type name.
     * @return TaxonomyType Taxonomy type found, null otherwise.
    */
    public static function getFromCacheByName($name)
    {
        foreach(self::$types as $type)
        {
            if (strtolower($type->name) == strtolower($name))
                return $type;
        }
        return null;
    }
    
    /**
     * Gets a taxonomy by guid (must have been loaded first).
     * @param int $guid Taxonomy type GUID.
     * @return TaxonomyType Taxonomy type found, null otherwise.
    */
    public static function getFromCacheByGuid($guid)
    {
        foreach(self::$types as $type)
        {
            if ($type->guid == $guid)
                return $type;
        }
        return null;
    }
	
	    /**
     * Print a list of categories to the browser.
    */
    public static function display($args = array())
    {
        $args = Utils::getArgs($args);
        $terms = self::get($args);
        self::_print($terms, 0, $args);
    }
    
    private static function _print($terms, $parent, $args)
    {
        $first = true;
        foreach($terms as $term)
        {
            if ($first)
            {
                echo '<ul class="'.arr::get($args,'ul_class').'">';
                $first = false;
            }
            if ($term->parent == $parent)
            {
                echo '<li class="'.arr::get($args,'li_class').'"><a href="#">'.$term->name.'</a>';
                self::_print($terms, $term->guid, $args);
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
        return Entity::getByMeta("taxonomyGuid", arr::get($args,'taxonomy',0), 0, 0, 'TaxonomyTerm');
    }
}