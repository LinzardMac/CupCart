<?php

class Admin_Dashboard
{
    public function __construct()
    {
	$panel  = Menu::addMenuPage('Dashboard', 'Dashboard', 'admin', array($this,'page'));
	
	$panel->addSubMenuPage('Home', 'Home', 'admin', array($this, 'page'));
	$panel->addSubMenuPage('Overview', 'Overview', 'admin', array($this, 'page'));
	$panel->addSubMenuPage('Quick Stats', 'Quick Stats', 'admin', array($this, 'page'));
	$panel->addSubMenuPage('Personalize', 'Personalize', 'admin', array($this, 'page'));
    }
    
    public function page($controller, $routeInfo)
    {
    }
}