<?php

/**
 * Template tag methods.
*/
class TPL
{
    /**
     * @var array Array of product loops available for this request.
    */
    private static $productLoops = array();
    
    /**
     * @var Loop The active product loop.
    */
    private static $activeProductLoop = null;
    
    /**
     * @var TaxonomyTerm The taxonomy the view is displaying if isTaxonomy.
    */
    private static $theTaxonomyTerm = null;
    
    /**
     * Gets the name of the store.
     * @return bool Store name.
    */
    public static function getStoreName()
    {
        return Core::$activeStore->name;
    }
    
    /**
     * Sets the current view as a single taxonomy.
     * @param Taxonomy $taxonomy The current taxonomy.
    */
    public static function setTaxonomyTerm(TaxonomyTerm $taxonomyTerm)
    {
        self::$theTaxonomyTerm = $taxonomyTerm;
    }
    
    /**
     * Gets if the current view is for a single taxonomy term.
     * @return bool
    */
    public static function isTaxonomyTerm()
    {
        return (self::$theTaxonomyTerm != null);
    }
    
    /**
     * Prints the current taxonomy name to the browser.
    */
    public static function theTaxonomyTerm()
    {
        echo self::getTheTaxonomyTerm();
    }
    
    /**
     * Gets the current taxonomy term name.
     * @return string Taxonomy term name.
    */
    public static function getTheTaxonomyTerm()
    {
        if (self::isTaxonomyTerm())
            return Hooks::applyFilter("the_taxonomy", self::$theTaxonomyTerm->name);
        return '';
    }
    
    /**
     * Prints the header to the browser.
     * @param string $name Optional. Name of the header to include.
    */
    public static function theHeader($name = '')
    {
        echo self::getTheHeader($name);
    }
    
    /**
     * Gets the header.
     * @param string $name Optional. Name of the header to include.
     * @return string The header.
    */
    public static function getTheHeader($name = '')
    {
        $file = 'header';
        if ($name != '') $file .= '-'.$name;
        return View::get($file)->render(false);
    }
    
    /**
     * Prints the footer to the browser.
     * @param string $name Optional. Name of the footer to include.
    */
    public static function theFooter($name = '')
    {
        echo self::getTheFooter($name);
    }
    
    /**
     * Gets the footer.
     * @param string $name Optional. Name of the footer to include.
     * @return string The footer.
    */
    public static function getTheFooter($name = '')
    {
        $file = 'footer';
        if ($name != '') $file .= '-'.$name;
        return View::get($file)->render(false);
    }
    
    /**
     * Prints the theme's URL to the browser.
    */
    public static function theThemeUrl()
    {
        echo self::getTheThemeUrl();
    }
    
    /**
     * Gets the theme's URL.
    */
    public static function getTheThemeUrl()
    {
        return Hooks::applyFilter('theme_url', Core::$activeTheme->httpUri);
    }
    
    /**
     * Adds a loop for use with template tag methods.
     * @param Loop $loop The loop to add.
     * @param string $loopName Name of the loop to add, defaults to "default".
    */
    public static function addProductLoop(Loop $loop, $loopName = 'default')
    {
        self::$productLoops[$loopName] = $loop;
    }
    
    /**
     * Determines if a product loop is available and has entities and makes it the active product loop.
     * @param string $loopName Name of the loop to check for. Defaults to "default".
     * @param bool $makeActive When false the checked loop will not be made active.
     * @return bool True when available and usable, false otherwise.
    */
    public static function hasProducts($loopName = 'default', $makeActive = true)
    {
        if (!array_key_exists($loopName, self::$productLoops))
            return false;
        $loop = self::$productLoops[$loopName];
        if (!$loop->hasEntities())
            return false;
        if ($makeActive)
            self::$activeProductLoop = $loop;
        return true;
    }
    
    /**
     * Activates the next product in the active product loop.
     * @return Entity The newly active product.
    */
    public static function theProduct()
    {
        if (self::$activeProductLoop == null)
            return null;
        return self::$activeProductLoop->theEntity();
    }
    
