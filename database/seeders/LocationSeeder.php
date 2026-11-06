<?php

namespace Database\Seeders;

use App\Models\Location;
use Illuminate\Database\Seeder;

class LocationSeeder extends Seeder
{
    public function run(): void
    {
        $locations = [
            [
                'name' => 'Port of Singapore',
                'code' => 'SGSIN',
                'type' => Location::TYPE_PORT,
                'address' => '7B Keppel Rd, Tanjong Pagar',
                'city' => 'Singapore',
                'country' => 'Singapore',
                'latitude' => 1.2644000,
                'longitude' => 103.8220000,
            ],
            [
                'name' => 'Port of Tanjung Priok',
                'code' => 'IDTPP',
                'type' => Location::TYPE_PORT,
                'address' => 'Jl. Raya Pelabuhan No.9, Tanjung Priok',
                'city' => 'Jakarta Utara',
                'country' => 'Indonesia',
                'latitude' => -6.1018000,
                'longitude' => 106.8833000,
            ],
            [
                'name' => 'Port of Rotterdam',
                'code' => 'NLRTM',
                'type' => Location::TYPE_PORT,
                'address' => 'Wilhelminakade 902, 3072 AP',
                'city' => 'Rotterdam',
                'country' => 'Netherlands',
                'latitude' => 51.9244000,
                'longitude' => 4.4777000,
            ],
            [
                'name' => 'Port of Shanghai (Yangshan Deep-Water Port)',
                'code' => 'CNSHA',
                'type' => Location::TYPE_PORT,
                'address' => 'Yangshan Island, Hangzhou Bay',
                'city' => 'Shanghai',
                'country' => 'China',
                'latitude' => 30.6167000,
                'longitude' => 122.0667000,
            ],
            [
                'name' => 'Port of Los Angeles',
                'code' => 'USLAX',
                'type' => Location::TYPE_PORT,
                'address' => '425 S. Palos Verdes St, San Pedro',
                'city' => 'Los Angeles',
                'country' => 'United States',
                'latitude' => 33.7432000,
                'longitude' => -118.2673000,
            ],
            [
                'name' => 'Port of Hamburg',
                'code' => 'DEHAM',
                'type' => Location::TYPE_PORT,
                'address' => 'Neuer Wandrahm 4, 20457',
                'city' => 'Hamburg',
                'country' => 'Germany',
                'latitude' => 53.5353000,
                'longitude' => 9.9868000,
            ],
            [
                'name' => 'Soekarno-Hatta Cargo Terminal',
                'code' => 'IDCGK',
                'type' => Location::TYPE_AIRPORT,
                'address' => 'Building 510 Cargo Area, Cengkareng',
                'city' => 'Tangerang',
                'country' => 'Indonesia',
                'latitude' => -6.1275000,
                'longitude' => 106.6537000,
            ],
            [
                'name' => 'Changi Airfreight Centre',
                'code' => 'SINAC',
                'type' => Location::TYPE_AIRPORT,
                'address' => 'Airport Boulevard, Changi',
                'city' => 'Singapore',
                'country' => 'Singapore',
                'latitude' => 1.3644000,
                'longitude' => 103.9915000,
            ],
        ];

        foreach ($locations as $location) {
            Location::updateOrCreate(['code' => $location['code']], $location);
        }
    }
}

