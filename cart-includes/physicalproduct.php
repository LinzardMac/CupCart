<?php

/**
 * Pysical product entity. Represents a physical product for retail.
*/
class PhysicalProduct extends Entity
{
    /**
     * @var float Price of product.
    */
    public $price;
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
     * Gets if the product is in stock.
     * @return bool True if product is in stock and eligable for sale.
    */
    public function isInStock()
    {
        
    }
}