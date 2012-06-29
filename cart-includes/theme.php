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
        return Hooks::applyFilter('active_theme', self::getThemeByShortName('twentytwelve'));
    }
    
    /**
     * Gets a theme by name.
     * @param string $themeShortName Name of the theme to get.
     * @return Theme The theme.
    */
    public static function getThemeByShortName($themeShortName)
    {
        $theme = new Theme();
        $theme->localUri = THEMES_DIR.$themeShortName.DIRECTORY_SEPARATOR;
        $theme->httpUri = THEMES_URI.$themeShortName.'/';
        
        if (file_exists($theme->localUri.'style.css'))
        {
            $meta = File::metaData($theme->localUri.'style.css');
            $theme->name = arr::get($meta, 'Theme Name', '');
            $theme->version = arr::get($meta, 'Version', '');
        }

        return $theme;
    }
}