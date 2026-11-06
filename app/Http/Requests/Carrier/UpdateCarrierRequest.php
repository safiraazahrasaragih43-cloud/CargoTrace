<?php

namespace App\Http\Requests\Carrier;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCarrierRequest extends FormRequest
{
    public function authorize(): bool
    {
        $carrier = $this->route('carrier');

        return $this->user()?->can('update', $carrier) ?? false;
    }

    public function rules(): array
    {
        $carrier = $this->route('carrier');
        $carrierId = is_numeric($carrier) ? $carrier : $carrier?->id;

        return [
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'code' => ['sometimes', 'required', 'string', 'max:20', Rule::unique('carriers', 'code')->ignore($carrierId)],
            'contact_email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
            'tracking_url_template' => ['nullable', 'string', 'max:255'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }
}

