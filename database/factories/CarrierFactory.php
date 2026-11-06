<?php

namespace Database\Factories;

use App\Models\Carrier;
use Illuminate\Database\Eloquent\Factories\Factory;

class CarrierFactory extends Factory
{
    protected $model = Carrier::class;

    public function definition(): array
    {
        return [
            'name' => fake()->company() . ' Lines',
            'code' => strtoupper(fake()->unique()->lexify('????')),
            'contact_email' => fake()->companyEmail(),
            'phone' => fake()->phoneNumber(),
            'tracking_url_template' => 'https://track.carrier.com?q={TRACKING_NUMBER}',
            'is_active' => true,
        ];
    }
}

