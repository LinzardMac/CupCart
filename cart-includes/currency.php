<?php

class Currency
{
    /**
     * @var array Array of currencies.
    */
    private static $currencies = array();
    
    /**
     * @var string ISO-4217 code.
    */
    public $alphaCode;
    /**
     * @var int ISO-4217 numeric code.
    */
    public $numericCode;
    /**
     * @var float Currency exponent.
    */
    public $exponent;
    /**
     * @var string Currency name.
    */
    public $name;
    /**
     * @var string Currency symbol.
    */
    public $symbol;
    /**
     * @var string Display name, singular.
    */
    public $displayName;
    /**
     * @var string Display name, plural.
    */
    public $displayNamePlural;
    
    public function __construct($alphaCode, $numericCode, $exponent, $name, $symbol = '',
	$displayName = '', $displayNamePlural = '')
    {
	$this->alphaCode = $alphaCode;
	$this->numericCode = $numericCode;
	$this->exponent = $exponent;
	$this->name = $name;
	$this->symbol = $symbol;
	$this->displayName = $displayName;
	$this->displayNamePlural = $displayNamePlural;
	
	if ($this->displayName == '') $this->displayName = $this->name;
	if ($this->displayNamePlural == '') $this->displayNamePlural = $this->displayName.'s';
    }

    /**
     * Gets an array of supported currencies.
     * @return array
    */
    public static function currencies()
    {
	if (sizeof(self::$currencies) == 0)
	{
	    $data = include_once(CC_INCLUDES_DIR.'data'.DIRECTORY_SEPARATOR.'currencies.php');
	    foreach($data as $currencyInfo)
	    {
		list($code, $number, $exponent, $name) = $currencyInfo;
		
		$size = sizeof($currencyInfo);
		$symbol = '';
		if($size > 4)
		    $symbol = $currencyInfo[4];
		$displayName = $name;
		if($size > 5)
		    $displayName = $currencyInfo[5];
		$displayNamePlural = $displayName.'s';
		if($size > 6)
		    $displayNamePlural = $currencyInfo[6];
		
		self::$currencies[$code] = new Currency($code, $number, $exponent, $name,
		    $symbol, $displayName, $displayNamePlural);
	    }
	    self::$currencies = Hooks::applyFilter("currencies", self::$currencies);
	}
	return self::$currencies;
    }
    
    /**
     * Gets a currency by ISO-4217 code.
     * @param string $isoCode ISO-4217 code.
     * @return Currency
    */
    public static function getByISO($isoCode)
    {
	$currencies = self::currencies();
	var_dump($currencies);
	if (array_key_exists($isoCode, $currencies))
	    return $currencies[$isoCode];
	return null;
    }
}