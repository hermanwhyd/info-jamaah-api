<?php

namespace App\Http\Resources;

use App\Utils\DateUtils;
use Illuminate\Http\Resources\Json\JsonResource;

class AssetAuditResource extends JsonResource
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
            'auditedAt' => DateUtils::toIso8601String($this->auditedAt),
            'assetId' => (int) $this->assetId,
            'locationId' => $this->locationId,
            'assetStatusEnum' => $this->assetStatusEnum,
            'notes' => $this->notes,
            'assetStatus' => new EnumTypeResource($this->whenLoaded('assetStatus')),
            'asset' => new AssetResource($this->whenLoaded('asset')),
            'location' => new LocationResource($this->whenLoaded('location')),
        ];
    }
}
