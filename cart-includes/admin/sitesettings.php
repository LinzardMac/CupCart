<?php

class Admin_SiteSettings
{
    public function __construct()
    {
	$panel  = Menu::addMenuPage('Site Settings', 'Site Settings', 'admin', array($this,'page'));
    }
    
    public function page($controller, $routeInfo)
    {
    }
}