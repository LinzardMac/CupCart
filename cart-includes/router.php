<?php

/**
 * Defines a router.
 * @abstract
*/
abstract class Router
{
    /**
     * @var array Router stack.
    */
    private static $stack = array();

    /**
     * Resolve the current queried object.
     * @return mixed An entity object or controller name as a string.
    */
    abstract public function match(Request $request);
    
    /**
     * Create a link to an object.
     * @return string
    */
    abstract public function uri($routeName, $params);
    
    /**
     * Attempts to match the request to a route.
     * @param Request $request
     * @return mixed A populated [RouteInfo] object if matching, null otherwise.
    */
    public static function resolve(Request $request)
    {
	foreach(self::$stack as $prio => $routers)
	{
	    foreach($routers as $router)
	    {
		$obj = $router->match($request);
		if ($obj != null)
		    return $obj;
	    }
	}
	return null;
    }
    
    /**
     * Product a URL to an object.
     * @param mixed $object The object to link to.
     * @return string
    */
    public static function url($routeName, $params)
    {
	foreach(self::$stack as $prio => $routers)
	{
	    foreach($routers as $router)
	    {
		$url = $router->uri($routeName, $params);
		if ($url != null && $url != '')
		    return Core::$activeStore->baseUri . $url;
	    }
	}
	return null;
    }
    
    /**
     * Adds a new router to the router stack.
    */
    public static function add(Router $router, $priority = 5)
    {
        if (!array_key_exists($priority, self::$stack))
            self::$stack[$priority] = array();
        
        self::$stack[$priority][] = $router;
	
	self::$stack = self::sortStack(self::$stack);
    }
    
    /**
     * Sorts a stack based on priority.
     * @param array $stack Stack to sort.
     * @return array Stack sorted by priority.
    */
    private static function sortStack($stack)
    {
        $sortedStack = array();
        $keys = array_keys($stack);
        asort($keys);
        foreach($keys as $key)
        {
            $sortedStack[$key] = $stack[$key];
        }
        return $sortedStack;
    }
}