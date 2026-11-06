<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ShipmentItemResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'description' => $this->description,
            'quantity' => $this->quantity,
            'weight_kg' => $this->weight_kg,
            'dimensions' => $this->dimensions,
            'hs_code' => $this->hs_code,
            'value_amount' => $this->value_amount,
        ];
    }
}
