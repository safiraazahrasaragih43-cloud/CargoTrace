<?php

namespace Database\Factories;

use App\Models\Shipment;
use App\Models\ShipmentItem;
use Illuminate\Database\Eloquent\Factories\Factory;

class ShipmentItemFactory extends Factory
{
    protected $model = ShipmentItem::class;

    public function definition(): array
    {
        return [
            'shipment_id' => Shipment::factory(),
            'description' => fake()->words(3, true),
            'quantity' => fake()->numberBetween(1, 100),
            'weight_kg' => fake()->randomFloat(2, 5, 2500),
            'dimensions' => '120x80x100 cm',
            'hs_code' => fake()->numerify('####.##'),
            'value_amount' => fake()->randomFloat(2, 100, 50000),
        ];
    }
}

