<?php

namespace Database\Factories;

use App\Models\Location;
use Illuminate\Database\Eloquent\Factories\Factory;

class LocationFactory extends Factory
{
    protected $model = Location::class;

    public function definition(): array
    {
        return [
            'name' => 'Port of ' . fake()->city(),
            'code' => strtoupper(fake()->unique()->lexify('?????')),
            'type' => Location::TYPE_PORT,
            'address' => fake()->streetAddress(),
            'city' => fake()->city(),
            'country' => fake()->countryCode(),
            'latitude' => fake()->latitude(),
            'longitude' => fake()->longitude(),
        ];
    }
}
