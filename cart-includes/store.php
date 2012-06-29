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
     * Base URI of the store.
    */
    public $baseUri;
    
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
        return $store;
    }
}