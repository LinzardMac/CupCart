<?php

/**
 * Product entity. Represents a physical product for retail.
*/
class Product extends Entity
{
    /**
     * @var array Array of prices, indexed by currency code.
    */
    public $prices = array();
    /**
     * @var string ISO-4217 identifier for currency to automatically convert prices from.
    */
    public $conversionCurrency;
    /**
     * @var string Name of the product.
    */
    public $name;
    /**
     * @var string Description of the product.
    */
    public $description;
    /**
     * @var int Thumbnail attachment GUID.
    */
    public $thumbnailAttachmentGuid;
    
    /**
     * @var Attachment Thumbnail attachment if it's been loaded.
    */
    private $thumbnailAttachment;
    
    /**
     * Gets a thumbnail for the product in the given preset size.
     * @param string $size Name of a thumbnail size.
     * @return Image
    */
    public function getThumbnail($size = '')
    {
	if ($size == '')
	{
	    $sizes = Core::$activeTheme->getThumbnailSizes();
	    if (sizeof($sizes) < 1)
		return null;
	    $size = array_shift($sizes);
	}
	$dimensions = Core::$activeTheme->getThumbnailSize($size);
	if ($dimensions == null) return null;
	list($width, $height) = $dimensions;
    
	if ($this->thumbnailAttachment == null)
	    $this->thumbnailAttachment = Entity::getByGuid($this->thumbnailAttachmentGuid, 'Attachment');
	if ($this->thumbnailAttachment instanceof Attachment && $this->thumbnailAttachment->type == Attachment::TYPE_IMAGE)
	{
	    return $this->thumbnailAttachment->getThumbnail($width, $height);
	}
	return null;
    }
    
    /**
     * Gets the price of the product in the given currency.
     * @param mixed $currency Optional. Either an ISO-4217 identifier string or a [Currency] object. Defaults to the main store currency.
     * @param bool $format Optional. Set to true to format as a currency string (ex. $1.00) or give a format string with the following format codes: %s - currency symbol, %m - major amount, %e - exponent (minor) amount, %n - currency name, %d - currency display name, %c - iso code
     * @return mixed A float or string depending on $format.
    */
    public function getPrice($currency = '', $format = false, $asExponent = false)
    {
	//  prices are stored as an array in the format
	//  array("USD"=>1000, "JPY"=>10000, ...)
	//  the format "USD"=>"-" denotes an automatic converstion
	
	if ($currency == '')
	    $currency = Core::$activeStore->currencies[0];
	if (!($currency instanceof Currency))
	    $currency = Currency::getByISO($currency);
	
	if (!($currency instanceof Currency))
	    throw new Exception("Invalid currency specified");
	
	if ($format !== false)
	{
	    $price = $this->getPrice($currency, false, true);
	    if ($price === null)
		return '';
	    if ($format === true)
		$format = '';
	    return $currency->formatAmount($price, $format);
	}
	
	$prices = $this->prices;
	if (!is_array($prices))
	    return null;
	if (!array_key_exists($currency->alphaCode, $prices))
	    return null;
	$amount = $prices[$currency->alphaCode];
	if ($amount == "-")
	{
	    $baseCurrency = Currency::getByISO($this->conversionCurrency);
	    if ($baseCurrency == null)
		return null;
	    if($baseCurrency == $currency)
		return null;
	    $amount = $this->getPrice($baseCurrency);
	    $convertedAmount = Currency::convert($amount, $baseCurrency, $currency);
	    if ($asExponent) return $convertedAmount;
	    else return $currency->toFloat($convertedAmount);
	}
	if ($asExponent)
	    return $amount;
	else
	    return $currency->toFloat($amount);
    }
    
    /**
     * Gets if the product is in stock.
     * @return bool True if product is in stock and eligable for sale.
    */
    public function isInStock()
    {
        
    }
}