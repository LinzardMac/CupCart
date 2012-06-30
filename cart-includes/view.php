<?php

/**
 * Handles rendering of views.
*/
class View
{
    /**
     * @var array Array of global variables available to all views.
    */
    private static $global = array();
    
    /**
     * @var string Name of the view.
    */
    public $name;
    
    /**
     * @var array Array of assigned variables.
    */
    private $vars;
    
    /**
     * Gets a view.
     * @param string $viewName Name of the view to get, by default tries to resolve a view name based on active controller.
     * @return View The found view, null if no view is found.
    */
    public static function get($viewName = '')
    {
        if ($viewName == '')
        {
            $viewName = get_class(Core::$activeController);
            if (substr($viewName, 0, 11) == 'Controller_')
                $viewName = substr($viewName, 11);
            if (!self::exists($viewName))
            {
                $testClass = Core::$activeController;
                while (true)
                {
                    $testClass = get_parent_class($testClass);
                    $viewName = $testClass;
                    if ($testClass == false || $testClass == "Controller")
                    {
                        $viewName = '';
                        break;
                    }
                    if (substr($viewName, 0, 11) == 'Controller_')
                        $viewName = substr($viewName, 11);
                    if (View::exists($viewName))
                        break;
                }
            }
        }
        
        $viewName = Hooks::applyFilter('get_view', $viewName);
        
        if ($viewName == '' || !self::exists($viewName))
        {
            if (self::exists("index"))
                $viewName = 'index';
            else
                return null;
        }
        
        $view = new View();
        $view->name = $viewName;
        return $view;
    }
    
    /**
     * Determines if a view exists.
     * @param string $viewName The view's name.
     * @return bool True if the view exists, false otherwise.
    */
    public static function exists($viewName)
    {
        $fileName = Core::$activeTheme->localUri.strtolower($viewName).'.php';
        if (file_exists($fileName) && is_file($fileName))
            return true;
        return false;
    }
    
    /**
     * Assigns a global variable to all views.
     * @param mixed $name Name of variable to assign or an array of variables to assign.
     * @param mixed $value The variable to assign.
    */
    public static function setGlobal($name, $value = null)
    {
        if (is_array($name))
        {
            foreach($name as $key => $val)
            {
                self::$global[$key] = $val;
            }
        }
        else
        {
            self::$global[$name] = $value;
        }
    }
    
    public function __construct()
    {
        $this->name = '';
        $this->vars = array();
    }
    
    /**
     * Assigns a variable to the view.
     * @param mixed $name Name of variable to assign or an array of variables to assign.
     * @param mixed $value The variable to assign.
    */
    public function set($name, $value = null)
    {
        if (is_array($name))
        {
            foreach($name as $key => $val)
            {
                $this->vars[$key] = $val;
            }
        }
        else
        {
            $this->vars[$name] = $value;
        }
        return $this;
    }
    
    /**
     * Renders a view.
     * @param bool $echo If true the view is rendered directly to the HTTP client. Defaults to true.
     * @return mixed Null if $echo is true, the HTML markup is $echo is false.
    */
    public function render($echo = true)
    {
        extract(self::$global);
        extract($this->vars);
        if (!$echo)
        {
            ob_start();
        }
        include(Core::$activeTheme->localUri.strtolower($this->name).'.php');
        if (!$echo)
        {
            $markup = ob_get_contents();
            ob_end_clean();
            return $markup;
        }
    }
}