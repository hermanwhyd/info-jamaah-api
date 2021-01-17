<?php
namespace App\Utils;

class Common
{

    public static function public_path($s = '')
    {
        return base_path('public/' . $s);
    }

    public static function storage_path($s = '')
    {
        return base_path('public/storage/' . $s);
    }

    public static function photoUrl($photoName)
    {
        return env('SIAPDIKLAT_URL') . '/' . $photoName;
    }

}
