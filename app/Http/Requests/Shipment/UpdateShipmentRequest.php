<?php

namespace App\Http\Requests\Shipment;

use App\Models\Shipment;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateShipmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        $shipment = $this->route('shipment');

        return $this->user()?->can('update', $shipment) ?? false;
    }

    public function rules(): array
    {
        return [
            'carrier_id' => ['sometimes', 'required', 'integer', Rule::exists('carriers', 'id')],
            'destination_id' => ['sometimes', 'required', 'integer', Rule::exists('locations', 'id')],
            'status' => ['sometimes', 'required', 'string', Rule::in(Shipment::STATUSES)],
            'estimated_delivery_at' => ['sometimes', 'required', 'date'],
            'departure_at' => ['nullable', 'date'],
            'actual_delivery_at' => ['nullable', 'date'],
            'notes' => ['nullable', 'string'],
        ];
    }
}
