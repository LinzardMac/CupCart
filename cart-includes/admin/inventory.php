<?php

class Admin_Inventory
{
    public function __construct()
    {
	$panel  = Menu::addMenuPage('Inventory', 'Inventory', 'admin', array($this,'page'));
    }
    
    public function page($controller, $routeInfo)
    {
    }
}