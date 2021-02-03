<?php

namespace App\Http\Resources;

use App\Utils\DateUtils;
use Illuminate\Http\Resources\Json\JsonResource;

class JamaahResource extends JsonResource
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
      'fullName' => $this->fullName,
      'nickname' => $this->nickname,
      'birthDate' => DateUtils::toIso8601String($this->birthDate),
      'gender' => $this->gender,
      'pembinaEnum' => $this->pembinaEnum,
      'family' => new FamilyResource($this->when($this->relationLoaded('family'), function () {
        return $this->family->first();
      })),
      'families' => FamilyResource::collection($this->whenLoaded('families')),
      'contacts' => ContactResource::collection($this->whenLoaded('contacts')),
      'details' => JamaahDetailResource::collection($this->whenLoaded('details')),
      'pembina' => new PembinaResource($this->whenLoaded('pembina')),
      'pembinaan' => new JamaahPembinaanResource($this->whenLoaded('pembinaan')),
      'pembinaanHistories' => JamaahPembinaanResource::collection($this->whenLoaded('pembinaanHistories')),
    ];
  }
}
