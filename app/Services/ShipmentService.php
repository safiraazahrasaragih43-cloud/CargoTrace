<?php

namespace App\Services;

use App\Models\AppNotification;
use App\Models\Shipment;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class ShipmentService
{
    public function __construct(
        protected NotificationService $notificationService,
        protected AuditLogService $auditLogService
    ) {}

    /**
     * Get paginated shipments scoped to user authorization with advanced server-side filters & sorting.
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

        if (! empty($filters['origin_id'])) {
            $query->where('origin_id', $filters['origin_id']);
        }

        if (! empty($filters['destination_id'])) {
            $query->where('destination_id', $filters['destination_id']);
        }

        if (isset($filters['is_delayed']) && $filters['is_delayed'] !== '') {
            $query->where('is_delayed', filter_var($filters['is_delayed'], FILTER_VALIDATE_BOOLEAN));
        }

        if (! empty($filters['date_from'])) {
            $query->whereDate('estimated_delivery_at', '>=', $filters['date_from']);
        }

        if (! empty($filters['date_to'])) {
            $query->whereDate('estimated_delivery_at', '<=', $filters['date_to']);
        }

        if (! empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('tracking_number', 'like', "%{$search}%")
                  ->orWhereHas('customer', fn ($cq) => $cq->where('name', 'like', "%{$search}%")->orWhere('email', 'like', "%{$search}%"))
                  ->orWhereHas('carrier', fn ($cq) => $cq->where('name', 'like', "%{$search}%")->orWhere('code', 'like', "%{$search}%"));
            });
        }

        $sortBy = $filters['sort_by'] ?? 'latest';
        match ($sortBy) {
            'oldest' => $query->orderBy('created_at', 'asc'),
            'eta_asc' => $query->orderBy('estimated_delivery_at', 'asc'),
            'eta_desc' => $query->orderBy('estimated_delivery_at', 'desc'),
            'delay_desc' => $query->orderBy('delay_hours', 'desc')->latest(),
            default => $query->latest(),
        };

        return $query->paginate($perPage);
    }

    /**
     * Find a shipment by ID or fail, with authorization verification.
     */
    public function findShipment(User $user, int $id): Shipment
    {
        $query = Shipment::with(['customer', 'carrier', 'origin', 'destination', 'currentLocation', 'items', 'exceptions']);

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
     * Create a new shipment with atomic transaction, items, notification, and audit log.
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

            if (empty($data['expected_delivery_at']) && ! empty($data['estimated_delivery_at'])) {
                $data['expected_delivery_at'] = $data['estimated_delivery_at'];
            }

            $shipment = Shipment::create($data);

            if (! empty($items)) {
                foreach ($items as $item) {
                    $shipment->items()->create($item);
                }
            }

            $this->auditLogService->record(
                'SHIPMENT_CREATED',
                'Shipment',
                $shipment->id,
                "Pengiriman {$shipment->tracking_number} dibuat dengan status {$shipment->status}.",
                null,
                null,
                ['tracking_number' => $shipment->tracking_number, 'status' => $shipment->status]
            );

            $this->notificationService->notifyShipmentStakeholders(
                $shipment,
                AppNotification::TYPE_TRACKING_UPDATE,
                "Pengiriman Baru Dibuat ({$shipment->tracking_number})",
                "Pengiriman dengan nomor resi {$shipment->tracking_number} telah terdaftar di sistem dengan status {$shipment->status}."
            );

            return $shipment->load(['customer', 'carrier', 'origin', 'destination', 'currentLocation', 'items']);
        });
    }

    /**
     * Update an existing shipment and record audit trail.
     */
    public function updateShipment(Shipment $shipment, array $data): Shipment
    {
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

        $oldValues = $shipment->only(array_keys($data));
        $shipment->update($data);

        $this->auditLogService->record(
            'SHIPMENT_UPDATED',
            'Shipment',
            $shipment->id,
            "Data pengiriman {$shipment->tracking_number} diperbarui.",
            null,
            $oldValues,
            $shipment->only(array_keys($data))
        );

        return $shipment->fresh(['customer', 'carrier', 'origin', 'destination', 'currentLocation', 'items']);
    }

    /**
     * Soft delete a shipment and record audit trail.
     */
    public function deleteShipment(Shipment $shipment): bool
    {
        $trackingNo = $shipment->tracking_number;
        $id = $shipment->id;
        $deleted = (bool) $shipment->delete();

        if ($deleted) {
            $this->auditLogService->record(
                'SHIPMENT_DELETED',
                'Shipment',
                $id,
                "Pengiriman {$trackingNo} dihapus (soft delete)."
            );
        }

        return $deleted;
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
