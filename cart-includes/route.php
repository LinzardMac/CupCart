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
	 * Generates a URI for the current route based on the parameters given.
	 *
	 *     // Using the "default" route: "users/profile/10"
	 *     $route->uri(array(
	 *         'controller' => 'users',
	 *         'action'     => 'profile',
	 *         'id'         => '10'
	 *     ));
	 *
	 * @param   array   URI parameters
	 * @return  string
	 * @throws  Kohana_Exception
	 * @uses    Route::REGEX_Key
	 */
	public function uri($params = NULL)
	{
	    if ($params instanceof RouteInfo)
	    {
		$array = array('controller'=>$params->controller,
		    'action'=>$params->action,
		    'format'=>$params->format,
		    'entity'=>$params->entity);
		if (is_array($params->params))
		{
		    foreach($params->params as $key => $val)
			$array[$key] = $val;
		}
		$params = $array;
		$pos = strpos($array['action'], '_');
		if ($pos !== false)
		    $params['action'] = substr($params['action'], $pos + 1);
	    }
	    
	    if (array_key_exists('entity', $params) && $params['entity'] != null
		&& $params['entity'] instanceof Entity)
		$params['entity'] = $params['entity']->guid;
		
	    //  fill params with defaults
	    foreach($this->_defaults as $key => $val)
	    {
		if ($val == '') continue;
		if (!array_key_exists($key, $params))
		    $params[$key] = $val;
	    }
	
	    // Start with the routed URI
	    $uri = $this->uri;

	    if (strpos($uri, '<') === FALSE AND strpos($uri, '(') === FALSE)
	    {
		    // This is a static route, no need to replace anything

		    //if ( ! $this->is_external())
		    //    return $uri;

		    // If the localhost setting does not have a protocol
		    /*if (strpos($this->_defaults['host'], '://') === FALSE)
		    {
			    // Use the default defined protocol
			    $params['host'] = Route::$default_protocol.$this->_defaults['host'];
		    }
		    else
		    {
			    // Use the supplied host with protocol
			    $params['host'] = $this->_defaults['host'];
		    }*/

		    // Compile the final uri and return it
		    //return rtrim($params['host'], '/').'/'.$uri;
		    return '';
	    }

	    while (preg_match('#\([^()]++\)#', $uri, $match))
	    {
		    // Search for the matched value
		    $search = $match[0];

		    // Remove the parenthesis from the match as the replace
		    $replace = substr($match[0], 1, -1);

		    while (preg_match('#'.Route::REGEX_KEY.'#', $replace, $match))
		    {
			    list($key, $param) = $match;

			    if (isset($params[$param]))
			    {
				    // Replace the key with the parameter value
				    $replace = str_replace($key, $params[$param], $replace);
			    }
			    else
			    {
				    // This group has missing parameters
				    $replace = '';
				    break;
			    }
		    }

		    // Replace the group in the URI
		    $uri = str_replace($search, $replace, $uri);
	    }

	    while (preg_match('#'.Route::REGEX_KEY.'#', $uri, $match))
	    {
		    list($key, $param) = $match;

		    if ( ! isset($params[$param]))
		    {
			    // Look for a default
			    if (isset($this->_defaults[$param]))
			    {
				    $params[$param] = $this->_defaults[$param];
			    }
			    else
			    {
				    // Ungrouped parameters are required
				    throw new Kohana_Exception('Required route parameter not passed: :param', array(
					    ':param' => $param,
				    ));
		    }
		    }

		    $uri = str_replace($key, $params[$param], $uri);
	    }

	    // Trim all extra slashes from the URI
	    $uri = preg_replace('#//+#', '/', rtrim($uri, '/'));

	    /*if ($this->is_external())
	    {
		    // Need to add the host to the URI
		    $host = $this->_defaults['host'];

		    if (strpos($host, '://') === FALSE)
		    {
			    // Use the default defined protocol
			    $host = Route::$default_protocol.$host;
		    }

		    // Clean up the host and prepend it to the URI
		    $uri = rtrim($host, '/').'/'.$uri;
	    }*/

	    return $uri;
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
	return new RouteInfo($this, $controller, $action, $format, $entity, $params);
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