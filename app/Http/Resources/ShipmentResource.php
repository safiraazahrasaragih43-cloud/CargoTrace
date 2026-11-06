<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ShipmentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'tracking_number' => $this->tracking_number,
            'status' => $this->status,
            'departure_at' => $this->departure_at?->toISOString(),
            'estimated_delivery_at' => $this->estimated_delivery_at?->toISOString(),
            'actual_delivery_at' => $this->actual_delivery_at?->toISOString(),
            'delay_hours' => $this->delay_hours,
            'notes' => $this->notes,
            'customer' => new UserResource($this->whenLoaded('customer')),
            'carrier' => new CarrierResource($this->whenLoaded('carrier')),
            'origin' => new LocationResource($this->whenLoaded('origin')),
            'destination' => new LocationResource($this->whenLoaded('destination')),
            'current_location' => new LocationResource($this->whenLoaded('currentLocation')),
            'items' => ShipmentItemResource::collection($this->whenLoaded('items')),
            'tracking_events' => TrackingEventResource::collection($this->whenLoaded('trackingEvents')),
            'latest_tracking_event' => new TrackingEventResource($this->whenLoaded('latestTrackingEvent')),
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}

