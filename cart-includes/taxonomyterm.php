<?php

/**
 * A taxonomy term.
*/
class TaxonomyTerm extends Entity
{
    /**
     * @var int [Taxonomy] GUID.
    */
    public $taxonomyGuid;
    
    /**
     * @var string Name of the taxanomy item.
    */
    public $name;
    
    /**
     * Gets a taxonomy term using a taxonomy and a taxonomy term id.
     * @param mixed $type Optional. The taxonomy. Either a string, an integer or a [Taxonomy] instance.
     * @param mixed $term Either a string or an integer identifying the taxonomy term.
     * @return TaxonomyTerm A taxonomy if found, null otherwise.
    */
    public static function get($term, $type = null)
    {
        $taxType = $type;
        if ($taxType != null)
        {
            if (!($taxType instanceof Taxonomy))
            {
                if (is_numeric($taxType))
                    $taxType = Taxonomy::getFromCacheByGuid($taxType);
                else
                    $taxType = Taxonomy::getFromCacheByName($taxType);
            }

            if ($taxType == null)
                return null;
            
            if (is_numeric($term))
                $entities = Entity::getByMeta(array("taxonomyGuid", "guid"), array($taxType->guid, $term), 1, 0, 'TaxonomyTerm');
            else
                $entities = Entity::getByMeta(array("taxonomyGuid", "name"), array($taxType->guid, $term), 1, 0, 'TaxonomyTerm');
        }
        else
        {
            if (is_numeric($term))
                $entities = Entity::getByMeta("guid", $term, 1, 0, 'TaxonomyTerm');
            else
                $entities = Entity::getByMeta("name", $term, 1, 0, 'TaxonomyTerm');
        }
        
        if (sizeof($entities) < 0)
            return null;
        else
            return array_shift($entities);
    }
}