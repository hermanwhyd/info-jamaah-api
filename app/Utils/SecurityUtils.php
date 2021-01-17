<?php
namespace App\Utils;

class SecurityUtils {

    /**
     * SiapDiklat encryption method
     */
    public static function encrypt($text = '', $salt = '') {
      if (empty($text)) $text = uniqid();

      $encryptText = sha1($text.$salt);
      $encryptText = strtoupper($encryptText);

      return $encryptText;
    }

}
