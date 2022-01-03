<?php

namespace App\Http\Resources;

use App\Utils\DateUtils;
use Illuminate\Http\Resources\Json\JsonResource;

class KepengurusanResource extends JsonResource
{
    public function __construct($resource)
    {
        parent::__construct($resource);
        self::withoutWrapping();
    }

    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'description' => $this->description,
            'pengurus' => new EnumTypeResource($this->whenLoaded('pengurus')),
            'pembina' => new EnumTypeResource($this->whenLoaded('pembina')),
            'jamaah' => new JamaahResource($this->whenLoaded('jamaah')),
            'createdAt' => DateUtils::toIso8601String($this->created_at),
            'deletedAt' => DateUtils::toIso8601String($this->deleted_at)
        ];
    }
}
