<?php

/**
 * Entity loop.
*/
class Loop
{
    /**
     * @var array Entities in the loop.
    */
    private $entities;
    
    /**
     * @var int Current entity index.
    */
    private $loopIndex;
    
    /**
     * @var Entity Current entity.
    */
    public $entity;
    
    public function __construct($entities)
    {
        $this->entities = $entities;
        $this->loopIndex = 0;
        $this->entity = null;
    }
    
    /**
     * Gets if the loop has entities and can continue to iterate over entities.
     * @return bool True if entities are loopable, false otherwise.
    */
    public function hasEntities()
    {
        if (!is_array($this->entities))
            return false;
        if (sizeof($this->entities) <= $this->loopIndex)
            return false;
        return true;
    }
    
    /**
     * Gets the current entity in the loop and advances the loop position by one.
     * @return Entity The current loop entity, null when no entity is available.
    */
    public function theEntity()
    {
        if ($this->hasEntities())
        {
            $this->entity = $this->entities[$this->loopIndex++];
            return $this->entity;
        }
        return null;
    }
    
    /**
     * Gets the URL used to view the current entity.
     * @return string The entity's URL.
    */
    public function theUrl()
    {
        if ($this->entity != null)
        {
            $store = Core::$activeStore;
            return Hooks::applyFilter('the_url', 'http://'.$store->hostname.$store->baseUri.'products/'.$this->theGuid().'/'.urlencode($this->theTitle()).'.html');
        }
        return '';
    }
    
    /**
     * Gets the title of the current entity.
     * @return string The entity's title.
    */
    public function theTitle()
    {
        $title = '';
        if ($this->entity instanceof PhysicalProduct)
            $title = $this->entity->name;
        return Hooks::applyFilter('the_title', $title);
    }
    
    /**
     * Gets the GUID of the current entity.
     * @return int The entity's GUID.
    */
    public function theGuid()
    {
        $guid = 0;
        if ($this->entity != null)
        {
            $guid = $this->entity->guid;
        }
        return Hooks::applyFilter('the_guid', $guid);
    }
}