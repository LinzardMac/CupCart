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
     * Runs Whatevercart.
    */
    public static function run()
    {
	//  set a default content-type for utf-8, images/binary files can override if needs be
	header("Content-Type: text/html; charset=utf-8");
	
        //  bootstrap
        self::bootstrap();
	
        //  get queried object
	$routerClass = Hooks::applyFilter("request_router", "Router_Basic");
        $queryObj = Hooks::applyFilter('resolve_queryObject', call_user_func($routerClass."::resolveQueryObject"));
        
        //  setup theme
        $isAdmin = ($queryObj == 'Admin') ? true : false;
        self::$activeTheme = Theme::getActive($isAdmin);
        Theme::bootstrap(self::$activeTheme);
        
        if ($queryObj == null)
        {
            throw new HTTP_Exception_404();
        }
        
        //  find and instantiate the controller used to respond for the queried object
        $controllerType = self::resolveController($queryObj);
        
        //  create controller instance
        $controller = new $controllerType();
        $controller->request = self::parseUrl();
        self::$activeController = $controller;
        
        //  resolve actionable method
        $method = Hooks::applyFilter('resolve_action', 'get_index');
        
        //  call
        $controller->$method();
    }
    
    /**
     * Handles bootstrapping the cart environment, including loading plugins and activating the current theme.
    */
    private static function bootstrap()
    {
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
        
        //  add widgets to widgetspaces based on settings
        $spaces = WidgetSpace::getAll();
        foreach($spaces as $space)
        {
            $space->add('Widget_Taxonomy', array('taxonomy' => Category::getTaxonomy()->guid));
        }
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
     * Resolves which controller should handle the request for the given object.
     * @param mixed $queryObj The string or object requested.
     * @return string The datatype of the resolved controller.
    */
    public static function resolveController($queryObj)
    {
        $className = '';
        if (is_object($queryObj))
            $className = get_class($queryObj);
        else
            $className = (string)$queryObj;
        return Hooks::applyFilter('resolve_controller', 'Controller_'.$className);
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