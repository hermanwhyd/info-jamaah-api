<?php

namespace App\Http\Resources;

use App\Utils\DateUtils;
use Illuminate\Http\Resources\Json\JsonResource;

class JamaahPembinaanResource extends JsonResource
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
      'pembinaEnum' => $this->pembinaEnum,
      'lvPembinaanEnum' => $this->lvPembinaanEnum,
      'startDate' => DateUtils::toIso8601String($this->startDate),
      'endDate' => DateUtils::toIso8601String($this->endDate),
    ];
  }
}
