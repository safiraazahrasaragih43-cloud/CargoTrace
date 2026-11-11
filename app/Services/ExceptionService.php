<?php

namespace App\Services;

use App\Models\AppNotification;
use App\Models\Shipment;
use App\Models\ShipmentException;
use App\Models\TrackingEvent;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ExceptionService
{
    public function __construct(
        protected EtaService $etaService,
        protected NotificationService $notificationService,
        protected AuditLogService $auditLogService
    ) {}

    /**
     * Get all exceptions for a shipment ordered by newest detection.
     */
    public function getShipmentExceptions(Shipment $shipment): Collection
    {
        return $shipment->exceptions()
            ->with(['reporter', 'resolver', 'trackingEvent'])
            ->get();
    }

    /**
     * Report a new operational exception on an active shipment.
     */
    public function reportException(Shipment $shipment, array $data, ?User $actor = null): ShipmentException
    {
        if ($shipment->isTerminal()) {
            throw ValidationException::withMessages([
                'shipment' => ["Cannot report exception on a terminal shipment ({$shipment->status})."],
            ]);
        }

        $detectedAt = ! empty($data['detected_at']) ? $data['detected_at'] : now();

        return DB::transaction(function () use ($shipment, $data, $detectedAt, $actor) {
            $trackingEventId = $data['tracking_event_id'] ?? null;

            // Transition shipment to EXCEPTION if allowed from its current active phase
            if ($shipment->canTransitionTo(Shipment::STATUS_EXCEPTION) && $shipment->status !== Shipment::STATUS_EXCEPTION) {
                $locationName = $shipment->currentLocation?->name
                    ?? $shipment->origin?->name
                    ?? 'Operational Checkpoint';

                $event = TrackingEvent::create([
                    'shipment_id' => $shipment->id,
                    'location_id' => $shipment->current_location_id,
                    'location_name' => $locationName,
                    'status' => Shipment::STATUS_EXCEPTION,
                    'description' => "Exception [{$data['type']}]: {$data['description']}",
                    'event_time' => $detectedAt,
                    'latitude' => $shipment->currentLocation?->latitude,
                    'longitude' => $shipment->currentLocation?->longitude,
                    'created_by' => $actor?->id,
                ]);

                $trackingEventId = $event->id;
                $shipment->update(['status' => Shipment::STATUS_EXCEPTION]);
            }

            $exception = ShipmentException::create([
                'shipment_id' => $shipment->id,
                'tracking_event_id' => $trackingEventId,
                'type' => $data['type'],
                'severity' => $data['severity'],
                'status' => ShipmentException::STATUS_OPEN,
                'description' => $data['description'],
                'detected_at' => $detectedAt,
                'reported_by' => $actor?->id,
            ]);

            // Optional ETA revision when reporting delay-inducing exception
            if (! empty($data['new_estimated_delivery_at'])) {
                $this->etaService->recalculateEta(
                    $shipment->fresh(),
                    $data['new_estimated_delivery_at'],
                    "Exception {$data['type']}"
                );
            } else {
                $this->etaService->evaluateShipment($shipment->fresh(), null, "Exception {$data['type']}");
            }

            $this->auditLogService->record(
                'EXCEPTION_REPORTED',
                'ShipmentException',
                $exception->id,
                "Kendala [{$data['type']} | {$data['severity']}] dilaporkan pada {$shipment->tracking_number}.",
                $actor,
                null,
                ['type' => $data['type'], 'severity' => $data['severity']]
            );

            $this->notificationService->notifyShipmentStakeholders(
                $shipment,
                AppNotification::TYPE_EXCEPTION_ALERT,
                "Kendala Pengiriman ({$shipment->tracking_number}): {$data['type']}",
                $data['description'],
                includeStaff: true
            );

            return $exception->load(['reporter', 'resolver', 'trackingEvent']);
        });
    }

    /**
     * Update status, severity, or resolve an exception.
     */
    public function updateException(ShipmentException $exception, array $data, ?User $actor = null): ShipmentException
    {
        if ($exception->isResolved() && isset($data['status']) && $data['status'] !== ShipmentException::STATUS_RESOLVED) {
            throw ValidationException::withMessages([
                'status' => ['Resolved exceptions cannot be reopened directly.'],
            ]);
        }

        $previousStatus = $exception->status;

        return DB::transaction(function () use ($exception, $previousStatus, $data, $actor) {
            $targetStatus = $data['status'] ?? $exception->status;

            $updates = [
                'status' => $targetStatus,
            ];

            if (isset($data['severity'])) {
                $updates['severity'] = $data['severity'];
            }

            if (isset($data['resolution_notes'])) {
                $updates['resolution_notes'] = $data['resolution_notes'];
            }

            if ($targetStatus === ShipmentException::STATUS_RESOLVED && ! $exception->resolved_at) {
                $updates['resolved_at'] = $data['resolved_at'] ?? now();
                $updates['resolved_by'] = $actor?->id;
            }

            $exception->update($updates);

            // Check if shipment can resume IN_TRANSIT when all exceptions are resolved
            $shipment = $exception->shipment()->first();
            if ($targetStatus === ShipmentException::STATUS_RESOLVED && $shipment && $shipment->status === Shipment::STATUS_EXCEPTION) {
                $remainingOpen = $shipment->openExceptions()->count();
                if ($remainingOpen === 0) {
                    $resumeStatus = $data['restore_status'] ?? Shipment::STATUS_IN_TRANSIT;
                    if ($shipment->canTransitionTo($resumeStatus)) {
                        $locationName = $shipment->currentLocation?->name
                            ?? $shipment->origin?->name
                            ?? 'Transit Hub';

                        TrackingEvent::create([
                            'shipment_id' => $shipment->id,
                            'location_id' => $shipment->current_location_id,
                            'location_name' => $locationName,
                            'status' => $resumeStatus,
                            'description' => "Exception [{$exception->type}] resolved. " . ($data['resolution_notes'] ?? 'Shipment cleared to resume.'),
                            'event_time' => $updates['resolved_at'] ?? now(),
                            'latitude' => $shipment->currentLocation?->latitude,
                            'longitude' => $shipment->currentLocation?->longitude,
                            'created_by' => $actor?->id,
                        ]);

                        $shipment->update(['status' => $resumeStatus]);
                    }
                }
            }

            $this->auditLogService->record(
                $targetStatus === ShipmentException::STATUS_RESOLVED ? 'EXCEPTION_RESOLVED' : 'EXCEPTION_UPDATED',
                'ShipmentException',
                $exception->id,
                "Status kendala [{$exception->type}] pada {$shipment?->tracking_number} diperbarui menjadi {$targetStatus}.",
                $actor,
                ['status' => $previousStatus],
                ['status' => $targetStatus, 'resolution_notes' => $data['resolution_notes'] ?? null]
            );

            if ($targetStatus === ShipmentException::STATUS_RESOLVED && $shipment) {
                $this->notificationService->notifyShipmentStakeholders(
                    $shipment,
                    AppNotification::TYPE_EXCEPTION_RESOLVED,
                    "Kendala Diselesaikan ({$shipment->tracking_number})",
                    "Kendala [{$exception->type}] telah diselesaikan. " . ($data['resolution_notes'] ?? '')
                );
            }

            return $exception->fresh(['reporter', 'resolver', 'trackingEvent']);
        });
    }
}

