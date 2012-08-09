<?php

/**
 * Cart core.
*/
class Core
{
    /**
     * @var Theme Currently active theme.
    */
    public static $activeTheme = null;
    
    /**
     * @var Controller Currently active controller.
    */
    public static $activeController = null;
    
    /**
     * @var Store Currently active store.
    */
    public static $activeStore = null;
    
    /**
     * @var array Array of active mu plugins.
    */
    public static $muPlugins = array();
    
    /**
     * @var array Array of active plugins.
    */
    public static $plugins = array();
    
    /**
     * @var RouteInfo Currently active route info.
    */
    public static $activeRouteInfo = null;
    
    /**
     * Runs Whatevercart.
    */
    public static function run()
    {
	//  set a default content-type for utf-8, images/binary files can override if needs be
	header("Content-Type: text/html; charset=utf-8");
	
        //  bootstrap
        self::bootstrap();
	
        //  get queried object
        $routeInfo = Hooks::applyFilter('resolve_routeinfo', Router::resolve(new Request($_SERVER['REQUEST_URI'])));
	self::$activeRouteInfo = $routeInfo;
        
        //  setup theme
        $isAdmin = (strtolower($routeInfo->controller) == 'admin') ? true : false;
        self::$activeTheme = Theme::getActive($isAdmin);
        Theme::bootstrap(self::$activeTheme);
	
	//  add widgets to widgetspaces based on settings
        $spaces = WidgetSpace::getAll();
        foreach($spaces as $space)
        {
            $space->add('Widget_Taxonomy', array('taxonomy' => Category::getTaxonomy()->guid));
        }
        
        if ($routeInfo == null)
        {
            throw new HTTP_Exception_404();
        }
        
        //  find and instantiate the controller used to respond for the queried object
        $controllerType = Hooks::applyFilter('resolve_controller', 'Controller_'.$routeInfo->controller);
	if (!class_exists($controllerType))
	    throw new HTTP_Exception_404();
        
        //  create controller instance
        $controller = new $controllerType();
        $controller->request = self::parseUrl();
	$controller->routeInfo = $routeInfo;
        self::$activeController = $controller;
        
        //  resolve actionable method
        $method = Hooks::applyFilter('resolve_action', $routeInfo->action);
	
	$methods = get_class_methods($controller);
	if ($methods == null || !in_array($method, $methods))
	    throw new HTTP_Exception_404();
        
        //  call
        $controller->$method();
    }
    
    /**
     * Handles bootstrapping the cart environment, including loading plugins and activating the current theme.
    */
    private static function bootstrap()
    {
	//  add the basic router to the routing stack
	Router::add(new Router_Basic(), 100);
    
        //  load store information
        self::$activeStore = Store::getActive();
        View::setGlobal('store', self::$activeStore);
        date_default_timezone_set(self::$activeStore->timezone);
        
        //  load plugins
        self::loadMuPlugins();
        self::loadPlugins();
        
        //  load taxanomy types
        Taxonomy::loadAll();
		
        //  load widgets
        Widget::register("Widget_Cart");
        Widget::register("Widget_Taxonomy");
        Hooks::doAction("register_widgets");
	
	Hooks::doAction("bootstrap");
    }
    
    /**
     * Loads all must-use plugins.
    */
    private static function loadMuPlugins()
    {
        self::$muPlugins = Plugin::getList(CC_MUPLUGINS_DIR);
        foreach(self::$muPlugins as $plugin)
            $plugin->load();
        Hooks::doAction("muplugins_loaded");
    }
    
    /**
     * Loads all active plugins.
    */
    private static function loadPlugins()
    {
        self::$plugins = Plugin::getActive();
        foreach(self::$plugins as $plugin)
            $plugin->load();
        Hooks::doAction("plugins_loaded");
    }
    
    /**
     * Parses the current request URL.
     * Caches the results for later use.
    */
    public static function parseUrl()
    {
        return Hooks::applyFilter("the_request", new Request($_SERVER['REQUEST_URI']));
    }
}