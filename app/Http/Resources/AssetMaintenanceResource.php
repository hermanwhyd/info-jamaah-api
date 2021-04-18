<?php

namespace App\Http\Resources;

use App\Utils\DateUtils;
use Illuminate\Http\Resources\Json\JsonResource;

class AssetMaintenanceResource extends JsonResource
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
            'assetId' => (int) $this->assetId,
            'title' => $this->title,
            'typeEnum' => $this->typeEnum,
            'notes' => $this->notes,
            'supplierId' => $this->supplierId,
            'startDate' => DateUtils::toIso8601String($this->startDate),
            'endDate' => DateUtils::toIso8601String($this->endDate),
            'type' => new EnumTypeResource($this->whenLoaded('type')),
            'supplier' => new SupplierResource($this->whenLoaded('supplier')),
            'asset' => new AssetResource($this->whenLoaded('asset')),
            'creator' => new UserResource($this->whenLoaded('creator')),
        ];
    }
}
