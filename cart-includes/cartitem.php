<?php

/**
 * An item stored in a shopping cart.
 */
class CartItem
{
    const WEIGHT_LBS = 'lbs';
    const WEIGHT_KG = 'kg';
    const WEIGHT_GRAMS = 'g';

    /**
     * @var int GUID of the item.
    */
    public $entityGuid = 0;
    
    /**
     * @var Product Retreived product instance.
    */
    private $_entity = null;
    
    /**
     * Gets the product.
     * @return Product
    */
    public function getProduct()
    {
	if ($this->_entity == null)
	    $this->_entity = Entity::getByGuid($this->entityGuid, "Product");
	return $this->_entity;
    }
    
    /**
     * Gets the price of the cart item.
     * @return float Price of the item.
    */
    public function price()
    {
	return $this->getProduct()->price;
    }
    
    /**
     * Gets the weight of the cart item.
     * @param string $unit Optional. Unit of measurement to return the weight in. Defaults to "lbs".
     * @return float Weight.
    */
    public function weight($unit = '')
    {
	if ($unit != CartItem::WEIGHT_LBS &&
	    $unit != CartItem::WEIGHT_KG &&
	    $unit != CartItem::WEIGHT_GRAMS)
	    $unit = CartItem::WEIGHT_LBS;
	//  weight is stored in the database in lbs
	$weight = $this->getProduct()->getMeta('weight');
	if ($weight == null)
	    return null;
	switch($unit)
	{
	    case CartItem::WEIGHT_KG:
		return $weight * 0.453592;
	    case CartItem::WEIGHT_GRAMS:
		return $weight * 453.592;
	}
	return $weight;
    }
}