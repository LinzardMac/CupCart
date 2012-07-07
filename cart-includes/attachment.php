<?php

/**
 * Image and other types of uploaded attachments.
*/
class Attachment extends Entity
{
    /**
     * @var string Attachment type. Built in types are "image" and "binary" (downloadable attachments).
    */
    public $type;
    
    /**
     * @var string Full URL to the file.
    */
    public $fileUrl;
    
    /**
     * @var bool True if the image is stored on the local hard disk drive.
    */
    public $isLocal;
    
    /**
     * @var string Uri to the file, relative to the document root. Only applicable if $isLocal is true.
    */
    public $fileUri;
    
    /**
     * @var string Name of the file, user can change this.
    */
    public $filename;
    
    /**
     * @var string File title.
    */
    public $title;
    
    /**
     * @var string Description of the file.
    */
    public $description;
}