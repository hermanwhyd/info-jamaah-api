<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Arr;

class EnumResource extends JsonResource
{
    private $mode;

    public function __construct($resource, $mode = null)
    {
        parent::__construct($resource);
        self::withoutWrapping();
        $this->mode = $mode;
    }

    public function toArray($request)
    {
        $result = [
            'id' => $this->id,
            'code' => $this->code,
            'label' => $this->label,
            'position' => $this->position,
            'removable' => $this->removable,
            'variables' => VariableResource::collection($this->whenLoaded('variable')),
            'customFields' => CustomFieldResource::collection($this->whenLoaded('customFields'), $this->mode)
        ];

        if ($this->mode === 'view') {
            return Arr::except($result, ['position', 'removable']);
        }

        return $result;
    }

    public static function collection($resource, $mode = null)
    {
        return new AnonymousResourceCollection($resource, EnumResource::class, $mode);
    }
}
