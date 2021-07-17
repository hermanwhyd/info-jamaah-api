<?php

namespace App\Http\Resources;

use App\Models\Asset;
use App\Utils\DateUtils;
use App\Utils\MediaUtils;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class MediaResource extends JsonResource
{

    public function __construct($resource)
    {
        parent::__construct($resource);
        self::withoutWrapping();
    }

    public function toArray($request)
    {
        return [
            'id' => (int) $this->id,
            'uuid' => $this->uuid,
            'name' => $this->name,
            'collectionName' => $this->collectionName,
            'collection' => new EnumTypeResource($this->collection),
            'mimeType' => $this->mime_type,
            'size' => (int) $this->size,
            'disk' => $this->disk,
            'file' => [
                'thumb' => MediaUtils::isImage($this->mime_type) ? $this->getTemporaryUrl(Carbon::now()->addMinutes(env('AWS_TEMPORARY_URL_MINUTES', '60'),), Asset::MEDIA_TAG_THUMB) : null,
                'url' => MediaUtils::isImage($this->mime_type) ? $this->getTemporaryUrl(Carbon::now()->addMinutes(env('AWS_TEMPORARY_URL_MINUTES', '60'),)) : null,
                'download' => route('media.download', ['uuid' => $this->uuid])
            ],
            'properties' => $this->custom_properties,
            'createdAt' => DateUtils::toIso8601String($this->created_at),
            'updatedAt' => DateUtils::toIso8601String($this->updated_at),
        ];
    }
}
