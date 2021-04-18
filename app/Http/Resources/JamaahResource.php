<?php

namespace App\Http\Resources;

use App\Models\Jamaah;
use App\Utils\DateUtils;
use App\Utils\MediaUtils;
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
            'gender' => $this->gender == 'M' ? 'Laki-laki' : 'Perempuan',
            'pembinaEnum' => $this->pembinaEnum,
            'lvPembinaanEnum' => $this->lvPembinaanEnum,
            'family' => new FamilyResource($this->when($this->relationLoaded('family'), function () {
                return $this->family->first();
            })),
            'photos' => '', //MediaUtils::getPhotoUrlOrDefault($this->firstMedia([Jamaah::MEDIA_TAG_CLOSEUP])),
            'families' => FamilyResource::collection($this->whenLoaded('families')),
            'contacts' => ContactResource::collection($this->whenLoaded('contacts')),
            'details' => JamaahDetailResource::collection($this->whenLoaded('details')),
            'pembina' => new PembinaResource($this->whenLoaded('pembina')),
            'lvPembinaan' => new EnumTypeResource($this->whenLoaded('lvPembinaan')),
            'pembinaan' => new JamaahPembinaanResource($this->whenLoaded('pembinaan')),
            'pembinaanHistories' => JamaahPembinaanResource::collection($this->whenLoaded('pembinaanHistories')),
        ];
    }
}
