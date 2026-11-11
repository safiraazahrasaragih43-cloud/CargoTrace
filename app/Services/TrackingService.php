<?php

namespace App\Services;

use App\Models\AppNotification;
use App\Models\Location;
use App\Models\Shipment;
use App\Models\TrackingEvent;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class TrackingService
{
    public function __construct(
        protected EtaService $etaService,
        protected NotificationService $notificationService,
        protected AuditLogService $auditLogService
    ) {}

    /**
     * Add an immutable tracking event to a shipment and update shipment lifecycle state.
     *
     * @param  Shipment  $shipment
     * @param  array  $data
     * @param  User|null  $actor
     * @return TrackingEvent
     *
     * @throws ValidationException
     */
    public function addEvent(Shipment $shipment, array $data, ?User $actor = null): TrackingEvent
    {
        // 1. Terminal state check
        if ($shipment->isTerminal()) {
            throw ValidationException::withMessages([
                'status' => ["Cannot add tracking event to a shipment in terminal status ({$shipment->status})."],
            ]);
        }

        $targetStatus = $data['status'] ?? $shipment->status;

        // 2. State transition check
        if (! $shipment->canTransitionTo($targetStatus)) {
            throw ValidationException::withMessages([
                'status' => ["Invalid status transition from {$shipment->status} to {$targetStatus}."],
            ]);
        }

        // 3. Coordinate and Location resolution
        $locationId = $data['location_id'] ?? null;
        $locationName = $data['location_name'] ?? null;
        $latitude = $data['latitude'] ?? null;
        $longitude = $data['longitude'] ?? null;

        if ($locationId) {
            $location = Location::find($locationId);
            if ($location) {
                if (empty($locationName)) {
                    $locationName = $location->name . ($location->code ? " ({$location->code})" : '');
                }
                if ($latitude === null && $location->latitude !== null) {
                    $latitude = $location->latitude;
                }
                if ($longitude === null && $location->longitude !== null) {
                    $longitude = $location->longitude;
                }
            }
        }

        if (empty($locationName)) {
            $locationName = 'Transit Waypoint';
        }

        $eventTime = ! empty($data['event_time']) ? $data['event_time'] : now();
        $previousStatus = $shipment->status;

        return DB::transaction(function () use ($shipment, $previousStatus, $targetStatus, $data, $locationId, $locationName, $latitude, $longitude, $eventTime, $actor) {
            // Create immutable tracking event
            $event = TrackingEvent::create([
                'shipment_id' => $shipment->id,
                'location_id' => $locationId,
                'location_name' => $locationName,
                'status' => $targetStatus,
                'description' => $data['description'],
                'event_time' => $eventTime,
                'latitude' => $latitude,
                'longitude' => $longitude,
                'created_by' => $actor?->id,
            ]);

            // Update shipment's current state
            $shipmentUpdates = [
                'status' => $targetStatus,
            ];

            if ($locationId) {
                $shipmentUpdates['current_location_id'] = $locationId;
            }

            // Auto-set actual_delivery_at if reaching DELIVERED
            if ($targetStatus === Shipment::STATUS_DELIVERED && empty($shipment->actual_delivery_at)) {
                $shipmentUpdates['actual_delivery_at'] = $eventTime;
            }

            // Auto-set departure_at if moving into PICKED_UP or IN_TRANSIT and not set yet
            if (in_array($targetStatus, [Shipment::STATUS_PICKED_UP, Shipment::STATUS_IN_TRANSIT], true) && empty($shipment->departure_at)) {
                $shipmentUpdates['departure_at'] = $eventTime;
            }

            $shipment->update($shipmentUpdates);

            // Evaluate deterministic ETA & delay metrics against the new checkpoint
            $etaResult = $this->etaService->evaluateShipment($shipment->fresh(), $event->event_time);

            // Record immutable audit trail
            $this->auditLogService->record(
                'TRACKING_EVENT_ADDED',
                'Shipment',
                $shipment->id,
                "Checkpoint [{$targetStatus}] dicatat untuk {$shipment->tracking_number} di {$locationName}.",
                $actor,
                ['status' => $previousStatus],
                ['status' => $targetStatus, 'location' => $locationName]
            );

            // Notify customer about movement
            $this->notificationService->notifyShipmentStakeholders(
                $shipment,
                AppNotification::TYPE_TRACKING_UPDATE,
                "Pembaruan Status ({$shipment->tracking_number}): {$targetStatus}",
                "Posisi terkini di {$locationName}: {$data['description']}"
            );

            // Trigger additional alert if delay threshold exceeded
            if (($etaResult['exceeds_threshold'] ?? false) === true) {
                $this->notificationService->notifyShipmentStakeholders(
                    $shipment,
                    AppNotification::TYPE_ETA_DELAY,
                    "Peringatan Keterlambatan ({$shipment->tracking_number})",
                    $etaResult['reason'] ?? "Keterlambatan terdeteksi (+{$etaResult['delay_hours']} jam).",
                    includeStaff: true
                );
            }

            return $event->load(['location', 'creator']);
        });
    }

    /**
     * Retrieve complete chronological timeline for a shipment.
     *
     * @param  Shipment  $shipment
     * @return Collection<int, TrackingEvent>
     */
    public function getTimeline(Shipment $shipment): Collection
    {
        return $shipment->trackingEvents()
            ->with(['location', 'creator'])
            ->orderBy('event_time', 'asc')
            ->get();
    }
}

