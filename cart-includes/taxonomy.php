<?php

/**
 * A taxonomy.
*/
class Taxonomy extends Entity
{
    /**
     * @var int [TaxonomyType] GUID.
    */
    public $typeGuid;
    
    /**
     * @var string Name of the taxanomy item.
    */
    public $name;
    
    /**
     * Gets a taxonomy using a taxonomy type and a taxonomy id.
     * @param mixed $type Optional. The taxonomy type. Either a string, an integer or a [TaxonomyType] instance.
     * @param mixed $taxonomy Either a string or an integer identifying the taxonomy.
     * @return Taxonomy A taxonomy if found, null otherwise.
    */
    public static function get($taxonomy, $type = null)
    {
        $taxType = $type;
        if ($taxType != null)
        {
            if (!($taxType instanceof TaxonomyType))
            {
                if (is_numeric($taxType))
                    $taxType = TaxonomyType::getFromCacheByGuid($taxType);
                else
                    $taxType = TaxonomyType::getFromCacheByName($taxType);
            }
            if ($taxType == null)
                return null;
            
            if (is_numeric($taxonomy))
                $entities = Entity::getByMeta(array("typeGuid", "guid"), array($taxType->guid, $taxonomy), 1, 0, 'Taxonomy');
            else
                $entities = Entity::getByMeta(array("typeGuid", "name"), array($taxType->guid, $taxonomy), 1, 0, 'Taxonomy');
        }
        else
        {
            if (is_numeric($taxonomy))
                $entities = Entity::getByMeta("guid", $taxonomy, 1, 0, 'Taxonomy');
            else
                $entities = Entity::getByMeta("name", $taxonomy, 1, 0, 'Taxonomy');
        }
        
        if (sizeof($entities) < 0)
            return null;
        else
            return array_shift($entities);
    }
}