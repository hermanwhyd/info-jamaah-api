<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AssetResource extends JsonResource
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
            'details' => AssetDetailResource::collection($this->whenLoaded('details')),
            'maintenances' => AssetMaintenanceResource::collection($this->whenLoaded('maintenances')),
            'audits' => AssetAuditResource::collection($this->whenLoaded('audits')),
        ];
    }
}