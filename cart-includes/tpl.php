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
     * @var Taxonomy The taxonomy the view is displaying if isTaxonomy.
    */
    private static $theTaxonomy = null;
    
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
    public static function setTaxonomy(Taxonomy $taxonomy)
    {
        self::$theTaxonomy = $taxonomy;
    }
    
    /**
     * Gets if the current view is for a single taxonomy.
     * @return bool
    */
    public static function isTaxonomy()
    {
        return (self::$theTaxonomy != null);
    }
    
    /**
     * Prints the current taxonomy name to the browser.
    */
    public static function theTaxonomy()
    {
        echo self::getTheTaxonomy();
    }
    
    /**
     * Gets the current taxonomy name.
     * @return string Taxonomy name.
    */
    public static function getTheTaxonomy()
    {
        if (self::isTaxonomy())
            return Hooks::applyFilter("the_taxonomy", self::$theTaxonomy->name);
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
     * Gets the URL of the active product.
     * @return string The Url.
    */
    public static function getTheProductTitle()
    {
        if (self::$activeProductLoop == null)
            return '';
        return self::$activeProductLoop->theTitle();
    }
}