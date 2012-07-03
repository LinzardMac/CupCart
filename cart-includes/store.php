<?php

class Store extends Entity
{
    /**
     * @var string Name of the store.
    */
    public $name;
    
    /**
     * @var string Hostname of the store.
    */
    public $hostname;
    
    /**
     * @var string Base URI of the store.
    */
    public $baseUri;
    
    /**
     * @var string Timezone setting for the store.
    */
    public $timezone;
    
    /**
     * Gets the active store.
     * @return Store The active store.
    */
    public static function getActive()
    {
        $store = new Store();
        $store->name = "Fubar Store";
        $store->hostname = $_SERVER['HTTP_HOST'];
        $store->baseUri = '/whatevercart/index.php/';
        $store->timezone = 'America/Chicago';
        return $store;
    }
}