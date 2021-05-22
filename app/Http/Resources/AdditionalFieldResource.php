<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AdditionalFieldResource extends JsonResource
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
            'value' => $this->value,
            'customField' => new CustomFieldResource($this->whenLoaded('customField')),
        ];
    }
}
