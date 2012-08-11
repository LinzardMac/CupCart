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
	    $size = $sizes[0];
	}
	$dimensions = Core::$activeTheme->getThumbnailSize($size);
	if ($dimensions == null) return null;
	list($width, $height) = $dimensions;
	$targetDimensions = $width.'x'.$height;
    
	if ($this->thumbnailAttachment == null)
	    $this->thumbnailAttachment = Entity::getByGuid($this->thumbnailAttachmentGuid, 'Attachment');
	if ($this->thumbnailAttachment instanceof Attachment && $this->thumbnailAttachment->type == Attachment::TYPE_IMAGE)
	{
	    $fileName = '';
	    $thumbnails = $this->thumbnailAttachment->thumbnails;
	    if (!is_array($thumbnails))
		$thumbnails = array($thumbnails);
	    foreach($thumbnails as $thumb)
	    {
		if ($thumb == null || trim($thumb) == '')
		    continue;
		list($dimensions, $name) = explode(":", $thumb, 2);
		if ($dimensions == $targetDimensions)
		    $fileName = $name;
	    }
	    if ($fileName == '') return null;
	    $fileDir = $this->thumbnailAttachment->directoryUri;
	    if (!$this->thumbnailAttachment->isLocal)
		$fileDir = $this->thumbnailAttachment->directoryUrl;
	    return new Image($fileDir.$fileName, $this->thumbnailAttachment->directoryUrl.$fileName, $this->thumbnailAttachment->isLocal, $width, $height);
	}
	return null;
    }
    
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
	//  the format "USD:-" denotes an automatic converstion
	
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
	    if ($price == null) continue;
	    
	    if (substr($price, 0, 3) == $currency->alphaCode)
	    {
		$amount = substr($price, 4);
		if ($amount == "-")
		{
		    $baseCurrency = Currency::getByISO($this->conversionCurrency);
		    if ($baseCurrency == null)
			return null;
		    $amount = $this->getPrice($baseCurrency);
		    return Currency::convert($amount, $baseCurrency, $currency);
		}
		return floatval($amount);
	    }
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