<?php

namespace App\Http\Resources;

use App\Utils\DateUtils;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
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
      'mobile' => $this->mobile,
      'email' => $this->email,
      'createdAt' => DateUtils::toIso8601String($this->created_at),
      'updatedAt' => DateUtils::toIso8601String($this->updated_at),
      'jamaah' => new JamaahResource($this->whenLoaded('jamaah'))
    ];
  }
}
