<?php

namespace App\Services;

use App\Models\Location;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class LocationService
{
    /**
     * Get paginated locations with filtering.
     */
    public function getLocations(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = Location::query();

        if (! empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%")
                  ->orWhere('city', 'like', "%{$search}%")
                  ->orWhere('country', 'like', "%{$search}%");
            });
        }

        if (! empty($filters['type'])) {
            $query->where('type', $filters['type']);
        }

        return $query->orderBy('name')->paginate($perPage);
    }

    public function getAll(): Collection
    {
        return Location::orderBy('name')->get();
    }

    public function createLocation(array $data): Location
    {
        $data['code'] = strtoupper(trim($data['code']));

        return Location::create($data);
    }

    public function updateLocation(Location $location, array $data): Location
    {
        if (isset($data['code'])) {
            $data['code'] = strtoupper(trim($data['code']));
        }

        $location->update($data);

        return $location->fresh();
    }

    public function deleteLocation(Location $location): bool
    {
        return (bool) $location->delete();
    }
}

