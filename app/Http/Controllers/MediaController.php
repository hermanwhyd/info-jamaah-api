<?php

namespace App\Http\Controllers;

use App\Models\Media;

class MediaController extends Controller
{

    public function downloadSingle($uuid)
    {
        return Media::findByUuid($uuid);
    }
}
