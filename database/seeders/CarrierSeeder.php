<?php

namespace Database\Seeders;

use App\Models\Carrier;
use Illuminate\Database\Seeder;

class CarrierSeeder extends Seeder
{
    public function run(): void
    {
        $carriers = [
            [
                'name' => 'A.P. Moller - Maersk',
                'code' => 'MAEU',
                'contact_email' => 'support@maersk.com',
                'phone' => '+45 33 63 33 63',
                'tracking_url_template' => 'https://www.maersk.com/tracking/{TRACKING_NUMBER}',
                'is_active' => true,
            ],
            [
                'name' => 'Mediterranean Shipping Company (MSC)',
                'code' => 'MSCU',
                'contact_email' => 'info@msc.com',
                'phone' => '+41 22 703 8888',
                'tracking_url_template' => 'https://www.msc.com/track-a-shipment?number={TRACKING_NUMBER}',
                'is_active' => true,
            ],
            [
                'name' => 'CMA CGM Group',
                'code' => 'CMDU',
                'contact_email' => 'ho.service@cma-cgm.com',
                'phone' => '+33 4 88 91 90 00',
                'tracking_url_template' => 'https://www.cma-cgm.com/ebusiness/tracking/search?SearchBy=BL&SearchValue={TRACKING_NUMBER}',
                'is_active' => true,
            ],
            [
                'name' => 'DHL Global Forwarding',
                'code' => 'DHLG',
                'contact_email' => 'support@dhl.com',
                'phone' => '+1 800 225 5345',
                'tracking_url_template' => 'https://www.dhl.com/global-en/home/tracking.html?tracking-id={TRACKING_NUMBER}',
                'is_active' => true,
            ],
            [
                'name' => 'FedEx Trade & Freight',
                'code' => 'FDXF',
                'contact_email' => 'freight_support@fedex.com',
                'phone' => '+1 800 463 3339',
                'tracking_url_template' => 'https://www.fedex.com/fedextrack/?trknbr={TRACKING_NUMBER}',
                'is_active' => true,
            ],
        ];

        foreach ($carriers as $carrier) {
            Carrier::updateOrCreate(['code' => $carrier['code']], $carrier);
        }
    }
}
