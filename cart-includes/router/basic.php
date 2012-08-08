<?php

/**
 * The default router for cupcart.
 * No permalink support.
*/
class Router_Basic extends Router
{
    /**
     * @var Request Current request.
    */
    private static $_request = null;
	
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
    
    public function getLinkToObject($object, $params = array(), $loop = null)
    {
	if ($object instanceof Entity)
	{
	    $title = '';
	    if ($loop != null) $title = $loop->theTitle();
	    return Core::$activeStore->baseUri.'store/'
                . get_class($object) . '/category stuff here/'.rawurlencode($title).'-'.$object->guid;
	}
	else if ($object == 'Cart')
	{
	    $action = '';
	    $entity = null;
	    if (array_key_exists('entity',$params))
		$entity = $params['entity'];
	    if (array_key_exists('action',$params))
		$action = $params['action'];
	    if ($action != '' && $entity != null)
		return Core::$activeStore->baseUri.'cart/'.$action.'/'.$entity->guid;
	    else
		return Core::$activeStore->baseUri.'cart';
	}
    }
	
	public function resolveQueryObject()
	{
		//  if looking at a page
		if (self::requestIsForAdmin())
		{
			return 'Admin';
		}
        else if (self::requestIsForPage())
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
	
	public static function requestIsForAdmin()
	{
		$request = self::parseUrl();
		$pathLen = strlen($request->rawPath);
		$checkUri = CC_ADMIN_URI;
		if (substr($checkUri,-1) == "/") $checkUri = substr($checkUri, 0, strlen($checkUri)-1);
		if ($pathLen >= strlen($checkUri) && strtolower(substr($request->rawPath, 0, $pathLen)) == strtolower($checkUri))
			return true;
		return false;
	}
	
    public static function requestIsForCheckout()
    {
        $request = self::parseUrl();
        if (sizeof($request->path) > 0 && strtolower($request->path[0]) == 'checkout')
            return true;
        return false;
    }
    
    public static function requestIsForCart()
    {
        $request = self::parseUrl();
        if (sizeof($request->path) > 0 && strtolower($request->path[0]) == 'cart')
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