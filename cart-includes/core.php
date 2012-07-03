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
     * @var Request Current request.
    */
    private static $_request = null;
    
    /**
     * Runs Whatevercart.
    */
    public static function run()
    {
        //  bootstrap
        self::bootstrap();
        
        //  get queried object
        $queryObj = Hooks::applyFilter('resolve_queryObject', self::getQueriedObject());
        if ($queryObj == null)
        {
            throw new HTTP_Exception_404();
        }
        
        //  find and instantiate the controller used to respond for the queried object
        $controllerType = self::resolveController($queryObj);
        
        //  create controller instance
        $controller = new $controllerType();
        $controller->request = self::$_request;
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
        TaxonomyType::loadAll();
        
        //  activate the current theme
        self::$activeTheme = Theme::getActive();
        Theme::bootstrap(self::$activeTheme);
    }
    
    /**
     * Loads all must-use plugins.
    */
    private static function loadMuPlugins()
    {
        self::$muPlugins = Plugin::getList(MUPLUGINS_DIR);
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
     * Gets the object currently being requested.
     * @return mixed Queried object or string.
    */
    public static function getQueriedObject()
    {
        //  if looking at a page
        if (self::requestIsForPage())
        {
            $pageUri = substr(self::$_request->rawPath, 6);
            $entities = Entity::getByMeta('uri', $pageUri, 1, 0, 'Page');
            if (sizeof($entities) < 0) return null;
            return array_shift($entities);
        }
        //  else if looking at a specific entity
        else if (self::requestIsForEntity())
        {
            $entityType = self::$_request->path[1];
            $bits = explode("-", self::$_request->file);
            $entityId = intval($bits[sizeof($bits)-1]);
            $entity = Entity::getByGuid($entityId);
            if ($entity->entityType != $entityType)
                return null;
            return $entity;
        }
        else if (self::requestIsForCheckout())
        {
            return 'Checkout';
        }
        else if (self::requestIsForCart())
        {
            return 'Cart';
        }
        else if (self::requestIsForListing())
        {
            return 'Listing';
        }
        else if (self::requestIsForFrontPage())
        {
            return 'FrontPage';
        }
        return null;
    }
    
    /**
     * Parses the current request URL.
     * Caches the results for later use.
    */
    public static function parseUrl()
    {
        if (self::$_request == null)
            self::$_request = Hooks::applyFilter("the_request", new Request($_SERVER['REQUEST_URI']));
        return self::$_request;
    }
    
    public static function requestIsForCheckout()
    {
        $request = self::parseUrl();
        if (strtolower($request->rawPath) == '/checkout')
            return true;
        return false;
    }
    
    public static function requestIsForCart()
    {
        $request = self::parseUrl();
        if (strtolower($request->rawPath) == '/viewcart')
            return true;
        return false;
    }
    
    public static function requestIsForListing()
    {
        $request = self::parseUrl();
        if (sizeof($request->path) == 2 && strtolower($request->path[0]) == 'store')
            return true;
        return false;
    }
    
    public static function requestIsForFrontPage()
    {
        $request = self::parseUrl();
        switch(strtolower($request->rawPath))
        {
            case "":
            case "/":
            case "/home":
                return true;
            default:
                return false;
        }
    }
    
    public static function requestIsForPage()
    {
        $request = self::parseUrl();
        if (sizeof($request->path) > 1 && strtolower($request->path[0]) == "cms")
            return true;
        return false;
    }
    
    public static function requestIsForEntity()
    {
        $request = self::parseUrl();
        if (sizeof($request->path) > 2 && strtolower($request->path[0]) == 'store')
            return true;
        return false;
    }
}