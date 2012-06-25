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
     * Gets the currently active theme.
     * @return Theme The currently active theme.
    */
    public static function getActiveTheme()
    {
        //  twentyten good enough for WP so...
        return Hooks::applyFilter('active_theme', self::getThemeByName('twentytwelve'));
    }
    
    /**
     * Gets a theme by name.
     * @param string $themeName Name of the theme to get.
     * @return Theme The theme.
    */
    public static function getThemeByName($themeName)
    {
        $theme = new Theme();
        $theme->name = $themeName;
        $theme->version = '1.0';
        $theme->localUri = THEMES_DIR.$theme->name.DIRECTORY_SEPARATOR;
        $theme->httpUri = THEMES_URI.$theme->name.'/';
        return $theme;
    }
}