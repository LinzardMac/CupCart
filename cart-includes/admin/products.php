<?php

class Admin_Products
{
    public function __construct()
    {
	$products  = Menu::addMenuPage('Products', 'Products', 'admin', array($this,'products'));
	
	$products->addSubMenuPage('Products', 'Products', null, null);
	$products->addSubMenuPage('New Product', 'New Product', 'admin', array($this, 'products'));
	$products->addSubMenuPage('Manage Products', 'Manage Products', 'admin', array($this, 'products'));
	
	$products->addSubMenuPage('Taxonomies', 'Taxonomies', null, null);
	$products->addSubMenuPage('New Category', 'New Category', 'admin', array($this, 'products'));
	$products->addSubMenuPage('Manage Categories', 'Manage Categories', 'admin', array($this, 'products'));
	$products->addSubMenuPage('New Tag', 'New Tag', 'admin', array($this, 'products'));
	$products->addSubMenuPage('Manage Tags', 'Manage Tags', 'admin', array($this, 'products'));
	$products->addSubMenuPage('New Taxonomy', 'New Taxonomy', 'admin', array($this, 'products'));
	$products->addSubMenuPage('Manage Taxonomies', 'Manage Taxonomies', 'admin', array($this, 'products'));
    }
    
    public function products($controller, $routeInfo)
    {
	var_dump($routeInfo);
    }
}