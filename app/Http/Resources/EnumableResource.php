<?php

namespace App\Http\Resources;

use App\Models\Jamaah;
use Illuminate\Http\Resources\Json\JsonResource;

class EnumableResource extends JsonResource
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
            'enum' => new EnumResource($this->whenLoaded('enum')),
            'model' => $this->when($this->relationLoaded('model'), function () {
                if ($this->model instanceof Jamaah) {
                    return new JamaahResource($this->model);
                }
            })
        ];
    }
}
