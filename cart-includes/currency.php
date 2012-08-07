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
		$obj = new Currency();
		$obj->alphaCode = $code;
		$obj->numericCode = $number;
		$obj->exponent = $exponent;
		$obj->name = $name;
		self::$currencies[$code] = $obj;
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
	if (array_key_exists($isoCode, $currencies))
	    return $currencies[$isoCode];
	return null;
    }
}