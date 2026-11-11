<?php

namespace App\Http\Requests\Exception;

use App\Models\Shipment;
use App\Models\ShipmentException;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateExceptionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Controlled via ShipmentExceptionPolicy in Controller
    }

    public function rules(): array
    {
        return [
            'status' => ['nullable', 'string', Rule::in(ShipmentException::STATUSES)],
            'severity' => ['nullable', 'string', Rule::in(ShipmentException::SEVERITIES)],
            'resolution_notes' => ['nullable', 'string', 'max:1000'],
            'resolved_at' => ['nullable', 'date'],
            'restore_status' => ['nullable', 'string', Rule::in([
                Shipment::STATUS_IN_TRANSIT,
                Shipment::STATUS_OUT_FOR_DELIVERY,
            ])],
        ];
    }
}
