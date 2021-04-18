<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ContactResource extends JsonResource
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
      'contactType' => $this->contactType,
      'label' => $this->label,
      'value' => $this->value,
      'jamaah' => new JamaahResource($this->whenLoaded('jamaah'))
    ];
  }
}
