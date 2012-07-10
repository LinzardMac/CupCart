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
     * @var int GUID of the parent taxonomy term, 0 if has no parent.
    */
    public $parent;
    
    public function __construct()
    {
        $this->parent = 0;
    }
    
    /**
     * Gets a url to the specific taxonomy term.
     * @return string
    */
    public function getUrl()
    {
        $taxonomy = Taxonomy::getFromCacheByGuid($this->getProperty("taxonomyGuid"));
        
        $taxonomyName = '';
        if ($taxonomy->name != "Category")
            $taxonomyName = rawurlencode($taxonomy->name).':';
        
        $url = Core::$activeStore->baseUri.'store/';
        $termName = rawurlencode($this->name);
        $parent = $this->parent;
        while ($parent != 0)
        {
            //  this needs to be made faster somehow, doing lookups is not good enough
            $parentTerm = Entity::getByGuid($parent, 'TaxonomyTerm');
            $termName = rawurlencode($parentTerm->name).':'.$termName;
            $parent = $parentTerm->parent;
        }
        $url .= $taxonomyName.$termName;
        return Hooks::applyFilter("taxonomy_term_url", $url);
    }
    
    /**
     * Gets an array of children taxonomy terms.
     * @param bool $recursive Optional. When set to true all sub-children will be retreived also.
     * @return array
    */
    public function getChildren($recursive = false)
    {
        $children = Entity::getByMeta("parent", $this->guid, 0, 0, 'TaxonomyTerm');
        if ($recursive)
        {
            foreach($children as $child)
            {
                $subChildren = $child->getChildren(true);
                foreach($subChildren as $child)
                {
                    $children[] = $child;
                }
            }
        }
        return $children;
    }
    
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