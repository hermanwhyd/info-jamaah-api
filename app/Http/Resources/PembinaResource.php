<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Str;

class PembinaResource extends JsonResource
{

  public function __construct($resource)
  {
    parent::__construct($resource);
    self::withoutWrapping();
  }

  public function toArray($request)
  {
    return [
      'lvPembina' => Str::ucfirst(Str::lower(Str::after($this->group, '_'))),
      'initial' => $this->code,
      'label' => $this->label
    ];
  }
}
