<?php

/**
 * Product entity. Represents a physical product for retail.
*/
class Product extends Entity
{
    /**
     * @var float Price of product.
    */
    public $prices;
    /**
     * @var string Name of the product.
    */
    public $name;
    /**
     * @var string Description of the product.
    */
    public $description;
    /**
     * @var string Product serial number.
    */
    public $serialNumber;
    /**
     * @var int Number of items in stock.
    */
    public $inStock;
    /**
     * @var int Number of items in stock that are reserved or already sold.
    */
    public $reservedStock;
    
    /**
     * Gets the price of the product in the given currency.
     * @param mixed $currency Optional. Either an ISO-4217 identifier string or a [Currency] object. Defaults to the main store currency.
     * @param bool $format Optional. Set to true to format as a currency string (ex. $1.00) or give a format string with the following format codes: %s - currency symbol, %m - major amount, %e - exponent (minor) amount, %n - currency name, %d - currency display name, %c - iso code
     * @return mixed A float or string depending on $format.
    */
    public function getPrice($currency = '', $format = false)
    {
	//  prices are stored as an array in the format
	//  array("USD:1.00", "JPY:1000", ...)
	//  the format "USD:-" denotes an automatic converstion using the FIRST non-automatic amount and the most recent conversion rates
	
	if ($currency == '')
	    $currency = Core::$activeStore->currencies[0];
	if (!($currency instanceof Currency))
	    $currency = Currency::getByISO($currency);
	
	if (!($currency instanceof Currency))
	    throw new Exception("Invalid currency specified");
	
	if ($format !== false)
	{
	    $price = $this->getPrice($currency);
	    if ($price === null)
		return '';
	    if ($format === true)
		$format = '';
	    return $currency->formatAmount($price, $format);
	}
	
	$prices = $this->prices;
	if (!is_array($prices))
	    $prices = array($this->prices);
	foreach($prices as $price)
	{
	    if (substr($price, 0, 3) == $currency->alphaCode)
	    {
		$amount = substr($price, 4);
		if ($amount == "-")
		{
		    $baseCurrency = $this->getConversionBaseCurrency();
		    $amount = $this->getPrice($baseCurrency);
		    return Currency::convert($amount, $baseCurrency, $currency);
		}
		return floatval($amount);
	    }
	}
	return null;
    }
    
    /**
     * Gets the currency this product will use for automatic currency conversions.
     * @return Currency
    */
    public function getConversionBaseCurrency()
    {
	$prices = $this->prices;
	if (!is_array($prices))
	    $prices = array($this->prices);
	foreach($prices as $price)
	{
	    if(substr($price, 4, 1) != "-")
		return Currency::getByISO(substr($price, 0, 3));
	}
	return null;
    }
    
    /**
     * Gets if the product is in stock.
     * @return bool True if product is in stock and eligable for sale.
    */
    public function isInStock()
    {
        
    }
}