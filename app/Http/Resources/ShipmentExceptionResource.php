<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ShipmentExceptionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'shipment_id' => $this->shipment_id,
            'tracking_event_id' => $this->tracking_event_id,
            'type' => $this->type,
            'severity' => $this->severity,
            'status' => $this->status,
            'description' => $this->description,
            'resolution_notes' => $this->resolution_notes,
            'detected_at' => $this->detected_at?->toISOString(),
            'resolved_at' => $this->resolved_at?->toISOString(),
            'resolution_duration_hours' => $this->resolutionDurationHours(),
            'reporter' => new UserResource($this->whenLoaded('reporter')),
            'resolver' => new UserResource($this->whenLoaded('resolver')),
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}

