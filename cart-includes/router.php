<?php

/**
 * Defines a router.
 * @abstract
*/
abstract class Router
{
	/**
	 * Resolve the current queried object.
	 * @return mixed An entity object or controller name as a string.
	*/
	public static function resolveQueryObject() { return null; }
}