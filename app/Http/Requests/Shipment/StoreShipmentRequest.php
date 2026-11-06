<?php

namespace App\Http\Requests\Shipment;

use App\Models\Shipment;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreShipmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create', Shipment::class) ?? false;
    }

    public function rules(): array
    {
        return [
            'tracking_number' => ['nullable', 'string', 'max:50', Rule::unique('shipments', 'tracking_number')],
            'customer_id' => ['required', 'integer', Rule::exists('users', 'id')],
            'carrier_id' => ['required', 'integer', Rule::exists('carriers', 'id')],
            'origin_id' => ['required', 'integer', Rule::exists('locations', 'id')],
            'destination_id' => ['required', 'integer', Rule::exists('locations', 'id'), 'different:origin_id'],
            'estimated_delivery_at' => ['required', 'date'],
            'departure_at' => ['nullable', 'date'],
            'notes' => ['nullable', 'string'],
            // Items array validation
            'items' => ['nullable', 'array', 'min:1'],
            'items.*.description' => ['required_with:items', 'string', 'max:255'],
            'items.*.quantity' => ['required_with:items', 'integer', 'min:1'],
            'items.*.weight_kg' => ['required_with:items', 'numeric', 'min:0'],
            'items.*.dimensions' => ['nullable', 'string', 'max:50'],
            'items.*.hs_code' => ['nullable', 'string', 'max:30'],
            'items.*.value_amount' => ['nullable', 'numeric', 'min:0'],
        ];
    }
}
