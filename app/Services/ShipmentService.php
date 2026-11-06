<?php

namespace App\Services;

use App\Models\Shipment;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class ShipmentService
{
    /**
     * Get paginated shipments scoped to user authorization and filters.
     */
    public function getShipments(User $user, array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = Shipment::with(['customer', 'carrier', 'origin', 'destination', 'currentLocation', 'items']);

        // Scope to customer if not admin or operator
        if ($user->isCustomer()) {
            $query->where('customer_id', $user->id);
        } elseif (! empty($filters['customer_id'])) {
            $query->where('customer_id', $filters['customer_id']);
        }

        if (! empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (! empty($filters['carrier_id'])) {
            $query->where('carrier_id', $filters['carrier_id']);
        }

        if (! empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('tracking_number', 'like', "%{$search}%")
                  ->orWhereHas('customer', fn ($cq) => $cq->where('name', 'like', "%{$search}%")->orWhere('email', 'like', "%{$search}%"))
                  ->orWhereHas('carrier', fn ($cq) => $cq->where('name', 'like', "%{$search}%"));
            });
        }

        return $query->latest()->paginate($perPage);
    }

    /**
     * Find a shipment by ID or fail, with authorization verification.
     */
    public function findShipment(User $user, int $id): Shipment
    {
        $query = Shipment::with(['customer', 'carrier', 'origin', 'destination', 'currentLocation', 'items']);

        if ($user->isCustomer()) {
            $query->where('customer_id', $user->id);
        }

        return $query->findOrFail($id);
    }

    /**
     * Find a shipment by public tracking number.
     */
    public function findByTrackingNumber(string $trackingNumber): Shipment
    {
        return Shipment::with(['customer', 'carrier', 'origin', 'destination', 'currentLocation', 'items'])
            ->where('tracking_number', strtoupper(trim($trackingNumber)))
            ->firstOrFail();
    }

    /**
     * Create a new shipment with atomic transaction and items support.
     */
    public function createShipment(array $data, ?array $items = null): Shipment
    {
        return DB::transaction(function () use ($data, $items) {
            if (empty($data['tracking_number'])) {
                $data['tracking_number'] = $this->generateTrackingNumber();
            } else {
                $data['tracking_number'] = strtoupper(trim($data['tracking_number']));
            }

            if (empty($data['status'])) {
                $data['status'] = Shipment::STATUS_CREATED;
            }

            $shipment = Shipment::create($data);

            if (! empty($items)) {
                foreach ($items as $item) {
                    $shipment->items()->create($item);
                }
            }

            return $shipment->load(['customer', 'carrier', 'origin', 'destination', 'currentLocation', 'items']);
        });
    }

    /**
     * Update an existing shipment.
     */
    public function updateShipment(Shipment $shipment, array $data): Shipment
    {
        // Prevent terminal state mutation unless special status change logic is used
        if ($shipment->isTerminal() && isset($data['status']) && $data['status'] !== $shipment->status) {
            throw ValidationException::withMessages([
                'status' => ["Cannot change status of a terminal shipment ({$shipment->status})."],
            ]);
        }

        if (isset($data['status']) && $data['status'] !== $shipment->status) {
            if (! $shipment->canTransitionTo($data['status'])) {
                throw ValidationException::withMessages([
                    'status' => ["Invalid transition from {$shipment->status} to {$data['status']}."],
                ]);
            }
        }

        $shipment->update($data);

        return $shipment->fresh(['customer', 'carrier', 'origin', 'destination', 'currentLocation', 'items']);
    }

    /**
     * Soft delete a shipment.
     */
    public function deleteShipment(Shipment $shipment): bool
    {
        return (bool) $shipment->delete();
    }

    /**
     * Deterministic tracking number generator: CT-YYYYMMDD-XXXX
     */
    public function generateTrackingNumber(): string
    {
        do {
            $number = 'CT-' . date('Ymd') . '-' . strtoupper(Str::random(4));
        } while (Shipment::where('tracking_number', $number)->exists());

        return $number;
    }
}
