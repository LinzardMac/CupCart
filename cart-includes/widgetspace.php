<?php

/**
 * Widget spaces API. Synonymous with WordPress register_sidebar functionality.
*/
class WidgetSpace
{
    /**
     * @var array Array of registered widget spaces.
    */
    private static $spaces = array();
    
    /**
     * @var string Name of the WidgetSpace.
    */
    public $name;
    /**
     * @var string HTML ID of the WidgetSpace.
    */
    public $id;
    /**
     * @var string Description of the WidgetSpace.
    */
    public $description;
    /**
     * @var string HTML markup prending widgets.
    */
    public $beforeWidget;
    /**
     * @var string HTML markup appending widgets.
    */
    public $afterWidget;
    /**
     * @var string HTML markup prepended to widget titles.
    */
    public $beforeTitle;
    /**
     * @var string HTML markup appended to widget titles.
    */
    public $afterTitle;
	/**
	 * @var array Array of widget class names.
	*/
	public $widgets;
	/**
	 * @var array Array of widget options.
	*/
	public $widgetOpts;
    
    /**
     * Register a widget space.
     * @param mixed $name Either a widget name or a WidgetSpace instance.
     * @param string $id Optional. HTML id to use for widgetspace html.
     * @param string $description Optional. Description of the widgetspace.
     * @param string $beforeWidget Optional. HTML markup for the header of widgets in the widgetspace.
     * @param string $afterWidget Optional. HTML markup for the footer of widgets in the widgetspace.
     * @param string $beforeTitle Optional. HTML markup prepending widget titles.
     * @param string $afterTitle Optional. HTML markup appending widget titles.
    */
    public static function register($name = '', $id = '',
        $description = '',  $beforeWidget = '', $afterWidget = '',
        $beforeTitle = '', $afterTitle = '')
    {
        $obj = $name;
        if (!($obj instanceof WidgetSpace))
        {
            $obj = new WidgetSpace();
            $obj->name = $name;
            $obj->id = $id;
            $obj->description = $description;
            $obj->beforeWidget = $beforeWidget;
            $obj->afterWidget = $afterWidget;
            $obj->beforeTitle = $beforeTitle;
            $obj->afterTitle = $afterTitle;
        }
        
        $i = sizeof(self::$spaces) + 1;
        
        $defaults = Hooks::applyFilter("widgetspace_defaults", array(
            'name'          => 'Sidebar '.$i,
            'id'            => 'sidebar-'.$i,
            'description'   => '',
            'beforeWidget'  => '<li id="%s" class="widget">',
            'afterWidget'   => '</li>',
            'beforeTitle'   => '<h3 class="widget title">',
            'afterTitle'    => '</h3>'
        ));
        
        foreach($defaults as $key => $val)
        {
            if ($obj->{$key} == '')
            {
                $obj->{$key} = $defaults[$key];
            }
        }
        
        self::$spaces[$obj->name] = $obj;
    }
	
	/**
	 * Adds a widget to the widget space.
	 * @param string $widget The widget to add.
	 * @param array $opts The array of options stored for this widget instance.
	*/
	public function add($widget, $opts)
	{
		if (class_exists($widget) && Utils::classExtends($widget, 'Widget'))
		{
			$this->widgets[] = $widget;
			$this->widgetOpts[] = $opts;
		}
	}
	
	/**
	 * Gets an array of all widget spaces.
	 * @return array
	*/
	public static function getAll()
	{
		return self::$spaces;
	}
    
    /**
     * Prints the specified WidgetSpace to the browser.
     * @param string $name Optional. Name of WidgetSpace to print.
    */
    public static function theSpace($name = '')
    {
        echo self::getTheSpace($name);
    }
    
    /**
     * Get the specified WidgetSpace to the browser.
     * @param string $name Optional. Name of WidgetSpace to get.
     * @return string
    */
    public static function getTheSpace($name = '')
    {
		$space = null;
		if (array_key_exists($name, self::$spaces))
		{
			$space = self::$spaces[$name];
		}
		else
		{
			//  try and find a default
		}
		
		$html = '<ul>';
		foreach($space->widgets as $index => $widgetClass)
		{
			$opts  = $space->widgetOpts[$index];
			$obj = new $widgetClass();
			$args = array(
				'beforeTitle'	=> sprintf($space->beforeTitle, $space->id),
				'afterTitle'	=> sprintf($space->afterTitle, $space->id),
				'beforeWidget'	=> sprintf($space->beforeWidget, $space->id),
				'afterWidget'	=> sprintf($space->afterWidget, $space->id)
			);
			foreach($opts as $name => $value)
				$args[$name] = $value;
			ob_start();
			$obj->display($args);
			$html .= ob_get_contents();
			ob_end_clean();
		}
		$html .= '</ul>';
        return $html;
    }
}