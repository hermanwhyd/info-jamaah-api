<?php

namespace App\Http\Resources;

use App\Models\AssetMaintenance;
use Illuminate\Http\Resources\Json\JsonResource;

class SupplierResource extends JsonResource
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
            'title' => $this->title,
            'maintenances' => AssetMaintenanceResource::collection($this->whenLoaded('maintenances')),
            'address' => new AddressResource($this->whenLoaded('address')),
            'contacts' => ContactResource::collection($this->whenLoaded('contacts')),
        ];
    }
}
