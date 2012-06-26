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
}