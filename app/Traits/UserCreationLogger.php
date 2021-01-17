<?php

namespace App\Traits;

use Illuminate\Support\Facades\Auth;

trait UserCreationLogger {
    public static function bootUserCreationLogger() {
        if (Auth::check()) {
            static::creating(function ($model) {
                $model->created_by = Auth::id();
            });
        }
    }
}
