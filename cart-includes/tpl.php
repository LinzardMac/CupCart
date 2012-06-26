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
     * Gets the name of the store.
     * @return bool Store name.
    */
    public static function getStoreName()
    {
        return Core::$activeStore->name;
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
    public static function getTheUrl()
    {
        if (self::$activeProductLoop == null)
            return '';
        return self::$activeProductLoop->theUrl();
    }
    
    /**
     * Gets the URL of the active product.
     * @return string The Url.
    */
    public static function getTheTitle()
    {
        if (self::$activeProductLoop == null)
            return '';
        return self::$activeProductLoop->theTitle();
    }
}