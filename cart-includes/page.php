<?php

/**
 * A static page.
*/
class Page extends Entity
{
    /**
     * @var string Pseudo-filename used to address the file with an URL.
    */
    public $filename;
    /**
     * @var string Page title.
    */
    public $title;
    /**
     * @var string Page body.
    */
    public $body;
}