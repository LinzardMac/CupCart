<?php

/**
 * An attribute of a product.
*/
class Attribute extends Entity
{
    /**
     * @var string Attribute name.
    */
    public $name;
    
    /**
     * @var bool True to use $possibleValues.
    */
    public $limitedValues;
    
    /**
     * @var array Array of possible attribute values.
    */
    public $possibleValues = array();
}