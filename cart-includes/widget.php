<?php

/**
 * Base widget class and API. Widgets should extend this class.
 * @abstract
*/
abstract class Widget
{
	abstract public function form($opts);
	abstract public function display($args);
	
	/**
	 * @var array Array of registered widgets.
	*/
	private static $registeredWidgets = array();
	
	/**
	 * Register a widget for use in WidgetSpaces.
	 * @param string $widgetClass Class name of the widget.
	*/
	public static function register($widgetClass)
	{
		if (class_exists($widgetClass) && Utils::classExtends($widgetClass, 'Widget'))
		{
			self::$registeredWidgets[] = $widgetClass;
		}
	}
}