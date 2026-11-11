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
        $cma = Carrier::where('code', 'CMDU')->first();

        $originJakarta = Location::where('code', 'IDTPP')->first();
        $originShanghai = Location::where('code', 'CNSHA')->first();
        $hubSingapore = Location::where('code', 'SGSIN')->first();
        $destRotterdam = Location::where('code', 'NLRTM')->first();
        $destLAX = Location::where('code', 'USLAX')->first();
        $destSydney = Location::where('code', 'AUSYD')->first();
        $destMelbourne = Location::where('code', 'AUMEL')->first();

        // 1. SEA -> Europe (Limit 12h | Customs Hold Delay +16h -> MEDIUM)
        $expected1 = now()->addDays(10);
        $s1 = Shipment::updateOrCreate(
            ['tracking_number' => 'CT-20261101-9821'],
            [
                'customer_id' => $customer->id,
                'carrier_id' => $maersk?->id ?: 1,
                'origin_id' => $originJakarta?->id ?: 1,
                'destination_id' => $destRotterdam?->id ?: 2,
                'current_location_id' => $hubSingapore?->id ?: 1,
                'status' => Shipment::STATUS_IN_TRANSIT,
                'departure_at' => now()->subDays(4),
                'expected_delivery_at' => $expected1,
                'estimated_delivery_at' => (clone $expected1)->addHours(16),
                'delay_hours' => 16,
                'is_delayed' => true,
                'delay_reason' => "Exception CUSTOMS_HOLD — Delayed 16h vs expected arrival ({$expected1->format('Y-m-d H:i')} UTC). Severity: MEDIUM. Corridor 'Southeast Asia → Europe' limit: 12h (THRESHOLD EXCEEDED).",
                'notes' => 'Container standard dry 40ft; high priority solar & battery electronics',
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

        // 2. Asia -> North America (Limit 18h | Weather Delay +28h -> HIGH, EXCEPTION)
        $expected2 = now()->addDays(8);
        $s2 = Shipment::updateOrCreate(
            ['tracking_number' => 'CT-20261105-4412'],
            [
                'customer_id' => $customer->id,
                'carrier_id' => $dhl?->id ?: 1,
                'origin_id' => $originShanghai?->id ?: 1,
                'destination_id' => $destLAX?->id ?: 3,
                'current_location_id' => $originShanghai?->id ?: 1,
                'status' => Shipment::STATUS_EXCEPTION,
                'departure_at' => now()->subDays(2),
                'expected_delivery_at' => $expected2,
                'estimated_delivery_at' => (clone $expected2)->addHours(28),
                'delay_hours' => 28,
                'is_delayed' => true,
                'delay_reason' => "Exception WEATHER_DELAY — Delayed 28h vs expected arrival ({$expected2->format('Y-m-d H:i')} UTC). Severity: HIGH. Corridor 'Asia → North America' limit: 18h (THRESHOLD EXCEEDED).",
                'notes' => 'Trans-Pacific apparel & semiconductor consignment rerouted due to North Pacific typhoon',
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

        // 3. SEA -> Australia (Limit 24h | Delivered on time with resolved Documentation Issue)
        $expected3 = now()->subDays(2);
        $s3 = Shipment::updateOrCreate(
            ['tracking_number' => 'CT-20261020-1109'],
            [
                'customer_id' => $customer->id,
                'carrier_id' => $msc?->id ?: 1,
                'origin_id' => $hubSingapore?->id ?: 1,
                'destination_id' => $destSydney?->id ?: 1,
                'current_location_id' => $destSydney?->id ?: 1,
                'status' => Shipment::STATUS_DELIVERED,
                'departure_at' => now()->subDays(10),
                'expected_delivery_at' => $expected3,
                'estimated_delivery_at' => $expected3,
                'actual_delivery_at' => $expected3,
                'delay_hours' => 0,
                'is_delayed' => false,
                'delay_reason' => "On schedule vs expected arrival ({$expected3->format('Y-m-d H:i')} UTC). Corridor 'Southeast Asia → Australia' threshold: 24h.",
                'notes' => 'Delivered and signed at Port Botany Medical Depot',
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

        // 4. SEA -> Australia (Booked & On Schedule)
        $expected4 = now()->addDays(14);
        $s4 = Shipment::updateOrCreate(
            ['tracking_number' => 'CT-20261108-7731'],
            [
                'customer_id' => $customer->id,
                'carrier_id' => $cma?->id ?: 1,
                'origin_id' => $originJakarta?->id ?: 1,
                'destination_id' => $destMelbourne?->id ?: 1,
                'current_location_id' => $originJakarta?->id ?: 1,
                'status' => Shipment::STATUS_BOOKED,
                'departure_at' => now()->addDays(1),
                'expected_delivery_at' => $expected4,
                'estimated_delivery_at' => $expected4,
                'delay_hours' => 0,
                'is_delayed' => false,
                'delay_reason' => "On schedule vs expected arrival ({$expected4->format('Y-m-d H:i')} UTC). Corridor 'Southeast Asia → Australia' threshold: 24h.",
                'notes' => 'Scheduled departure on CMA CGM Oceania Shuttle',
            ]
        );

        $s4->items()->delete();
        $s4->items()->create([
            'description' => 'Specialty Arabica Coffee Beans',
            'quantity' => 200,
            'weight_kg' => 12000.00,
            'dimensions' => '20ft Container',
            'hs_code' => '0901.11',
            'value_amount' => 68000.00,
        ]);
    }
}
