<?php

/**
 * Request information.
*/
class Request
{
    /**
     * @var array Array of path information separated by a slash.
    */
    public $path;
    /**
     * @var string Raw path URI.
    */
    public $rawPath;
    /**
     * @var string Filename from path.
    */
    public $file;
    /**
     * @var string Query string.
    */
    public $queryString;
    /**
     * @var string Request method.
    */
    public $method;
    
    public function __construct($url, $method = '')
    {
	if ($method == '')
	    $method = $_SERVER['REQUEST_METHOD'];
	$this->method = $method;
	
        $compare = Core::$activeStore->baseUri;
        if (substr($compare, -1, 1) == "/") $compare = substr($compare, 0, -1);
        if (strtolower(substr($url, 0, strlen($compare))) == $compare)
            $url = substr($url, strlen($compare));
        if ($url[0] != "/") $url = "/".$url;
        if (strpos($url, "://") === false)
            $url = 'http://hostname'.$url;
        $urlInfo = parse_url($url);
        $this->rawPath = '';
        $this->queryString = arr::get($urlInfo, 'query', '');
        $bits = explode("/", arr::get($urlInfo, 'path', ''));
        $this->path = array();
        foreach($bits as $bit)
        {
            if ($bit != "")
            {
                $bit = rawurldecode($bit);
                $this->rawPath .= '/'.$bit;
                $this->path[] = $bit;
            }
        }
        $this->file = '';
        if (sizeof($this->path) > 0)
            $this->file = $this->path[sizeof($this->path)-1];
    }
}