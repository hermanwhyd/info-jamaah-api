<?php

namespace App\Models;

use \Eloquence\Behaviours\CamelCasing;
use Spatie\MediaLibrary\MediaCollections\Models\Media as BaseMedia;

class Media extends BaseMedia
{
    use CamelCasing;

    public function collection()
    {
        return $this->belongsTo(Enum::class, 'collection_name', 'code')->where('group', 'ASSET_MEDIA_COLL');
    }
}
