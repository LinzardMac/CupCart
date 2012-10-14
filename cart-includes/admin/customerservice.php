<?php

class Admin_CustomerService
{
    public function __construct()
    {
	$panel  = Menu::addMenuPage('Customer Service', 'Customer Service', 'admin', array($this,'page'));
    }
    
    public function page($controller, $routeInfo)
    {
    }
}