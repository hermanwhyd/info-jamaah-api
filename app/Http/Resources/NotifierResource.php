<?php

namespace App\Http\Resources;

use App\Models\AdditionalField;
use App\Utils\DateUtils;
use Illuminate\Http\Resources\Json\JsonResource;

class NotifierResource extends JsonResource
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
            'name' => $this->name,
            'description' => $this->description,
            'dueDateAt' => DateUtils::toIso8601String($this->dueDateAt),
            'isRepetition' => $this->isRepetition === 1,
            'reminderDays' => $this->reminderDays,
            'lastFiredAt' => DateUtils::toIso8601String($this->lastFiredAt),
            'lastFiredStatus' => $this->lastFiredStatus,
            'lastFiredError' => $this->lastFiredError,
            'referable' => $this->when($this->relationLoaded('referable'), function () {
                if ($this->referable instanceof AdditionalField) {
                    return new AdditionalFieldResource($this->whenLoaded('referable'));
                }
            }),
            'subscriptions' => SubscriptionResource::collection($this->whenLoaded('subscriptions')),
        ];
    }
}
