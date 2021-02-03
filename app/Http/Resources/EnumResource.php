<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class EnumResource extends JsonResource
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
      'code' => $this->code,
      'label' => $this->label,
      'position' => $this->position,
      'removable' => $this->removable,
      'variables' => VariableResource::collection($this->whenLoaded('variable'))
    ];
  }
}
