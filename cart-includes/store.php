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
     * @var Tables Table information for the store.
    */
    public $tables;
    
    /**
     * @var Currency Active currency as ISO-4217 code.
    */
    public $currency;
    
    /**
     * @var Country Active country.
    */
    public $country;
    
    /**
     * Gets the active store.
     * @return Store The active store.
    */
    public static function getActive()
    {
        $tables = new Tables();
        $tables->entity = CC_DB_PREFIX.'entities';
        $tables->entityMeta = CC_DB_PREFIX.'entities_meta';
        $tables->entityMetaKeys = CC_DB_PREFIX.'entities_metakeys';
        
        $store = new Store();
        $store->name = "Fubar Store";
        $store->hostname = $_SERVER['HTTP_HOST'];
        $store->baseUri = '/whatevercart/index.php/';
        $store->timezone = 'America/Chicago';
        $store->tables = $tables;
	$store->currency = Currency::getByISO("USD");
	$store->country = Country::getByISO("USA");
        return $store;
    }
}