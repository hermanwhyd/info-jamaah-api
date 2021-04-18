<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class LocationResource extends JsonResource
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
            'label' => $this->label,
            'typeEnum' => $this->typeEnum,
            'type' => new EnumTypeResource($this->whenLoaded('type')),
        ];
    }
}
