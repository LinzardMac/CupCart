<?php

/**
 * Taxonomy type API.
*/
class TaxonomyType extends Entity
{
    /**
     * @var array Loaded taxonomy types.
    */
    private static $types = array();
    
    /**
     * @var string Name of the taxonomy type.
    */
    public $name;
    
    /**
     * Loads all taxonomies into memory.
    */
    public static function loadAll()
    {
        self::$types = Entity::getByType(0, 0, 'TaxonomyType');
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
            if ($type->name == $name)
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
}