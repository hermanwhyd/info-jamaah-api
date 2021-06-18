<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class EnumTypeResource extends JsonResource
{

    public function __construct($resource)
    {
        parent::__construct($resource);
        self::withoutWrapping();
    }

    public function toArray($request)
    {
        return array_filter([
            'code' => $this->code,
            'label' => $this->label,
            'enumablesCount' => $this->enumablesCount
        ]);
    }
}
