<?php
/*
Plugin Name: Hello Molly
Version: 1.0
Author: John Carruthers
Description: Basic plugin example. Drop into mu-plugins or plugins (requires activation) to use. Goto any store URL with ?molly=true to see it work.
*/

class HelloMolly_Plugin
{
    private static $_instance = null;
    
    public static function instance()
    {
        if (self::$_instance == null)
            self::$_instance = new HelloMolly_Plugin();
        return self::$_instance;
    }
    
    public function __construct()
    {
        if (arr::get($_GET,'molly') == 'true')
        {
            Hooks::addAction("muplugins_loaded", array($this, 'mupluginsloaded'));
            Hooks::addAction("plugins_loaded", array($this, 'pluginsloaded'));
            Hooks::addFilter("resolve_controller", array($this,"controlleroverride"));
        }
    }
    
    public function controlleroverride($controller)
    {
        return 'Controller_Molly';
    }
    
    public function mupluginsloaded()
    {
        echo 'Mu plugins loaded<br />';
    }
    
    public function pluginsloaded()
    {
        echo 'Plugins loaded<br />';
    }
}
HelloMolly_Plugin::instance();

class Controller_Molly extends Controller
{
    public function get_index()
    {
        echo "Molly says hi!";
    }
}