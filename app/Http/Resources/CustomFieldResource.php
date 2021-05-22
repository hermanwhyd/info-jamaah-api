<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CustomFieldResource extends JsonResource
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
            'groupEnumId' => $this->groupEnumId,
            'position' => $this->position,
            'fieldName' => $this->fieldName,
            'fieldType' => $this->fieldType,
            'fieldReference' => $this->fieldReference,
            'group' => new EnumTypeResource($this->whenLoaded('group')),
            'additionalFields' => AdditionalFieldResource::collection($this->whenLoaded('additionalFields')),
            'value' => new AdditionalFieldResource($this->whenLoaded('value'))
        ];
    }
}
