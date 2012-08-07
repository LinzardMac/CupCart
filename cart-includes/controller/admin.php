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
		$activeMenu = 0;
		$activeSubMenu = $activeTab = -1;
		if (sizeof($this->request->path) > 1)
			$activeMenu = $this->request->path[1];
		if (sizeof($this->request->path) > 2)
			$activeSubMenu = $this->request->path[2];
		if (sizeof($this->request->path) > 3)
			$activeTab = $this->request->path[3];
		$theMenu = Menu::$menus[$activeMenu];
		if ($activeSubMenu > -1)
		{
			$theMenu = $theMenu->submenus[$activeSubMenu];
			if ($activeTab > -1)
			{
				$theMenu = $theMenu->submenus[$activeTab];
			}
		}
		
		//  check permissions
		
		//  run
		$callback = $theMenu->function;
		ob_start();
		call_user_func_array($callback, array($this));
		$html = ob_get_contents();
		ob_end_clean();
		
		//  show
		View::get('index')->set('output',$html)->render();
	}
	
	public function dashboard($controller)
	{
		View::get('dashboard')->render();
	}
	
	private function createDefaultMenu()
	{
		$dashboard = Menu::addMenuPage('Dashboard',      'Dashboard',      'admin', array($this,'dashboard'), 0);
		$catalog   = Menu::addMenuPage('Catalog Mgt.',   'Catalog Mgt.',   'admin', array($this,''), 1);
		$orders    = Menu::addMenuPage('Order Mgt.',     'Order Mgt.',     'admin', array($this,''), 2);
		$employees = Menu::addMenuPage('Employees',      'Employees',      'admin', array($this,''), 3);
		$shipping  = Menu::addMenuPage('Shipping',       'Shipping',       'admin', array($this,''), 4);
		$payment   = Menu::addMenuPage('Payment',        'Payment',        'admin', array($this,''), 5);
		$settings  = Menu::addMenuPage('Store Settings', 'Store Settings', 'admin', array($this,''), 6);
		
		$catalog->addSubMenuPage('Categories', 'Categories', 'admin', array($this, ''), 0);
		$catalogProducts = $catalog->addSubMenuPage('Products',   'Products',   'admin', array($this, ''), 1);
		$catalogProducts->addSubMenuPage('Dashboard', 'Dashboard', 'admin', array($this, ''), 0);
		$catalogProducts->addSubMenuPage('Add New', 'Add New', 'admin', array($this, ''), 1);
		$catalogProducts->addSubMenuPage('Images', 'Images', 'admin', array($this, ''), 2);
		$catalogProducts->addSubMenuPage('Attributes', 'Attributes', 'admin', array($this, ''), 3);
		$catalogProducts->addSubMenuPage('Discounts', 'Discounts', 'admin', array($this, ''), 4);
	}
}