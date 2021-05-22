<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Arr;

class EnumResource extends JsonResource
{
    protected $mode;

    public function __construct($resource)
    {
        parent::__construct($resource);
        self::withoutWrapping();
    }

    public function mode($mode)
    {
        $this->mode = $mode;
        return $this;
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
            'customFields' => CustomFieldResource::collection($this->whenLoaded('customFields'))
        ];
        return $result;
    }
}
