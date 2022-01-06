<?php

namespace App\Http\Resources;

use App\Models\Jamaah;
use App\Utils\DateUtils;
use Carbon\Carbon;
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
            'avatar' => $this->avatar?->getTemporaryUrl(Carbon::now()->addMinutes(env('AWS_TEMPORARY_URL_MINUTES', '60'),), Jamaah::MEDIA_TAG_THUMB),
            'photos' => MediaResource::collection($this->whenLoaded('photos')),
            'families' => FamilyResource::collection($this->whenLoaded('families')),
            'contacts' => ContactResource::collection($this->whenLoaded('contacts')),
            'pembina' => new PembinaResource($this->whenLoaded('pembina')),
            'lvPembinaan' => new EnumTypeResource($this->whenLoaded('lvPembinaan')),
            'pembinaan' => new JamaahPembinaanResource($this->whenLoaded('pembinaan')),
            'pembinaanHistories' => JamaahPembinaanResource::collection($this->whenLoaded('pembinaanHistories')),
            'dapukans' => KepengurusanResource::collection($this->whenLoaded('dapukans'))
        ];
    }
}
