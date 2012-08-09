<?php

/**
 * Describes a theme.
*/
class Theme
{
    /**
     * @var string Name of the theme.
    */
    public $name;
    /**
     * @var string Version of the theme.
    */
    public $version;
    /**
     * @var string Location of the theme's directory on the local filesystem.
    */
    public $localUri;
    /**
     * @var string Location of the theme's directory relative to the document root.
    */
    public $httpUri;
    /**
     * @var array Array of supported thumbnail sizes.
    */
    public $thumbnailSizes;
    
    /**
     * Gets thumbnail sizes.
    */
    public function getThumbnailSizes()
    {
	return array_keys($this->thumbnailSizes);
    }
    
    /**
     * Gets thumbnail size if it exists.
    */
    public function getThumbnailSize($name)
    {
	if (array_key_exists($name, $this->thumbnailSizes))
	    return $this->thumbnailSizes[$name];
	return null;
    }
    
    /**
     * Gets all installed and useable themes.
     * @return array Array of themes.
    */
    public static function getAll()
    {
        $ret = array();
        $dirs = File::getDirectories(CC_THEMES_DIR);
        foreach($dirs as $dir)
        {
            $shortName = basename($dir);
            $theme = self::getByShortName($shortName);
            if (self::isUseable($theme))
                $ret[] = $theme;
        }
        return $ret;
    }
    
    /**
     * Gets if the specified theme is useable.
     * @param mixed Either the short name of the theme or a [Theme] instance.
     * @return bool If theme is not broken and is allowed returns true.
    */
    public static function isUseable($theme)
    {
        if (!($theme instanceof Theme)) $theme = self::getByShortName($theme);
        if ($theme == null) return false;
        
        if (self::isBroken($theme)) return false;
        if (!self::isAllowed($theme)) return false;
        
        return true;
    }
    
    /**
     * Gets if the specified theme is broken.
     * @param mixed Either the short name of the theme or a [Theme] instance.
     * @return bool
    */
    public static function isBroken($theme)
    {
        if (!($theme instanceof Theme)) $theme = self::getByShortName($theme);
        if ($theme == null) return false;
        
        if ($theme->name == '') return true;
        
        return false;
    }
    
    /**
     * Gets if the theme is allowed to be used.
     * @param mixed Either the short name of the theme or a [Theme] instance.
     * @return bool
    */
    public static function isAllowed($theme)
    {
        if (!($theme instanceof Theme)) $theme = self::getByShortName($theme);
        if ($theme == null) return false;
        
        return true;
    }
    
    /**
     * Gets the currently active theme.
     * @var bool $admin If the current request is for an admin page.
     * @return Theme The currently active theme.
    */
    public static function getActive($admin = false)
    {
        if ($admin)
        {
            return Hooks::applyFilter('active_admin_theme', self::getByShortName('default', true));
        }
        else
        {
            //  twentyten good enough for WP so...
            $theme = 'twentytwelve';
            if (self::isUseable('wootique'))
                $theme = 'wootique';
            return Hooks::applyFilter('active_theme', self::getByShortName($theme));
        }
    }
    
    /**
     * Gets a theme by name.
     * @param string $themeShortName Name of the theme to get.
     * @param bool $admin If the theme is an admin theme.
     * @return Theme The theme.
    */
    public static function getByShortName($themeShortName, $admin = false)
    {
        $theme = new Theme();
        
        if ($admin)
        {
            $theme->localUri = CC_ADMIN_THEMES_DIR.$themeShortName.DIRECTORY_SEPARATOR;
            $theme->httpUri = CC_ADMIN_THEMES_URI.$themeShortName.'/';
        }
        else
        {
            $theme->localUri = CC_THEMES_DIR.$themeShortName.DIRECTORY_SEPARATOR;
            $theme->httpUri = CC_THEMES_URI.$themeShortName.'/';
        }
        
        if (file_exists($theme->localUri.'style.css'))
        {
            $meta = File::metaData($theme->localUri.'style.css');
            $theme->name = arr::get($meta, 'Theme Name', '');
            $theme->version = arr::get($meta, 'Version', '');
	    $theme->thumbnailSizes = array();
	    $thumbStr = arr::get($meta, 'Thumbnails', '');
	    if ($thumbStr != '')
	    {
		$bits = explode(",", $thumbStr);
		foreach($bits as $bit)
		{
		    if (trim($bit)=='') continue;
		    list($name, $size) = explode(" ", trim($bit));
		    list($width,$height) = explode("x", $size);
		    $theme->thumbnailSizes[$name] = array($width, $height);
		}
	    }
        }

        return $theme;
    }
    
    /**
     * Sets up the theme ready to be used.
     * @param Theme $theme Theme to boot.
    */
    public static function bootstrap($theme)
    {
        if (file_exists($theme->localUri.'functions.php'))
        {
            include($theme->localUri.'functions.php');
        }
    }
}