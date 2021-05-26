<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Arr;

class CustomFieldResource extends JsonResource
{

    protected $mode;

    public function __construct($resource, $mode = null)
    {
        parent::__construct($resource);
        self::withoutWrapping();
        $this->mode = $mode;
    }

    public function toArray($request)
    {
        $data = [
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

        if ($this->mode === 'view') {
            return Arr::except($data, ['position', 'groupEnumId', 'fieldReference']);
        }

        return $data;
    }

    public static function collection($resource, $mode = null)
    {
        return new AnonymousResourceCollection($resource, CustomFieldResource::class, $mode);
    }
}
