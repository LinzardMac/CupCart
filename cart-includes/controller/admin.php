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
		//$this->createDefaultMenu();
		$this->loadAdminHandlers();
		Hooks::doAction("admin_menus");
		
		//  determine which page is being viewed
		$activePanel = Menu::$menus[0];
		$params = Core::$activeRouteInfo->params;
		if (array_key_exists('panel', $params))
		{
		    $searchPanelSlug = $params['panel'];
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
		
		if (array_key_exists('page', $params))
		{
		    $searchSlug = $params['page'];
		    foreach($activePanel->submenus as $menu)
		    {
			if ($menu->slug == $searchSlug)
			{
			    $activePage = $menu;
			    break;
			}
		    }
		}
		
		//  check permissions
		
		//  run
		View::setGlobal('activePanel', $activePanel);
		View::setGlobal('activePage', $activePage);
		
		$callback = $activePage->function;
		ob_start();
		call_user_func_array($callback, array($this, Core::$activeRouteInfo));
		$html = ob_get_contents();
		ob_end_clean();
		
		//  show
		View::get('index')->set('output',$html)->render();
	}
	
	public function loadAdminHandlers()
	{
	    new Admin_Dashboard();
	    new Admin_Products();
	    new Admin_Inventory();
	    new Admin_Orders();
	    new Admin_Logistics();
	    new Admin_SiteSettings();
	    new Admin_Reports();
	    new Admin_CustomerService();
	}
}