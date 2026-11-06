<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TrackingEventResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'shipment_id' => $this->shipment_id,
            'status' => $this->status,
            'location_name' => $this->location_name,
            'description' => $this->description,
            'event_time' => $this->event_time?->toISOString(),
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'location' => new LocationResource($this->whenLoaded('location')),
            'creator' => new UserResource($this->whenLoaded('creator')),
            'created_at' => $this->created_at?->toISOString(),
        ];
    }
}
