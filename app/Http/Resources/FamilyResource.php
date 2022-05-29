<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class FamilyResource extends JsonResource
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
            'kepalaKeluargaId' => $this->kepalaKeluargaId,
            'label' => $this->label,
            'pembinaEnum' => $this->pembinaEnum,
            'residanceId' => $this->residanceId,
            'kepalaKeluarga' => new JamaahResource($this->whenLoaded('kepalaKeluarga')),
            'pembina' => new PembinaResource($this->whenLoaded('pembina')),
            'residance' => new ResidanceResource($this->whenLoaded('residance')),
            'members' => FamilyMemberResource::collection($this->whenLoaded('members')),
            'membersCount' => $this->when(isset($this->membersCount), $this->membersCount),
        ];
    }
}
