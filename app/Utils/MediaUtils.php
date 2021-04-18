<?php

namespace App\Utils;

class MediaUtils
{

    /**
     * Get aggregate types recognized by the application
     */
    public static function isImage($mimeType)
    {
        $imageMime = array('image/jpeg', 'image/png', 'image/svg+xml');
        $result = in_array($mimeType, $imageMime);

        return $result;
    }
}
