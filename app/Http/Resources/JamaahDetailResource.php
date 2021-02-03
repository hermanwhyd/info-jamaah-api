<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class JamaahDetailResource extends JsonResource
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
      'typeEnum' => $this->typeEnum,
      'label' => $this->type->label,
      'value' => $this->value
    ];
  }
}
