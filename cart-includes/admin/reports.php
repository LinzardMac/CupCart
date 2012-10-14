<?php

class Admin_Reports
{
    public function __construct()
    {
	$panel  = Menu::addMenuPage('Reports', 'Reports', 'admin', array($this,'page'));
    }
    
    public function page($controller, $routeInfo)
    {
    }
}