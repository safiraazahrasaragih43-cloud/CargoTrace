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
        $locRotterdam = Location::where('code', 'NLRTM')->first();
        $locLAX = Location::where('code', 'USLAX')->first();

        // 1. Shipment CT-20261101-9821 (Currently IN_TRANSIT)
        $s1 = Shipment::where('tracking_number', 'CT-20261101-9821')->first();
        if ($s1) {
            $s1->trackingEvents()->delete();

            TrackingEvent::create([
                'shipment_id' => $s1->id,
                'location_id' => $locJakarta?->id,
                'location_name' => 'Port of Tanjung Priok (IDTPP)',
                'status' => Shipment::STATUS_CREATED,
                'description' => 'Consignment manifest generated and electronic export declaration filed.',
                'event_time' => now()->subDays(4),
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
                'event_time' => now()->subDays(3)->subHours(12),
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
                'event_time' => now()->subDays(3),
                'latitude' => $locJakarta?->latitude ?: -6.1018,
                'longitude' => $locJakarta?->longitude ?: 106.8833,
                'created_by' => $operator?->id,
            ]);

            TrackingEvent::create([
                'shipment_id' => $s1->id,
                'location_id' => $locJakarta?->id,
                'location_name' => 'Port of Tanjung Priok Outbound Channel',
                'status' => Shipment::STATUS_IN_TRANSIT,
                'description' => 'Vessel departed Tanjung Priok; heading toward Singapore Malacca Strait corridor.',
                'event_time' => now()->subDays(2)->subHours(10),
                'latitude' => -5.9500,
                'longitude' => 106.9000,
                'created_by' => $operator?->id,
            ]);

            TrackingEvent::create([
                'shipment_id' => $s1->id,
                'location_id' => $locSingapore?->id,
                'location_name' => 'Port of Singapore (SGSIN) - Pasir Panjang Terminal',
                'status' => Shipment::STATUS_IN_TRANSIT,
                'description' => 'Vessel berthed. Container discharged for transshipment feeder connection.',
                'event_time' => now()->subHours(6),
                'latitude' => $locSingapore?->latitude ?: 1.2644,
                'longitude' => $locSingapore?->longitude ?: 103.8222,
                'created_by' => $operator?->id,
            ]);
        }

        // 2. Shipment CT-20261105-4412 (Currently BOOKED)
        $s2 = Shipment::where('tracking_number', 'CT-20261105-4412')->first();
        if ($s2) {
            $s2->trackingEvents()->delete();

            TrackingEvent::create([
                'shipment_id' => $s2->id,
                'location_id' => $locJakarta?->id,
                'location_name' => 'Soekarno-Hatta Cargo Terminal (CGK)',
                'status' => Shipment::STATUS_CREATED,
                'description' => 'Airway bill CT-20261105-4412 drafted for textile cargo consignee.',
                'event_time' => now()->subDays(1),
                'latitude' => -6.1275,
                'longitude' => 106.6537,
                'created_by' => $operator?->id,
            ]);

            TrackingEvent::create([
                'shipment_id' => $s2->id,
                'location_id' => $locJakarta?->id,
                'location_name' => 'Soekarno-Hatta Cargo Terminal (CGK)',
                'status' => Shipment::STATUS_BOOKED,
                'description' => 'Air freight pallet space booked with DHL Global Forwarding.',
                'event_time' => now()->subHours(18),
                'latitude' => -6.1275,
                'longitude' => 106.6537,
                'created_by' => $operator?->id,
            ]);
        }

        // 3. Shipment CT-20261020-1109 (Currently DELIVERED)
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
                'location_name' => 'Port of Singapore (SGSIN)',
                'status' => Shipment::STATUS_BOOKED,
                'description' => 'Confirmed on MSC feeder line Mediterranean Express.',
                'event_time' => now()->subDays(9)->subHours(16),
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
                'location_name' => 'Sunda Strait Corridor',
                'status' => Shipment::STATUS_IN_TRANSIT,
                'description' => 'Vessel transiting Indonesian territorial waters on scheduled route.',
                'event_time' => now()->subDays(5),
                'latitude' => -5.8500,
                'longitude' => 105.7500,
                'created_by' => $operator?->id,
            ]);

            TrackingEvent::create([
                'shipment_id' => $s3->id,
                'location_id' => $locJakarta?->id,
                'location_name' => 'Port of Tanjung Priok Customs Zone',
                'status' => Shipment::STATUS_OUT_FOR_DELIVERY,
                'description' => 'Customs import green line clearance granted; dispatched on local delivery truck.',
                'event_time' => now()->subDays(2)->subHours(6),
                'latitude' => $locJakarta?->latitude ?: -6.1018,
                'longitude' => $locJakarta?->longitude ?: 106.8833,
                'created_by' => $operator?->id,
            ]);

            TrackingEvent::create([
                'shipment_id' => $s3->id,
                'location_id' => $locJakarta?->id,
                'location_name' => 'Jakarta Industrial Estate Pulogadung (JIEP)',
                'status' => Shipment::STATUS_DELIVERED,
                'description' => 'Package signed and received in good condition by Dr. R. Hidayat.',
                'event_time' => now()->subDays(2),
                'latitude' => -6.1950,
                'longitude' => 106.9050,
                'created_by' => $operator?->id,
            ]);
        }
    }
}

