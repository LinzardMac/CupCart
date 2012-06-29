<?php

/**
 * Plugin loading API.
*/
class Plugin
{
    /**
     * @var string Source file.
    */
    public $file;
    /**
     * @var string Name of the plugin.
    */
    public $name;
    /**
     * @var string Description of plugin.
    */
    public $description;
    /**
     * @var string Version of the plugin.
    */
    public $version;
    /**
     * @var string Author name.
    */
    public $author;
    /**
     * @var string Author's website.
    */
    public $authorUrl;
    /**
     * @var string Author's email address.
    */
    public $authorEmail;
    /**
     * @var string Plugin's website.
    */
    public $website;
    
    /**
     * Gets an array of active plugins.
     * @return array Array of Plugin instances.
    */
    public static function getActive()
    {
        $ret = array();
        return $ret;
    }
    
    /**
     * Get a list of plugins in the given directory.
     * @param string $dir Directory to find plugins in.
     * @return array Array of Plugin instances.
    */
    public static function getList($dir)
    {
        $ret = array();
        
        $files = File::getFiles($dir);
        foreach($files as $file)
        {
            $meta = File::metaData($file);
            $obj = self::createInstance($file, $meta);
            if ($obj != null)
                $ret[] = $obj;
        }
        $dirs = File::getDirectories($dir);
        foreach($dirs as $dir)
        {
            if (file_exists($dir.'plugin.php'))
            {
                $meta = File::metaData($dir.'plugin.php');
                $obj = self::createInstance($dir.'plugin.php', $meta);
                if ($obj != null)
                    $ret[] = $obj;
            }
        }
        
        return $ret;
    }
    
    /**
     * Creates a [Plugin] instance from filename and meta data.
     * @return Plugin
    */
    private static function createInstance($filename, $meta)
    {
        $obj = new Plugin();
        $obj->file = $filename;
        if (arr::get($meta, 'Plugin Name') == null)
            return null;
        $obj->name = arr::get($meta, 'Plugin Name');
        $obj->description = arr::get($meta, 'Description');
        $obj->version = arr::get($meta, 'Version');
        $obj->author = arr::get($meta, 'Author');
        $obj->authorUrl = arr::get($meta, 'Author Url');
        $obj->authorEmail = arr::get($meta, 'Author Email');
        $obj->website = arr::get($meta, 'Website');
        return $obj;
    }
    
    /**
     * Loads the plugin.
    */
    public function load()
    {
        include($this->file);
    }
}