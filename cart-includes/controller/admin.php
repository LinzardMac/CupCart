<?php

/**
 * Admin controller.
 * Handles all admin requests.
*/
class Controller_Admin extends Controller
{
	public function get_index()
	{
		//  setup admin menus
		$this->createDefaultMenu();
		Hooks::doAction("admin_menus");
		
		//  determine which page is being viewed
		$activePanel = Menu::$menus[0];
		$params = Core::$activeRouteInfo->params;
		if (array_key_exists('category1', $params))
		{
		    $searchPanelSlug = $params['category1'];
		    foreach(Menu::$menus as $menu)
		    {
			if ($menu->slug == $searchPanelSlug)
			{
			    $activePanel = $menu;
			    break;
			}
		    }
		}
		
		$activePage = null;
		for($i = 0; $i < sizeof($activePanel->submenus); $i++)
		{
		    if ($activePanel->submenus[$i]->function != null)
		    {
			$activePage = $activePanel->submenus[$i];
			break;
		    }
		}
		
		if ($activePage == null)
		    $activePage = $activePanel;
		
		if (array_key_exists('category2', $params))
		{
		    $searchSlug = $params['category2'];
		    foreach($activePanel->submenus as $menu)
		    {
			if ($menu->slug == $searchSlug)
			{
			    $activePage = $menu;
			    break;
			}
		    }
		}
		
		//  make a more appropriate parameter array
		$paramArray = array();
		foreach($params as $index => $param)
		{
			if ($index == 'category1' ||
				$index == 'category2')
				continue;
			$paramArray[] = $param;
		}
		
		//  check permissions
		
		//  run
		View::setGlobal('activePanel', $activePanel);
		View::setGlobal('activePage', $activePage);
		
		$callback = $activePage->function;
		ob_start();
		call_user_func_array($callback, array($this, $paramArray));
		$html = ob_get_contents();
		ob_end_clean();
		
		//  show
		View::get('index')->set('output',$html)->render();
	}
	
	public function dashboard($controller, $params)
	{
		View::get('dashboard')->render();
	}
	
	public function products($controller, $params)
	{
	    echo 'Products';
	}
	
	public function inventory($controller, $params)
	{
	    echo 'Inventory';
	}
	
	public function orders($controller, $params)
	{
	    echo 'Orders';
	}
	
	public function logistics($controller, $params)
	{
	    echo 'Logistics';
	}
	
	public function settings($controller, $params)
	{
	    echo 'Settings';
	}
	
	public function reports($controller, $params)
	{
	    echo 'Reports';
	}
	
	public function customerService($controller, $params)
	{
	    echo 'Customer Service';
	}
	
	private function createDefaultMenu()
	{
		$dashboard = Menu::addMenuPage('Dashboard',      'Dashboard',      'admin', array($this,'dashboard'), 0);
		$products  = Menu::addMenuPage('Products',   	 'Products',   	   'admin', array($this,'products'), 1);
		$inventory = Menu::addMenuPage('Inventory',      'Inventory',      'admin', array($this,'inventory'), 2);
		$orders    = Menu::addMenuPage('Orders',         'Orders',         'admin', array($this,'orders'), 3);
		$logistics = Menu::addMenuPage('Logistics',      'Logistics',      'admin', array($this,'logistics'), 4);
		$settings  = Menu::addMenuPage('Site Settings',  'Site Settings',  'admin', array($this,'settings'), 5);
		$reports   = Menu::addMenuPage('Reports', 	 'Reports', 	   'admin', array($this,'reports'), 6);
		$customer  = Menu::addMenuPage('Customer Service','Customer Service', 	   'admin', array($this,'customerService'), 7);
		
		$dashboard->addSubMenuPage('Home', 'Home', 'admin', array($this, 'dashboard'), 0);
		$dashboard->addSubMenuPage('Overview', 'Overview', 'admin', array($this, 'dashboard'), 1);
		$dashboard->addSubMenuPage('Quick Stats', 'Quick Stats', 'admin', array($this, 'dashboard'), 2);
		$dashboard->addSubMenuPage('Personalize', 'Personalize', 'admin', array($this, 'dashboard'), 3);
		
		$products->addSubMenuPage('Products', 'Products', null, null, 0);
		$products->addSubMenuPage('New Product', 'New Product', 'admin', array($this, 'products'), 1);
		$products->addSubMenuPage('Manage Products', 'Manage Products', 'admin', array($this, 'products'), 2);
		
		$products->addSubMenuPage('Taxonomies', 'Taxonomies', null, null, 3);
		$products->addSubMenuPage('New Category', 'New Category', 'admin', array($this, 'products'), 4);
		$products->addSubMenuPage('Manage Categories', 'Manage Categories', 'admin', array($this, 'products'), 5);
		$products->addSubMenuPage('New Tag', 'New Tag', 'admin', array($this, 'products'), 6);
		$products->addSubMenuPage('Manage Tags', 'Manage Tags', 'admin', array($this, 'products'), 7);
		$products->addSubMenuPage('New Taxonomy', 'New Taxonomy', 'admin', array($this, 'products'), 8);
		$products->addSubMenuPage('Manage Taxonomies', 'Manage Taxonomies', 'admin', array($this, 'products'), 9);
		
		/*$catalog->addSubMenuPage('Categories', 'Categories', 'admin', array($this, ''), 0);
		$catalogProducts = $catalog->addSubMenuPage('Products',   'Products',   'admin', array($this, ''), 1);
		$catalogProducts->addSubMenuPage('Dashboard', 'Dashboard', 'admin', array($this, ''), 0);
		$catalogProducts->addSubMenuPage('Add New', 'Add New', 'admin', array($this, ''), 1);
		$catalogProducts->addSubMenuPage('Images', 'Images', 'admin', array($this, ''), 2);
		$catalogProducts->addSubMenuPage('Attributes', 'Attributes', 'admin', array($this, ''), 3);
		$catalogProducts->addSubMenuPage('Discounts', 'Discounts', 'admin', array($this, ''), 4);*/
	}
}