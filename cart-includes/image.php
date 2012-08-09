<?php

/**
 * Image information and manipulation.
*/
class Image
{
    public $sourceFile;
    public $isLocal;
    public $width;
    public $height;
    public $url;
    
    public function __construct($sourceFile, $url, $isLocal, $width, $height)
    {
	$this->sourceFile = $sourceFile;
	$this->isLocal = $isLocal;
	$this->width = $width;
	$this->height = $height;
	$this->url = $url;
    }
}