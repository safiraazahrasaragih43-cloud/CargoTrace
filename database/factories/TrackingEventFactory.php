<?php

namespace Database\Factories;

use App\Models\Location;
use App\Models\Shipment;
use App\Models\TrackingEvent;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class TrackingEventFactory extends Factory
{
    protected $model = TrackingEvent::class;

    public function definition(): array
    {
        return [
            'shipment_id' => Shipment::factory(),
            'location_id' => Location::factory(),
            'location_name' => fake()->city() . ' Freight Depot',
            'status' => Shipment::STATUS_IN_TRANSIT,
            'description' => fake()->sentence(),
            'event_time' => now(),
            'latitude' => fake()->latitude(),
            'longitude' => fake()->longitude(),
            'created_by' => User::factory(),
        ];
    }
}
