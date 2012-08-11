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
    /**
     * @var string Currency display format format string with the following format codes: %s - currency symbol, %m - major amount, %e - exponent (minor) amount, %n - currency name, %d - currency display name, %c - iso code
    */
    public $format;
    
    public function __construct($alphaCode, $numericCode, $exponent, $name, $symbol = '',
	$format = '', $displayName = '', $displayNamePlural = '')
    {
	$this->alphaCode = $alphaCode;
	$this->numericCode = $numericCode;
	$this->exponent = $exponent;
	$this->name = $name;
	$this->symbol = $symbol;
	$this->displayName = $displayName;
	$this->displayNamePlural = $displayNamePlural;
	$this->format = $format;
	
	if ($this->displayName == '') $this->displayName = $this->name;
	if ($this->displayNamePlural == '') $this->displayNamePlural = $this->displayName.'s';
	if ($this->format == '') $this->format = '%s%m.%e %c';
    }
    
    /**
     * Formats a given amount into a string.
     * @param float $amount Monetary amount.
     * @param string $format Custom format string.
     * @return string
    */
    public function formatAmount($amount, $format = '')
    {
	if ($format == '')
	    $format = $this->format;
	
	$amount = $this->toFloat($amount);
	$majorAmount = floor($amount);
	$minorAmount = sprintf("%0".$this->exponent."d", ($amount - $majorAmount) * (pow(10, $this->exponent)));
	
	$match = array(
	    '%s'	=> $this->symbol,
	    '%m'	=> $majorAmount,
	    '%e'	=> $minorAmount,
	    '%n'	=> $this->name,
	    '%d'	=> ($majorAmount > 1) ? $this->displayNamePlural : $this->displayName,
	    '%c'	=> $this->alphaCode
	);
	return str_replace(array_keys($match), $match, $format);
    }
    
    /**
     * Converts an exponent value to it's float representation.
     * eg. 1000 cents becomes 10.00 dollars
     * @param int $exponentValue The exponent value.
     * @return float
    */
    public function toFloat($exponentValue)
    {
	if ($this->exponent == 0) return $exponentValue;
	return $exponentValue / pow(10, $this->exponent);
    }
    
    /**
     * Converts an amount into another currency using cached exchange rates.
     * @param float $amount Monetary amount.
     * @param mixed $sourceCurrency Source currency as ISO-4217 string or [Currency] instance.
     * @param mixed $targetCurrency Target currency as ISO-4217 string or [Currency] instance.
    */
    public static function convert($amount, $sourceCurrency, $targetCurrency)
    {
	return 0;
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
		$format = '';
		if($size > 5)
		    $format = $currencyInfo[5];
		$displayName = $name;
		if($size > 6)
		    $displayName = $currencyInfo[6];
		$displayNamePlural = $displayName.'s';
		if($size > 7)
		    $displayNamePlural = $currencyInfo[7];
		
		self::$currencies[$code] = new Currency($code, $number, $exponent, $name,
		    $symbol, $format, $displayName, $displayNamePlural);
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