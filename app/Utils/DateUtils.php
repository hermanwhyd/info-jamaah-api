<?php
namespace App\Utils;

class DateUtils {

    public static function toIso8601String($date) {
        return $date == null ? null : $date->toIso8601String();
    }

}
