<?php

/**
 * Various file related operations.
*/
class File
{
    /**
     * Loads metadata stored in comments from the first 4KB of a file.
     * @param string $filename File to load data from.
     * @param resource $context Context to pass to fopen. Optional.
     * @return array An array of meta keys and values.
    */
    public static function metaData($filename, $context = null)
    {
        $meta = array();
        
        if ($context === null)
            $fp = fopen($filename, 'r', false);
        else
            $fp = fopen($filename, 'r', false, $context);
        
        $scanSize = 4096;
        $scanData = fread($fp, $scanSize);
        fclose($fp);
        
        $scanData = str_replace("\r", "\n", $scanData);
        
        $lines = explode("\n", $scanData);
        $inComments = false;
        foreach($lines as $line)
        {
            $stop = false;
            $line = trim($line);
            if (!$inComments && ($pos = strpos($line, '/*')) !== false)
            {
                $inComments = true;
                $line = substr($line, $pos + 2);
            }
            else if ($inComments && ($pos = strpos($line, '*/')) !== false)
            {
                $line = substr($line, 0, $pos);
                $stop = true;
            }
            
            if ($inComments)
            {
                $pos = strpos($line, ":");
                if ($pos !== false)
                {
                    $key = trim(substr($line, 0, $pos));
                    $value = trim(substr($line, $pos + 1));
                    $meta[$key] = $value;
                }
            }
            
            if ($stop)
                $inComments = false;
        }
        
        return $meta;
    }
}