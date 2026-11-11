<?php

namespace App\Http\Requests\Exception;

use App\Models\ShipmentException;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreExceptionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Controlled via ShipmentExceptionPolicy in Controller
    }

    public function rules(): array
    {
        return [
            'type' => ['required', 'string', Rule::in(ShipmentException::TYPES)],
            'severity' => ['required', 'string', Rule::in(ShipmentException::SEVERITIES)],
            'description' => ['required', 'string', 'max:1000'],
            'detected_at' => ['nullable', 'date'],
            'tracking_event_id' => ['nullable', 'integer', 'exists:tracking_events,id'],
            'new_estimated_delivery_at' => ['nullable', 'date'],
        ];
    }
}

