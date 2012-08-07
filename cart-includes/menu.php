<?php

/**
 * Menu API.
*/
class Menu
{
    public $pageTitle;
    public $menuTitle;
    public $capability;
    public $function;
    public $position;
    public $slug;
    
    public $submenus;
    
    public function __construct()
    {
	$this->submenus = array();
    }
    
    public function addSubMenuPage($pageTitle, $menuTitle, $capability, $function, $position)
    {
	return self::addSubMenuPageTo($this, $pageTitle, $menuTitle, $capability, $function, $position);
    }

    /**
     * @var array Array of top-level menus.
    */
    public static $menus = array();
    /**
     * @var array Array of all menus.
    */
    private static $allMenus = array();
    
    public static function addMenuPage($pageTitle, $menuTitle, $capability, $function, $position)
    {
	$obj = new Menu();
	$obj->pageTitle = $pageTitle;
	$obj->menuTitle = $menuTitle;
	$obj->capability = $capability;
	$obj->function = $function;
	$obj->position = $position;
	$obj->slug = sizeof(self::$allMenus) + 1;
	self::$menus[$position] = $obj;
	self::$allMenus[] = $obj;
	return $obj;
    }
    
    public static function addSubMenuPageTo($parent, $pageTitle, $menuTitle, $capability, $function, $position)
    {
	if (!($parent instanceof Menu))
	{
	    $search = $parent;
	    $parent = null;
	    foreach(self::$allMenus as $menu)
	    {
		if ($menu->slug == $search)
		{
		    $parent = $menu;
		    break;
		}
	    }
	}
	
	if (!($parent instanceof Menu))
	    return null;
	
	$obj = new Menu();
	$obj->pageTitle = $pageTitle;
	$obj->menuTitle = $menuTitle;
	$obj->capability = $capability;
	$obj->function = $function;
	$obj->position = $position;
	$obj->slug = sizeof(self::$allMenus) + 1;
	
	$parent->submenus[$position] = $obj;
	self::$allMenus[] = $obj;
	
	return $obj;
    }
}