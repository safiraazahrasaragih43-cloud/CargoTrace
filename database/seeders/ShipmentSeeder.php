<?php

namespace Database\Seeders;

use App\Models\Carrier;
use App\Models\Location;
use App\Models\Shipment;
use App\Models\User;
use Illuminate\Database\Seeder;

class ShipmentSeeder extends Seeder
{
    public function run(): void
    {
        $customer = User::where('role', User::ROLE_CUSTOMER)->first();
        if (! $customer) {
            return;
        }

        $maersk = Carrier::where('code', 'MAEU')->first();
        $dhl = Carrier::where('code', 'DHLG')->first();
        $msc = Carrier::where('code', 'MSCU')->first();

        $originJakarta = Location::where('code', 'IDTPP')->first();
        $destRotterdam = Location::where('code', 'NLRTM')->first();
        $destLAX = Location::where('code', 'USLAX')->first();
        $hubSingapore = Location::where('code', 'SGSIN')->first();

        // 1. In Transit Shipment
        $s1 = Shipment::updateOrCreate(
            ['tracking_number' => 'CT-20261101-9821'],
            [
                'customer_id' => $customer->id,
                'carrier_id' => $maersk?->id ?: 1,
                'origin_id' => $originJakarta?->id ?: 1,
                'destination_id' => $destRotterdam?->id ?: 2,
                'current_location_id' => $hubSingapore?->id ?: 1,
                'status' => Shipment::STATUS_IN_TRANSIT,
                'departure_at' => now()->subDays(3),
                'estimated_delivery_at' => now()->addDays(12),
                'delay_hours' => 0,
                'notes' => 'Container standard dry 40ft; high priority electronics',
            ]
        );

        $s1->items()->delete();
        $s1->items()->create([
            'description' => 'Industrial Solar Inverters',
            'quantity' => 120,
            'weight_kg' => 4500.00,
            'dimensions' => '120x80x160 cm',
            'hs_code' => '8504.40',
            'value_amount' => 180000.00,
        ]);
        $s1->items()->create([
            'description' => 'Lithium Storage Batteries',
            'quantity' => 40,
            'weight_kg' => 2400.00,
            'dimensions' => '100x100x120 cm',
            'hs_code' => '8507.60',
            'value_amount' => 95000.00,
        ]);

        // 2. Booked Shipment
        $s2 = Shipment::updateOrCreate(
            ['tracking_number' => 'CT-20261105-4412'],
            [
                'customer_id' => $customer->id,
                'carrier_id' => $dhl?->id ?: 1,
                'origin_id' => $originJakarta?->id ?: 1,
                'destination_id' => $destLAX?->id ?: 3,
                'current_location_id' => null,
                'status' => Shipment::STATUS_BOOKED,
                'departure_at' => now()->addDays(2),
                'estimated_delivery_at' => now()->addDays(18),
                'delay_hours' => 0,
                'notes' => 'Apparel export consignment',
            ]
        );

        $s2->items()->delete();
        $s2->items()->create([
            'description' => 'Organic Cotton Garments',
            'quantity' => 850,
            'weight_kg' => 1850.00,
            'dimensions' => '80x60x50 cm',
            'hs_code' => '6109.10',
            'value_amount' => 42500.00,
        ]);

        // 3. Delivered Shipment
        $s3 = Shipment::updateOrCreate(
            ['tracking_number' => 'CT-20261020-1109'],
            [
                'customer_id' => $customer->id,
                'carrier_id' => $msc?->id ?: 1,
                'origin_id' => $hubSingapore?->id ?: 1,
                'destination_id' => $originJakarta?->id ?: 1,
                'current_location_id' => $originJakarta?->id ?: 1,
                'status' => Shipment::STATUS_DELIVERED,
                'departure_at' => now()->subDays(10),
                'estimated_delivery_at' => now()->subDays(2),
                'actual_delivery_at' => now()->subDays(2),
                'delay_hours' => 0,
                'notes' => 'Successfully signed and received at warehouse gate 4',
            ]
        );

        $s3->items()->delete();
        $s3->items()->create([
            'description' => 'Precision Medical Diagnostics Kits',
            'quantity' => 300,
            'weight_kg' => 620.00,
            'dimensions' => '40x40x30 cm',
            'hs_code' => '3822.00',
            'value_amount' => 74000.00,
        ]);
    }
}
