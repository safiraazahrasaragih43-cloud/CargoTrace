<?php

namespace Database\Factories;

use App\Models\Shipment;
use App\Models\ShipmentException;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ShipmentExceptionFactory extends Factory
{
    protected $model = ShipmentException::class;

    public function definition(): array
    {
        return [
            'shipment_id' => Shipment::factory(),
            'tracking_event_id' => null,
            'type' => ShipmentException::TYPE_CUSTOMS_HOLD,
            'severity' => ShipmentException::SEVERITY_MEDIUM,
            'status' => ShipmentException::STATUS_OPEN,
            'description' => fake()->sentence(),
            'resolution_notes' => null,
            'detected_at' => now()->subHours(4),
            'resolved_at' => null,
            'reported_by' => User::factory(),
            'resolved_by' => null,
        ];
    }
}
