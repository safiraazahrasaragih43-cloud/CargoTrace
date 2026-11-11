<?php

namespace App\Services;

use App\Models\Shipment;
use App\Models\ShipmentException;
use Carbon\Carbon;
use DateTimeInterface;

class EtaService
{
    /**
     * Detect the global corridor configuration for a shipment based on origin/destination.
     */
    public function detectCorridor(Shipment $shipment): array
    {
        $shipment->loadMissing(['origin', 'destination']);
        $destCountry = $shipment->destination?->country;
        $corridors = config('cargotrace.corridors', []);

        foreach ($corridors as $key => $corridor) {
            if ($key === 'GLOBAL_DEFAULT') {
                continue;
            }

            $destinations = $corridor['destination_countries'] ?? [];
            if ($destCountry && in_array($destCountry, $destinations, true)) {
                return array_merge(['key' => $key], $corridor);
            }
        }

        $default = $corridors['GLOBAL_DEFAULT'] ?? [
            'name' => 'Global Standard Corridor',
            'delay_threshold_hours' => (int) config('cargotrace.default_delay_threshold_hours', 12),
            'primary_exception' => 'PORT_CONGESTION',
            'kpi_focus' => 'on_time_delivery_rate',
        ];

        return array_merge(['key' => 'GLOBAL_DEFAULT'], $default);
    }

    /**
     * Map delay hours deterministically to module severity tier (Slide 08).
     */
    public function determineDelaySeverity(int $delayHours): string
    {
        if ($delayHours <= 0) {
            return 'ON_TIME';
        }

        if ($delayHours <= 6) {
            return 'LOW';
        }

        if ($delayHours <= 24) {
            return 'MEDIUM';
        }

        return 'HIGH';
    }

    /**
     * Evaluate expected vs estimated/actual delivery and persist deterministic delay metrics.
     */
    public function evaluateShipment(
        Shipment $shipment,
        ?DateTimeInterface $referenceTime = null,
        ?string $customReason = null
    ): array {
        $expected = $shipment->expected_delivery_at ?? $shipment->estimated_delivery_at;
        $corridor = $this->detectCorridor($shipment);
        $thresholdHours = (int) ($corridor['delay_threshold_hours'] ?? 12);

        if (! $expected) {
            return [
                'shipment_id' => $shipment->id,
                'corridor' => $corridor,
                'delay_hours' => 0,
                'is_delayed' => false,
                'exceeds_threshold' => false,
                'threshold_hours' => $thresholdHours,
                'severity' => 'ON_TIME',
                'reason' => 'Expected delivery date not yet assigned.',
            ];
        }

        if ($shipment->status === Shipment::STATUS_DELIVERED && $shipment->actual_delivery_at) {
            $effectiveArrival = Carbon::instance($shipment->actual_delivery_at);
        } elseif ($shipment->status === Shipment::STATUS_CANCELLED) {
            $effectiveArrival = Carbon::instance($expected);
        } else {
            $checkNow = $referenceTime ? Carbon::instance($referenceTime) : now();
            $estimated = $shipment->estimated_delivery_at ? Carbon::instance($shipment->estimated_delivery_at) : Carbon::instance($expected);
            $effectiveArrival = $checkNow->greaterThan($estimated) ? $checkNow : $estimated;
        }

        $expectedCarbon = Carbon::instance($expected);
        $delayHours = $effectiveArrival->greaterThan($expectedCarbon)
            ? (int) round($expectedCarbon->diffInMinutes($effectiveArrival) / 60)
            : 0;

        $isDelayed = $delayHours > 0;
        $exceedsThreshold = $delayHours > $thresholdHours;
        $severity = $this->determineDelaySeverity($delayHours);

        if (! $isDelayed) {
            $reason = "On schedule vs expected arrival ({$expectedCarbon->format('Y-m-d H:i')} UTC). Corridor '{$corridor['name']}' threshold: {$thresholdHours}h.";
        } else {
            $toleranceLabel = $exceedsThreshold ? 'THRESHOLD EXCEEDED' : 'within corridor threshold';
            $prefix = $customReason ? "{$customReason} — " : '';
            $reason = "{$prefix}Delayed {$delayHours}h vs expected arrival ({$expectedCarbon->format('Y-m-d H:i')} UTC). Severity: {$severity}. Corridor '{$corridor['name']}' limit: {$thresholdHours}h ({$toleranceLabel}).";
        }

        $shipment->update([
            'expected_delivery_at' => $shipment->expected_delivery_at ?? $expectedCarbon,
            'delay_hours' => $delayHours,
            'is_delayed' => $isDelayed,
            'delay_reason' => $reason,
        ]);

        return [
            'shipment_id' => $shipment->id,
            'tracking_number' => $shipment->tracking_number,
            'expected_delivery_at' => $expectedCarbon->toISOString(),
            'estimated_delivery_at' => $shipment->estimated_delivery_at?->toISOString(),
            'actual_delivery_at' => $shipment->actual_delivery_at?->toISOString(),
            'corridor' => $corridor,
            'delay_hours' => $delayHours,
            'is_delayed' => $isDelayed,
            'exceeds_threshold' => $exceedsThreshold,
            'threshold_hours' => $thresholdHours,
            'severity' => $severity,
            'reason' => $reason,
        ];
    }

