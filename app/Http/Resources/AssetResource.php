<?php

namespace App\Http\Resources;

use App\Models\Asset;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class AssetResource extends JsonResource
{

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
            'pembinaEnum' => $this->pembinaEnum,
            'category' => new EnumTypeResource($this->whenLoaded('category')),
            'status' => new EnumTypeResource($this->whenLoaded('status')),
            'pembina' => new PembinaResource($this->whenLoaded('pembina')),
            'location' => new LocationResource($this->whenLoaded('location')),
            'avatar' => $this->avatar?->getTemporaryUrl(Carbon::now()->addMinutes(env('AWS_TEMPORARY_URL_MINUTES', '60'),), Asset::MEDIA_TAG_THUMB),
            'photos' => MediaResource::collection($this->whenLoaded('photos')),
            'additionalFields' => AdditionalFieldResource::collection($this->whenLoaded('additionalFields')),
            'maintenances' => AssetMaintenanceResource::collection($this->whenLoaded('maintenances')),
            'notifiers' => NotifierResource::collection($this->whenLoaded('notifiers')),
            'audits' => AssetAuditResource::collection($this->whenLoaded('audits')),
            'media' => MediaResource::collection($this->whenLoaded('media', function () {
                return $this->media->all();
            }))
        ];
    }
}
