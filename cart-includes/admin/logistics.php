<?php

class Admin_Logistics
{
    public function __construct()
    {
	$panel  = Menu::addMenuPage('Logistics', 'Logistics', 'admin', array($this,'page'));
    }
    
    public function page($controller, $routeInfo)
    {
    }
}