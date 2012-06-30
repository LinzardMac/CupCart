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
    
    public function __construct($url)
    {
        if (substr($url, 0, (strlen(ROOT_URI) + strlen(INDEX_FILE))) == ROOT_URI.INDEX_FILE)
            $url = substr($url, (strlen(ROOT_URI) + strlen(INDEX_FILE)));
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
                $this->rawPath .= '/'.$bit;
                $this->path[] = $bit;
            }
        }
        $this->file = '';
        if (sizeof($this->path) > 0)
            $this->file = $this->path[sizeof($this->path)-1];
    }
}