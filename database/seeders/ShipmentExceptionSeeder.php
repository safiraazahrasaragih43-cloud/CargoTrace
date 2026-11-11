<?php

namespace Database\Seeders;

use App\Models\Shipment;
use App\Models\ShipmentException;
use App\Models\User;
use Illuminate\Database\Seeder;

class ShipmentExceptionSeeder extends Seeder
{
    public function run(): void
    {
        $operator = User::where('role', User::ROLE_OPERATOR)->first()
            ?: User::where('role', User::ROLE_ADMIN)->first();

        // 1. SEA -> Europe: CUSTOMS_HOLD (Investigating)
        $s1 = Shipment::where('tracking_number', 'CT-20261101-9821')->first();
        if ($s1) {
            $s1->exceptions()->delete();
            ShipmentException::create([
                'shipment_id' => $s1->id,
                'type' => ShipmentException::TYPE_CUSTOMS_HOLD,
                'severity' => ShipmentException::SEVERITY_MEDIUM,
                'status' => ShipmentException::STATUS_INVESTIGATING,
                'description' => 'Transshipment customs X-ray inspection initiated at Singapore terminal; feeder schedule shifted +16h.',
                'detected_at' => now()->subHours(14),
                'reported_by' => $operator?->id,
            ]);
        }

        // 2. Asia -> North America: WEATHER_DELAY (Open - High Severity)
        $s2 = Shipment::where('tracking_number', 'CT-20261105-4412')->first();
        if ($s2) {
            $s2->exceptions()->delete();
            ShipmentException::create([
                'shipment_id' => $s2->id,
                'type' => ShipmentException::TYPE_WEATHER_DELAY,
                'severity' => ShipmentException::SEVERITY_HIGH,
                'status' => ShipmentException::STATUS_OPEN,
                'description' => 'North Pacific severe storm system forcing 28-hour maritime detour before Los Angeles approach.',
                'detected_at' => now()->subHours(8),
                'reported_by' => $operator?->id,
            ]);
        }

        // 3. SEA -> Australia: DOCUMENTATION_ISSUE (Resolved in 4.5 hours)
        $s3 = Shipment::where('tracking_number', 'CT-20261020-1109')->first();
        if ($s3) {
            $s3->exceptions()->delete();
            ShipmentException::create([
                'shipment_id' => $s3->id,
                'type' => ShipmentException::TYPE_DOCUMENTATION_ISSUE,
                'severity' => ShipmentException::SEVERITY_LOW,
                'status' => ShipmentException::STATUS_RESOLVED,
                'description' => 'Certificate of Origin HS code mismatch flagged during pre-arrival screening at Sydney.',
                'resolution_notes' => 'Amended commercial invoice & quarantine certificate submitted and cleared.',
                'detected_at' => now()->subDays(3)->subHours(5),
                'resolved_at' => now()->subDays(3)->subMinutes(30),
                'reported_by' => $operator?->id,
                'resolved_by' => $operator?->id,
            ]);
        }
    }
}