    /**
     * Gets if the active product is the last product in the loop.
     * @return bool
    */
    public static function isLastProduct()
    {
        if (self::$activeProductLoop == null)
            return false;
        if (self::$activeProductLoop->loopPosition() == self::$activeProductLoop->entityCount() - 1)
            return true;
        return false;
    }
    
    /**
     * Gets the current product position pointer for the active product loop.
     * @return int
    */
    public static function productPosition()
    {
        if (self::$activeProductLoop == null)
            return 0;
        return self::$activeProductLoop->loopPosition();
    }
    
    /**
     * Gets <img /> markup for the active product.
     * @return string
    */
    public static function getTheThumbnail($size = '')
    {
        if (self::$activeProductLoop == null)
            return '';
        return self::$activeProductLoop->theThumbnail($size);
    }
    
    /**
     * Gets the URL of the active product.
     * @return string The Url.
    */
    public static function getTheProductUrl()
    {
        if (self::$activeProductLoop == null)
            return '';
        return self::$activeProductLoop->theUrl();
    }
    
    /**
     * Gets the URL to add the active product to the shopping cart.
     * @return string The Url.
    */
    public static function getAddToCartUrl()
    {
        if (self::$activeProductLoop == null)
            return '';
        return self::$activeProductLoop->theAddToCartUrl();
    }
    
    /**
     * Gets the URL of the active product.
     * @return string The Url.
    */
    public static function getTheProductTitle()
    {
        if (self::$activeProductLoop == null)
            return '';
        return self::$activeProductLoop->theTitle();
    }
	
	/**
	 * Prints the URL to an admin page.
	 *
	 * @param mixed $panel Optional. Name, index or Menu instance of admin panel to link to.
	 * @param mixed $page Optional. Name, index or Menu instance of the admin page to link to.
	 * @param array $params Optional. Array of parameters to include in the URI. Up to 8 parameters are supported by the basic router.
	 * @param string $title Optional. A title to be included at the end of the URI. Will be made URL safe.
	 * @param int $entity Optional. An entity GUID or other unique ID to be included with the title in the URI.
	 * @param string $format Optional. The file extension to include at the end of the URL. Defaults to ".html".
	*/
	public static function adminUrl($panel = null, $page = null, $params = array(), $title = null, $entity = null, $format = null)
	{
		echo self::getAdminUrl($panel, $page, $params, $title, $entity, $format);
	}
	
	/**
	 * Gets the URL to an admin page.
	 *
	 * @param mixed $panel Optional. Name, index or Menu instance of admin panel to link to.
	 * @param mixed $page Optional. Name, index or Menu instance of the admin page to link to.
	 * @param array $params Optional. Array of parameters to include in the URI. Up to 8 parameters are supported by the basic router.
	 * @param string $title Optional. A title to be included at the end of the URI. Will be made URL safe.
	 * @param int $entity Optional. An entity GUID or other unique ID to be included with the title in the URI.
	 * @param string $format Optional. The file extension to include at the end of the URL. Defaults to "html".
	 *
	 * @return string URL to the specified admin page.
	*/
	public static function getAdminUrl($panel = null, $page = null, $params = array(), $title = null, $entity = null, $format = null)
	{
		$routerParams = array();
		foreach($params as $i => $param)
			$routerParams['category'.($i + 2)] = $param;
		if ($title != null)
			$routerParams['title'] = Router::title($title);
		if ($entity != null)
			$routerParams['entity'] = $entity;
		if ($format != null)
			$routerParams['format'] = $format;
		if ($title != null && $entity == null)
			$routerParams['entity'] = 0;
		
		if (!($panel instanceof Menu))
		{
			$panel = Menu::getPanel($panel);
		}
		
		if ($panel == null)
			return Router::url('admin', $routerParams);
		
		if (!($page instanceof Menu))
		{
			$page = $panel->getPage($page);
		}
		
		if ($page == null)
			return Router::url('admin', array_merge(array('category1'=>$panel->slug), $routerParams));
		
		return Router::url('admin', array_merge(array('category1'=>$panel->slug, 'category2'=>$page->slug), $routerParams));
	}
}