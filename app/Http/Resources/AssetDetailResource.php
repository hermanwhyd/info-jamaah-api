<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AssetDetailResource extends JsonResource
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
            'typeEnum' => $this->typeEnum,
            'value' => $this->value,
            'asset' => new AssetResource($this->whenLoaded('asset')),
            'type' => new EnumTypeResource($this->whenLoaded('type')),
        ];
    }
}
