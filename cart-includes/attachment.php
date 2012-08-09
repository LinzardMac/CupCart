<?php

/**
 * Image and other types of uploaded attachments.
*/
class Attachment extends Entity
{
    const TYPE_IMAGE = 'image';
    const TYPE_DOWNLOAD = 'download';

    /**
     * @var string Attachment type. Use the Attachment::TYPE_* constants.
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