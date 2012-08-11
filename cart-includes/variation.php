<?php

/**
 * A variation of a product.
 * Includes a collection of attributes and custom variation information.
*/
class Variation extends Entity
{
    /**
     * @var string Variation name.
    */
    public $name;
    
    /**
     * @var int Product guid this variation is for.
    */
    public $productGuid;
    
    /**
     * @var array Array of member attribute guids.
    */
    public $attributeGuids = array();
    
    /**
     * @var array Array of attribute values, indexed by attribute guid.
    */
    public $attributeValues = array();
    
    public function addAttribute(Attribute $attr, $value)
    {
	if (in_array($attr->guid, $this->attributeGuids))
	    return;
	$this->attributeGuids[] = $attr->guid;
	$this->attributeValues[$attr->guid] = $value;
    }
}