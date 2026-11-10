<?php

namespace App\Http\Requests\Tracking;

use App\Models\Shipment;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreTrackingEventRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Controlled via TrackingEventPolicy in Controller
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'status' => ['required', 'string', Rule::in(Shipment::STATUSES)],
            'location_id' => ['nullable', 'integer', 'exists:locations,id'],
            'location_name' => ['nullable', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:1000'],
            'event_time' => ['nullable', 'date'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
        ];
    }
}

