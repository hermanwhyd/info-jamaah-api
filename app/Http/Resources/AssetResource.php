<?php

namespace App\Http\Resources;

use App\Models\Asset;
use Illuminate\Http\Resources\Json\JsonResource;

class AssetResource extends JsonResource
{
    private String $mode;

    public function __construct($resource, $mode = 'view')
    {
        parent::__construct($resource);
        self::withoutWrapping();
        $this->mode = $mode;
    }

    public function toArray($request)
    {
        return [
            'id' => (int) $this->id,
            'title' => $this->title,
            'tagNo' => $this->tagNo,
            'categoryEnum' => $this->categoryEnum,
            'statusEnum' => $this->statusEnum,
            'locationId' => $this->locationId,
            'ownerEnum' => $this->ownerEnum,
            'category' => new EnumTypeResource($this->whenLoaded('category')),
            'status' => new EnumTypeResource($this->whenLoaded('status')),
            'owner' => new PembinaResource($this->whenLoaded('owner')),
            'location' => new LocationResource($this->whenLoaded('location')),
            'avatar' => $this->avatar?->getUrl(Asset::MEDIA_TAG_THUMB),
            'photos' => MediaResource::collection($this->whenLoaded('photos')),
            'additionalFields' => AdditionalFieldResource::collection($this->whenLoaded('additionalFields')),
            'maintenances' => AssetMaintenanceResource::collection($this->whenLoaded('maintenances')),
            'audits' => AssetAuditResource::collection($this->whenLoaded('audits')),
            'media' => MediaResource::collection($this->whenLoaded('media', function () {
                return $this->media->all();
            }))
        ];
    }
}
