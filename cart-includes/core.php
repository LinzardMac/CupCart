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
        //  configure
        include(ROOT_DIR.'config.php');
        
        //  load store information
        self::$activeStore = Store::getActiveStore();
        View::setGlobal('store', self::$activeStore);
        
        //  load plugins
        
        //  activate the current theme
        self::$activeTheme = Theme::getActiveTheme();
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
            $pageName = 'whatever.html';
            $entities = Entity::getByMeta('filename', $pageName, 1, 0, 'Page');
            if (sizeof($entities) < 0) return null;
            return array_shift($entities);
        }
        //  else if looking at a specific entity
        else if (self::requestIsForEntity())
        {
            $entityId = '1';
            $entity = Entity::getByGuid($entityId);
            return $entity;
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
    
    public static function requestIsForCart()
    {
        return false;
    }
    
    public static function requestIsForListing()
    {
        return false;
    }
    
    public static function requestIsForFrontPage()
    {
        return true;
    }
    
    public static function requestIsForPage()
    {
        return false;
    }
    
    public static function requestIsForEntity()
    {
        return false;
    }
}