    /**
     * Update shipment estimated arrival and re-run deterministic delay evaluation.
     */
    public function recalculateEta(Shipment $shipment, DateTimeInterface|string $newEstimatedAt, ?string $reason = null): array
    {
        $newEta = $newEstimatedAt instanceof DateTimeInterface
            ? Carbon::instance($newEstimatedAt)
            : Carbon::parse($newEstimatedAt);

        if (empty($shipment->expected_delivery_at) && ! empty($shipment->estimated_delivery_at)) {
            $shipment->expected_delivery_at = $shipment->estimated_delivery_at;
        }

        $shipment->estimated_delivery_at = $newEta;
        $shipment->save();

        return $this->evaluateShipment($shipment, null, $reason);
    }

    /**
     * Compute combined global KPIs across all 3 corridors (Slide 23).
     */
    public function calculateGlobalKpis(): array
    {
        $totalShipments = Shipment::count();
        $deliveredShipments = Shipment::where('status', Shipment::STATUS_DELIVERED)->get();
        $deliveredCount = $deliveredShipments->count();

        // KPI 1 (Group A): On-time delivery rate
        $onTimeDelivered = $deliveredShipments->where('delay_hours', 0)->count();
        $onTimeDeliveryRate = $deliveredCount > 0
            ? round(($onTimeDelivered / $deliveredCount) * 100, 1)
            : 100.0;

        // KPI 2 (Group B): Average delay duration
        $delayedShipments = Shipment::where('delay_hours', '>', 0)->get();
        $delayedCount = $delayedShipments->count();
        $averageDelayHours = $delayedCount > 0
            ? round($delayedShipments->avg('delay_hours'), 1)
            : 0.0;

        // KPI 3 (Group C): Exception resolution time
        $openExceptionsCount = ShipmentException::where('status', '!=', ShipmentException::STATUS_RESOLVED)->count();
        $resolvedExceptions = ShipmentException::where('status', ShipmentException::STATUS_RESOLVED)
            ->whereNotNull('resolved_at')
            ->get();

        $avgExceptionResolutionHours = 0.0;
        if ($resolvedExceptions->isNotEmpty()) {
            $totalHours = $resolvedExceptions->sum(fn ($ex) => $ex->resolutionDurationHours() ?? 0);
            $avgExceptionResolutionHours = round($totalHours / $resolvedExceptions->count(), 1);
        }

        return [
            'total_shipments' => $totalShipments,
            'delivered_count' => $deliveredCount,
            'delayed_count' => $delayedCount,
            'on_time_delivery_rate' => $onTimeDeliveryRate,
            'average_delay_hours' => $averageDelayHours,
            'open_exceptions_count' => $openExceptionsCount,
            'avg_exception_resolution_hours' => $avgExceptionResolutionHours,
        ];
    }
}
