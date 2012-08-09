<?php

class Route
{

    // Defines the pattern of a <segment>
    const REGEX_KEY     = '<([a-zA-Z0-9_]++)>';

    // What can be part of a <segment> value
    const REGEX_SEGMENT = '[^/.,;?\n]++';

    // What must be escaped in the route regex
    const REGEX_ESCAPE  = '[.\\+*?[^\\]${}=!|]';
    
    public static function factory($name, $uri)
    {
	return new Route($name, $uri);
    }
    
    public $name;
    public $uri;
    
    private $_defaults;
	
    public function __construct($name, $uri)
    {
	$this->name = $name;
	$this->uri = $uri;
	$this->_defaults = array();
    }
    
    public function defaults($defaults)
    {
	$this->_defaults = $defaults;
	return $this;
    }
    
    /**
     * Attemptes to match the request to the route.
     * @param Request $request
     * @return mixed A populated [RouteInfo] object if matching, null otherwise.
    */
    public function match(Request $request)
    {
	$regex = $this->compile($this->uri);
	$uri = $request->rawPath;
	if (strlen($uri) > 0 && $uri[0] == '/') $uri = substr($uri, 1);
    
	if ( ! preg_match($regex, $uri, $matches))
	    return null;

	$controller = $action = $format = '';
	$entity = null;
	$params = array();
	foreach ($matches as $key => $value)
	{
	    if (is_int($key))
	    {
		// Skip all unnamed keys
		continue;
	    }

	    // Set the value for all matched keys
	    $params[$key] = $value;
	}

	foreach ($this->_defaults as $key => $value)
	{
		if ( ! isset($params[$key]) OR $params[$key] === '')
		{
			// Set default values for any key that was not matched
			$params[$key] = $value;
		}
	}
	
	if (array_key_exists('controller', $params))
	{
	    $controller = $params['controller'];
	    unset($params['controller']);
	}
	if (array_key_exists('action', $params))
	{
	    $action = $params['action'];
	    unset($params['action']);
	}
	if (array_key_exists('format', $params))
	{
	    $format = $params['format'];
	    unset($params['format']);
	}
	if (array_key_exists('entity', $params))
	{
	    $entity = Entity::getByGuid(intval($params['entity']));
	    unset($params['entity']);
	}
	if ($action != '')
	    $action = strtolower($request->method).'_'.$action;
	return new RouteInfo($controller, $action, $format, $entity, $params);
    }
    
    /**
     * Returns the compiled regular expression for the route. This translates
     * keys and optional groups to a proper PCRE regular expression.
     *
     *     $compiled = Route::compile(
     *        '<controller>(/<action>(/<id>))',
     *         array(
     *           'controller' => '[a-z]+',
     *           'id' => '\d+',
     *         )
     *     );
     *
     * @return  string
     * @uses    Route::REGEX_ESCAPE
     * @uses    Route::REGEX_SEGMENT
     */
    public function compile($uri, array $regex = NULL)
    {
	    if ( ! is_string($uri))
		    return;

	    // The URI should be considered literal except for keys and optional parts
	    // Escape everything preg_quote would escape except for : ( ) < >
	    $expression = preg_replace('#'.Route::REGEX_ESCAPE.'#', '\\\\$0', $uri);

	    if (strpos($expression, '(') !== FALSE)
	    {
		    // Make optional parts of the URI non-capturing and optional
		    $expression = str_replace(array('(', ')'), array('(?:', ')?'), $expression);
	    }

	    // Insert default regex for keys
	    $expression = str_replace(array('<', '>'), array('(?P<', '>'.Route::REGEX_SEGMENT.')'), $expression);

	    if ($regex)
	    {
		    $search = $replace = array();
		    foreach ($regex as $key => $value)
		    {
			    $search[]  = "<$key>".Route::REGEX_SEGMENT;
			    $replace[] = "<$key>$value";
		    }

		    // Replace the default regex with the user-specified regex
		    $expression = str_replace($search, $replace, $expression);
	    }

	    return '#^'.$expression.'$#uD';
	}
}