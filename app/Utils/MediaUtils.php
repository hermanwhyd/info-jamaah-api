<?php

namespace App\Utils;

use App\Models\Jamaah;

class MediaUtils
{

    public static function getPhotoUrlOrDefault($media)
    {
        $list[Jamaah::MEDIA_TAG_CLOSEUP] = $media ? $media->getUrl() : env('APP_URL') . '/media/profile/anonymous-user.png';
        $list[Jamaah::MEDIA_TAG_THUMB] = $media ? $media->findVariant(Jamaah::MEDIA_TAG_THUMB)->getUrl() : env('APP_URL') . '/media/profile/anonymous-user.png';

        return $list;
    }
}
