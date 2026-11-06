<?php

namespace App\Services;

use App\Models\Carrier;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class CarrierService
{
    /**
     * Get paginated carriers or all active carriers.
     */
    public function getCarriers(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = Carrier::query();

        if (! empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%");
            });
        }

        if (isset($filters['is_active'])) {
            $query->where('is_active', (bool) $filters['is_active']);
        }

        return $query->latest()->paginate($perPage);
    }

    public function getAllActive(): Collection
    {
        return Carrier::where('is_active', true)->orderBy('name')->get();
    }

    public function createCarrier(array $data): Carrier
    {
        $data['code'] = strtoupper(trim($data['code']));

        return Carrier::create($data);
    }

    public function updateCarrier(Carrier $carrier, array $data): Carrier
    {
        if (isset($data['code'])) {
            $data['code'] = strtoupper(trim($data['code']));
        }

        $carrier->update($data);

        return $carrier->fresh();
    }

    public function deleteCarrier(Carrier $carrier): bool
    {
        return (bool) $carrier->delete();
    }
}

