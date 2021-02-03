<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AddressResource extends JsonResource
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
      'streetName' => $this->streetName,
      'houseNo' => $this->houseNo,
      'rt' => $this->rt,
      'rw' => $this->rw,
      'kelurahan' => $this->kelurahan,
      'kecamatan' => $this->kecamatan,
      'city' => $this->city,
      'postCode' => $this->postCode,
      'geo' => $this->geo,
      'residance' => ResidanceResource::collection($this->whenLoaded('residance'))
    ];
  }
}
