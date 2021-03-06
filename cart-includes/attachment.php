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
     * @var string Full URL to the file's directory. MUST end with a slash.
    */
    public $directoryUrl;
    
    /**
     * @var bool True if the image is stored on the local hard disk drive.
    */
    public $isLocal;
    
    /**
     * @var string Uri to the file's directory, relative to the document root. Only applicable if $isLocal is true. MUST end with a slash.
    */
    public $directoryUri;
    
    /**
     * @var string Name of the file.
    */
    public $filename;
    
    /**
     * @var string Custom filename, used when downloading files.
    */
    public $customFilename;
    
    /**
     * @var string File title.
    */
    public $title;
    
    /**
     * @var string Description of the file.
    */
    public $description;
    
    /**
     * @var array Array of thumbnails in the format array("WxH"=>"filename", ...) - thumbnails are in the same directory as the original file.
    */
    public $thumbnails = array();
    
    /**
     * Gets a thumbnail [Image] if it exists with the given width and height.
     * @param int $width Thumbnail width
     * @param int $height Thumbnail height
     * @return Image
    */
    public function getThumbnail($width, $height)
    {
	$fileName = '';
	$thumbnails = $this->thumbnails;
	if (!is_array($thumbnails))
	    return null;
	$dimensions = $width.'x'.$height;
	if (!array_key_exists($dimensions, $thumbnails))
	    return null;
	$fileName = trim($thumbnails[$dimensions]);
	if ($fileName == '') return null;
	$fileDir = $this->directoryUri;
	if (!$this->isLocal)
	    $fileDir = $this->directoryUrl;
	return new Image($fileDir.$fileName, $this->directoryUrl.$fileName, $this->isLocal, $width, $height);
    }
}