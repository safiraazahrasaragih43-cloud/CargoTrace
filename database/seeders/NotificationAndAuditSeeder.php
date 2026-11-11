<?php

namespace Database\Seeders;

use App\Models\AppNotification;
use App\Models\AuditLog;
use App\Models\Shipment;
use App\Models\User;
use Illuminate\Database\Seeder;

class NotificationAndAuditSeeder extends Seeder
{
    /**
     * Run initial notifications and audit logs seeders idempotently.
     */
    public function run(): void
    {
        $admin = User::where('email', 'admin@cargotrace.com')->first();
        $operator = User::where('email', 'operator@cargotrace.com')->first();
        $customer = User::where('email', 'customer@cargotrace.com')->first();

        $shipments = Shipment::orderBy('id')->take(3)->get();
        if ($shipments->isEmpty()) {
            return;
        }

        $firstShipment = $shipments->first();
        $delayedShipment = $shipments->firstWhere('is_delayed', true) ?? $shipments->last();

        if ($customer) {
            AppNotification::firstOrCreate(
                [
                    'user_id' => $customer->id,
                    'shipment_id' => $firstShipment->id,
                    'title' => 'Status Pengiriman Diperbarui',
                ],
                [
                    'type' => AppNotification::TYPE_TRACKING_UPDATE,
                    'message' => "Pengiriman {$firstShipment->tracking_number} saat ini berstatus {$firstShipment->status} pada rute koridor internasional.",
                    'read_at' => null,
                ]
            );

            AppNotification::firstOrCreate(
                [
                    'user_id' => $customer->id,
                    'shipment_id' => $delayedShipment->id,
                    'title' => 'Peringatan Keterlambatan ETA Rute Global',
                ],
                [
                    'type' => AppNotification::TYPE_ETA_DELAY,
                    'message' => "Terdeteksi potensi keterlambatan pada pengiriman {$delayedShipment->tracking_number}. Tim logistik memantau jadwal kapal dan pemeriksaan pelabuhan.",
                    'read_at' => null,
                ]
            );
        }

        if ($operator) {
            AppNotification::firstOrCreate(
                [
                    'user_id' => $operator->id,
                    'shipment_id' => $delayedShipment->id,
                    'title' => 'Tinjauan Kendala Koridor Diperlukan',
                ],
                [
                    'type' => AppNotification::TYPE_EXCEPTION_ALERT,
                    'message' => "Mohon verifikasi kendala dokumen atau bea cukai pada pengiriman {$delayedShipment->tracking_number}.",
                    'read_at' => null,
                ]
            );
        }

        if ($admin) {
            AppNotification::firstOrCreate(
                [
                    'user_id' => $admin->id,
                    'shipment_id' => $firstShipment->id,
                    'title' => 'Ringkasan Operasi Harian CargoTrace',
                ],
                [
                    'type' => AppNotification::TYPE_TRACKING_UPDATE,
                    'message' => "Sinkronisasi data awal rute Asia-Eropa, Asia-Amerika Utara, dan Asia-Australia untuk {$firstShipment->tracking_number} selesai.",
                    'read_at' => now(),
                ]
            );

            AuditLog::firstOrCreate(
                [
                    'action' => 'SHIPMENT_CREATED',
                    'entity_type' => Shipment::class,
                    'entity_id' => $firstShipment->id,
                ],
                [
                    'user_id' => $operator?->id ?? $admin->id,
                    'description' => "Inisialisasi pengiriman {$firstShipment->tracking_number} ke dalam sistem.",
                    'old_values' => null,
                    'new_values' => [
                        'tracking_number' => $firstShipment->tracking_number,
                        'status' => $firstShipment->status,
                    ],
                    'ip_address' => '127.0.0.1',
                ]
            );

            AuditLog::firstOrCreate(
                [
                    'action' => 'TRACKING_EVENT_ADDED',
                    'entity_type' => Shipment::class,
                    'entity_id' => $delayedShipment->id,
                ],
                [
                    'user_id' => $operator?->id ?? $admin->id,
                    'description' => "Pembaruan checkpoint lokasi dan kalkulasi ulang ETA untuk {$delayedShipment->tracking_number}.",
                    'old_values' => ['status' => 'BOOKED'],
                    'new_values' => ['status' => $delayedShipment->status, 'is_delayed' => (bool) $delayedShipment->is_delayed],
                    'ip_address' => '127.0.0.1',
                ]
            );
        }
    }
}

