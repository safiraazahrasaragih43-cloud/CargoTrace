<?php

namespace App\Http\Requests\Location;

use App\Models\Location;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateLocationRequest extends FormRequest
{
    public function authorize(): bool
    {
        $location = $this->route('location');

        return $this->user()?->can('update', $location) ?? false;
    }

    public function rules(): array
    {
        $location = $this->route('location');
        $locationId = is_numeric($location) ? $location : $location?->id;

        return [
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'code' => ['sometimes', 'required', 'string', 'max:20', Rule::unique('locations', 'code')->ignore($locationId)],
            'type' => ['sometimes', 'required', 'string', Rule::in(Location::TYPES)],
            'address' => ['nullable', 'string'],
            'city' => ['sometimes', 'required', 'string', 'max:100'],
            'country' => ['sometimes', 'required', 'string', 'max:100'],
            'latitude' => ['sometimes', 'required', 'numeric', 'between:-90,90'],
            'longitude' => ['sometimes', 'required', 'numeric', 'between:-180,180'],
        ];
    }
}
