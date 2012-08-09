<?php

/**
 * Hooks API.
 * Support for filters and actions.
*/
class Hooks
{
    /**
     * Filter stack, keyed by stack name.
    */
    private static $filterStack = array();
    
    /**
     * Action stack.
    */
    private static $actionStack = array();
    
    /**
     * Adds an action callback to the specified action's stack.
     * @param string $actionName Name of the action.
     * @param callable $callback Function or method to invoke when action is run.
     * @param int $priority Priority of the callback. Lower priority callbacks are run first. Defaults to 5.
    */
    public static function addAction($actionName, $callback, $priority = 5)
    {
        $actionName = strtolower($actionName);
        
        if (!array_key_exists($actionName, self::$actionStack))
            self::$actionStack[$actionName] = array();
        
        $actionStack = self::$actionStack[$actionName];
        if (!array_key_exists($priority, $actionStack))
            $actionStack[$priority] = array();
        
        $actionStack[$priority][] = $callback;
        
        self::$actionStack[$actionName] = self::sortStack($actionStack);
    }
    
    /**
     * Removes an action callback from the specified action's stack.
     * @param string $actionName Name of the action.
     * @param callable $callback Function or method registered on the action's stack.
    */
    public static function removeAction($actionName, $callback)
    {
        
    }
    
    /**
     * Runs an action, invoking all callbacks in the action's stack.
     * @param string $actionName Name of the action to invoke.
    */
    public static function doAction($actionName)
    {
        $actionName = strtolower($actionName);
        if (!array_key_exists($actionName, self::$actionStack))
            return;
        $args = func_get_args();
        array_shift($args);
        
        foreach(self::$actionStack[$actionName] as $prio => $callbacks)
        {
            foreach($callbacks as $callback)
            {
                call_user_func_array($callback, $args);
            }
        }
    }
    
    /**
     * Adds a filter callback to the specified filter's stack.
     * @param string $filterName Name of the filter.
     * @param callable $callback Function or method to invoke when filter is applied.
     * @param int $priority Priority of the callback. Lower priority callbacks are run first. Defaults to 5.
    */
    public static function addFilter($filterName, $callback, $priority = 5)
    {
        if (!array_key_exists($filterName, self::$filterStack))
            self::$filterStack[$filterName] = array();
        
        $filterStack = self::$filterStack[$filterName];
        if (!array_key_exists($priority, $filterStack))
            $filterStack[$priority] = array();
        
        $filterStack[$priority][] = $callback;
        
        self::$filterStack[$filterName] = self::sortStack($filterStack);
    }
    
    /**
     * Removes a filter callback from the specified filter's stack.
     * @param string $filterName Name of the filter.
     * @param callable $callback Function or method registered on the filter's stack.
    */
    public static function removeFilter($filterName, $callback)
    {
        
    }
    
    /**
     * Applies filters to a value using the specified filter stack.
     * @param string $filterName Name of the filter stack.
     * @param mixed $value Value/object to filter.
    */
    public static function applyFilter($filterName, $value)
    {
        if (!array_key_exists($filterName, self::$filterStack))
            return $value;
	$args = func_get_args();
        array_shift($args);
	array_shift($args);
        $filters = self::$filterStack[$filterName];
        $newValue = $value;
        foreach($filters as $prio => $callbacks)
        {
            foreach($callbacks as $callback)
            {
		$callbackArgs = array($newValue);
		foreach($args as $val)
		    $callbackArgs[] = $val;
                $newValue = call_user_func_array($callback, $callbackArgs);
            }
        }
        return $newValue;
    }
    
    /**
     * Sorts a stack based on priority.
     * @param array $stack Stack to sort.
     * @return array Stack sorted by priority.
    */
    private static function sortStack($stack)
    {
        $sortedStack = array();
        $keys = array_keys($stack);
        asort($keys);
        foreach($keys as $key)
        {
            $sortedStack[$key] = $stack[$key];
        }
        return $sortedStack;
    }
}