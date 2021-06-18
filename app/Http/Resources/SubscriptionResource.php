<?php

namespace App\Http\Resources;

use App\Models\Enum;
use App\Models\Notifier;
use Illuminate\Http\Resources\Json\JsonResource;

class SubscriptionResource extends JsonResource
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
            'subscribe' => $this->when($this->relationLoaded('subscribe'), function () {
                if ($this->subscribe instanceof Notifier) {
                    return new NotifierResource($this->subscribe);
                }
            }),
            'subscriber' => $this->when($this->relationLoaded('subscriber'), function () {
                if ($this->subscriber instanceof Enum) {
                    return new EnumResource($this->subscriber);
                }
            })
        ];
    }
}
