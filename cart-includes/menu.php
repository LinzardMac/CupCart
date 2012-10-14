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
	 * Gets a page.
	 *
	 * @param mixed $page Page title or index.
	 *
	 * @return Menu Menu instance or null on failure.
	*/
	public function getPage($page = '')
	{
		$ret = null;
		
		if (is_numeric($page))
		{
			if (array_key_exists($page, $this->submenus))
				$ret = $this->submenus[$page];
		}
		else
		{
			foreach($this->submenus as $menu)
			{
				if ($menu->pageTitle == $page ||
					$menu->menuTitle == $page)
				{
					$ret = $menu;
					break;
				}
			}
		}
		return $ret;
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
		$slugBase = Router::title($menuTitle);
		$slug = $slugBase;
		$i = 0;
		while(self::getPanel($slug) != null)
			$slug = $slugBase . '-' . (++$i);
		
	$obj = new Menu();
	$obj->pageTitle = $pageTitle;
	$obj->menuTitle = $menuTitle;
	$obj->capability = $capability;
	$obj->function = $function;
	$obj->position = $position;
	$obj->slug = $slug;
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
	
	$slugBase = Router::title($menuTitle);
	$slug = $slugBase;
	$i = 0;
	while($parent->getPage($slug) != null)
		$slug = $slugBase . '-' . (++$i);
	
	$obj = new Menu();
	$obj->pageTitle = $pageTitle;
	$obj->menuTitle = $menuTitle;
	$obj->capability = $capability;
	$obj->function = $function;
	$obj->position = $position;
	$obj->slug = $slug;
	
	$parent->submenus[$position] = $obj;
	self::$allMenus[] = $obj;
	
	return $obj;
    }
	
	/**
	 * Gets a panel.
	 *
	 * @param mixed $panel Panel title or index.
	 *
	 * @return Menu Menu instance or null on failure.
	*/
	public static function getPanel($panel = '')
	{
		$ret = null;
		
		if (is_numeric($panel))
		{
			if (array_key_exists($panel, self::$menus))
				$ret = self::$menus[$panel];
		}
		else
		{
			foreach(self::$menus as $menu)
			{
				if ($menu->pageTitle == $panel ||
					$menu->menuTitle == $panel ||
					$menu->slug == $panel)
				{
					$ret = $menu;
					break;
				}
			}
		}
		return $ret;
	}
}