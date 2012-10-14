<?php

class Admin_Orders
{
    public function __construct()
    {
	$panel  = Menu::addMenuPage('Orders', 'Orders', 'admin', array($this,'page'));
    }
    
    public function page($controller, $routeInfo)
    {
    }
}