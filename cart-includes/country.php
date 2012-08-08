<?php

class Country
{

    /**
     * @var array Array of countries.
    */
    private static $countries = array();
    
    public $name;
    public $alpha2;
    public $alpha3;
    public $numeric;
    public $iso;
    
    public function __construct($name, $alpha2, $alpha3, $numeric, $iso)
    {
	$this->name = $name;
	$this->alpha2 = $alpha2;
	$this->alpha3 = $alpha3;
	$this->numeric = $numeric;
	$this->iso = $iso;
    }
    
    /**
     * Gets an array of supported countries.
     * @return array
    */
    public static function countries()
    {
	if (sizeof(self::$countries) == 0)
	{
	    $data = include_once(CC_INCLUDES_DIR.'data'.DIRECTORY_SEPARATOR.'countries.php');
	    foreach($data as $countryInfo)
	    {
		list($name, $alpha2, $alpha3, $numeric, $iso) = $countryInfo;
		
		self::$countries[$alpha3] = new Country($name, $alpha2, $alpha3, $numeric, $iso);
	    }
	    self::$countries = Hooks::applyFilter("countries", self::$countries);
	}
	return self::$countries;
    }

    /**
     * Gets a country by ISO-3166-1 code, either alpha-2, alpha-3 or numeric code.
     * Alpha-3 offers the fastest lookup.
     * @param string $isoCode ISO-3166-1 code.
     * @return Country
    */
    public static function getByISO($isoCode)
    {
	$countries = self::countries();
	if (is_numeric($isoCode))
	{
	    foreach($countries as $country)
	    {
		if ($country->numeric == $isoCode) return $country;
	    }
	    return null;
	}
	if (strlen($isoCode) == 2)
	{
	    foreach($countries as $country)
	    {
		if ($country->alpha2 == $isoCode) return $country;
	    }
	    return null;
	}
	if (array_key_exists($isoCode, $countries))
	    return $countries[$isoCode];
	return null;
    }
}