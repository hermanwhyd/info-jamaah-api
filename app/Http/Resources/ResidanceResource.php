<?php

namespace App\Http\Resources;

use App\Utils\DateUtils;
use Illuminate\Http\Resources\Json\JsonResource;

class ResidanceResource extends JsonResource
{

  public function __construct($resource)
  {
    parent::__construct($resource);
    self::withoutWrapping();
  }

  public function toArray($request)
  {
    return [
      'typeEnum' => $this->typeEnum,
      'label' => $this->label,
      'createdAt' => DateUtils::toIso8601String($this->createdAt),
      'updatedAt' => DateUtils::toIso8601String($this->updatedAt),
      'type' => new EnumTypeResource($this->whenLoaded('type')),
      'address' => new AddressResource($this->whenLoaded('address')),
    ];
  }
}
