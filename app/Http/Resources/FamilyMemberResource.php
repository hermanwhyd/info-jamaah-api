<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class FamilyMemberResource extends JsonResource
{

  public function __construct($resource)
  {
    parent::__construct($resource);
    self::withoutWrapping();
  }

  public function toArray($request)
  {
    return [
      'familyId' => (int) $this->familyId,
      'jamaahId' => $this->jamaahId,
      'relationshipEnum' => $this->relationshipEnum,
      'status' => $this->status,
      'position' => $this->position,
      'jamaah' => new JamaahResource($this->whenLoaded('jamaah')),
      'relationship' => new EnumTypeResource($this->whenLoaded('relationship')),
      'contacts' => ContactResource::collection($this->whenLoaded('contact')),
      'family' => FamilyResource::collection($this->whenLoaded('family'))
    ];
  }
}
