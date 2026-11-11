<?php

namespace Database\Seeders;

use App\Models\Location;
use App\Models\Shipment;
use App\Models\TrackingEvent;
use App\Models\User;
use Illuminate\Database\Seeder;

class TrackingEventSeeder extends Seeder
{
    public function run(): void
    {
        $operator = User::where('role', User::ROLE_OPERATOR)->first()
            ?: User::where('role', User::ROLE_ADMIN)->first();

        $locJakarta = Location::where('code', 'IDTPP')->first();
        $locSingapore = Location::where('code', 'SGSIN')->first();
        $locShanghai = Location::where('code', 'CNSHA')->first();
        $locSydney = Location::where('code', 'AUSYD')->first();

        // 1. SEA -> Europe: CT-20261101-9821 (IN_TRANSIT)
        $s1 = Shipment::where('tracking_number', 'CT-20261101-9821')->first();
        if ($s1) {
            $s1->trackingEvents()->delete();

            TrackingEvent::create([
                'shipment_id' => $s1->id,
                'location_id' => $locJakarta?->id,
                'location_name' => 'Port of Tanjung Priok (IDTPP)',
                'status' => Shipment::STATUS_CREATED,
                'description' => 'Consignment manifest generated and electronic export declaration filed.',
                'event_time' => now()->subDays(5),
                'latitude' => $locJakarta?->latitude ?: -6.1018,
                'longitude' => $locJakarta?->longitude ?: 106.8833,
                'created_by' => $operator?->id,
            ]);

            TrackingEvent::create([
                'shipment_id' => $s1->id,
                'location_id' => $locJakarta?->id,
                'location_name' => 'Port of Tanjung Priok (IDTPP)',
                'status' => Shipment::STATUS_BOOKED,
                'description' => 'Ocean container slot confirmed on Maersk Mc-Kinney Moller voy 2608W.',
                'event_time' => now()->subDays(4)->subHours(12),
                'latitude' => $locJakarta?->latitude ?: -6.1018,
                'longitude' => $locJakarta?->longitude ?: 106.8833,
                'created_by' => $operator?->id,
            ]);

            TrackingEvent::create([
                'shipment_id' => $s1->id,
                'location_id' => $locJakarta?->id,
                'location_name' => 'Port of Tanjung Priok Container Terminal 1',
                'status' => Shipment::STATUS_PICKED_UP,
                'description' => 'Container sealed (Seal #MSK-992140) and gated in at quay crane 03.',
                'event_time' => now()->subDays(4),
                'latitude' => $locJakarta?->latitude ?: -6.1018,
                'longitude' => $locJakarta?->longitude ?: 106.8833,
                'created_by' => $operator?->id,
            ]);

            TrackingEvent::create([
                'shipment_id' => $s1->id,
                'location_id' => $locSingapore?->id,
                'location_name' => 'Port of Singapore (SGSIN) - Pasir Panjang Terminal',
                'status' => Shipment::STATUS_IN_TRANSIT,
                'description' => 'Vessel berthed. Container cleared transshipment customs hold and transferred to Europe mainline.',
                'event_time' => now()->subHours(6),
                'latitude' => $locSingapore?->latitude ?: 1.2644,
                'longitude' => $locSingapore?->longitude ?: 103.8222,
                'created_by' => $operator?->id,
            ]);
        }

        // 2. Asia -> North America: CT-20261105-4412 (EXCEPTION - Weather Delay)
        $s2 = Shipment::where('tracking_number', 'CT-20261105-4412')->first();
        if ($s2) {
            $s2->trackingEvents()->delete();

            TrackingEvent::create([
                'shipment_id' => $s2->id,
                'location_id' => $locShanghai?->id,
                'location_name' => 'Port of Shanghai Yangshan (CNSHA)',
                'status' => Shipment::STATUS_CREATED,
                'description' => 'Export consignment manifest registered for Trans-Pacific lane.',
                'event_time' => now()->subDays(3),
                'latitude' => $locShanghai?->latitude ?: 30.6167,
                'longitude' => $locShanghai?->longitude ?: 122.0667,
                'created_by' => $operator?->id,
            ]);

            TrackingEvent::create([
                'shipment_id' => $s2->id,
                'location_id' => $locShanghai?->id,
                'location_name' => 'Port of Shanghai Yangshan (CNSHA)',
                'status' => Shipment::STATUS_PICKED_UP,
                'description' => 'Cargo loaded onto Trans-Pacific vessel at Yangshan Phase 4.',
                'event_time' => now()->subDays(2),
                'latitude' => $locShanghai?->latitude ?: 30.6167,
                'longitude' => $locShanghai?->longitude ?: 122.0667,
                'created_by' => $operator?->id,
            ]);

            TrackingEvent::create([
                'shipment_id' => $s2->id,
                'location_id' => $locShanghai?->id,
                'location_name' => 'East China Sea Holding Zone',
                'status' => Shipment::STATUS_EXCEPTION,
                'description' => 'Exception [WEATHER_DELAY]: Severe typhoon advisory delaying departure window by +28h.',
                'event_time' => now()->subHours(8),
                'latitude' => 31.1500,
                'longitude' => 124.2000,
                'created_by' => $operator?->id,
            ]);
        }

        // 3. SEA -> Australia: CT-20261020-1109 (DELIVERED)
        $s3 = Shipment::where('tracking_number', 'CT-20261020-1109')->first();
        if ($s3) {
            $s3->trackingEvents()->delete();

            TrackingEvent::create([
                'shipment_id' => $s3->id,
                'location_id' => $locSingapore?->id,
                'location_name' => 'Port of Singapore (SGSIN)',
                'status' => Shipment::STATUS_CREATED,
                'description' => 'Medical cargo consignment booked by pharmaceutical supplier.',
                'event_time' => now()->subDays(10),
                'latitude' => $locSingapore?->latitude ?: 1.2644,
                'longitude' => $locSingapore?->longitude ?: 103.8222,
                'created_by' => $operator?->id,
            ]);

            TrackingEvent::create([
                'shipment_id' => $s3->id,
                'location_id' => $locSingapore?->id,
                'location_name' => 'Port of Singapore Berth 07',
                'status' => Shipment::STATUS_PICKED_UP,
                'description' => 'Temperature-controlled reefer container loaded onto vessel.',
                'event_time' => now()->subDays(9),
                'latitude' => $locSingapore?->latitude ?: 1.2644,
                'longitude' => $locSingapore?->longitude ?: 103.8222,
                'created_by' => $operator?->id,
            ]);

            TrackingEvent::create([
                'shipment_id' => $s3->id,
                'location_id' => null,
                'location_name' => 'Timor Sea Maritime Corridor',
                'status' => Shipment::STATUS_IN_TRANSIT,
                'description' => 'Vessel transiting Southbound route toward Australian coastline.',
                'event_time' => now()->subDays(5),
                'latitude' => -11.5000,
                'longitude' => 126.5000,
                'created_by' => $operator?->id,
            ]);

            TrackingEvent::create([
                'shipment_id' => $s3->id,
                'location_id' => $locSydney?->id,
                'location_name' => 'Port of Sydney - Port Botany (AUSYD)',
                'status' => Shipment::STATUS_OUT_FOR_DELIVERY,
                'description' => 'Quarantine & documentation issue resolved; released for final depot transfer.',
                'event_time' => now()->subDays(2)->subHours(6),
                'latitude' => $locSydney?->latitude ?: -33.9722,
                'longitude' => $locSydney?->longitude ?: 151.2167,
                'created_by' => $operator?->id,
            ]);

            TrackingEvent::create([
                'shipment_id' => $s3->id,
                'location_id' => $locSydney?->id,
                'location_name' => 'Sydney Medical Logistics Hub',
                'status' => Shipment::STATUS_DELIVERED,
                'description' => 'Signed and received in good temperature-compliant condition.',
                'event_time' => now()->subDays(2),
                'latitude' => -33.8688,
                'longitude' => 151.2093,
                'created_by' => $operator?->id,
            ]);
        }

        // 4. SEA -> Australia: CT-20261108-7731 (BOOKED)
        $s4 = Shipment::where('tracking_number', 'CT-20261108-7731')->first();
        if ($s4) {
            $s4->trackingEvents()->delete();

            TrackingEvent::create([
                'shipment_id' => $s4->id,
                'location_id' => $locJakarta?->id,
                'location_name' => 'Port of Tanjung Priok (IDTPP)',
                'status' => Shipment::STATUS_BOOKED,
                'description' => 'Vessel booking confirmed on CMA CGM Oceania Shuttle to Melbourne.',
                'event_time' => now()->subHours(12),
                'latitude' => $locJakarta?->latitude ?: -6.1018,
                'longitude' => $locJakarta?->longitude ?: 106.8833,
                'created_by' => $operator?->id,
            ]);
        }
    }
}
