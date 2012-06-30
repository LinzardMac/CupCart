<?php

/**
 * A static page.
*/
class Page extends Entity
{
    /**
     * @var string Pseudo-uri used to address the file with an URL.
    */
    public $uri;
    /**
     * @var string Page title.
    */
    public $title;
    /**
     * @var string Page body.
    */
    public $body;
}