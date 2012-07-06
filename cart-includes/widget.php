<?php

/**
 * Base widget class. Widgets should extend this class.
 * @abstract
*/
abstract class Widget
{
	abstract public function form($opts);
	abstract public function display($args);
}