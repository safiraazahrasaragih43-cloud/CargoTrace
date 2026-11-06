<?php

namespace Database\Factories;

use App\Models\Carrier;
use App\Models\Location;
use App\Models\Shipment;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ShipmentFactory extends Factory
{
    protected $model = Shipment::class;

    public function definition(): array
    {
        return [
            'tracking_number' => 'CT-' . date('Ymd') . '-' . strtoupper(fake()->unique()->lexify('????')),
            'customer_id' => User::factory()->customer(),
            'carrier_id' => Carrier::factory(),
            'origin_id' => Location::factory(),
            'destination_id' => Location::factory(),
            'current_location_id' => null,
            'status' => Shipment::STATUS_CREATED,
            'departure_at' => now(),
            'estimated_delivery_at' => now()->addDays(5),
            'actual_delivery_at' => null,
            'delay_hours' => 0,
            'notes' => fake()->sentence(),
        ];
    }
}
