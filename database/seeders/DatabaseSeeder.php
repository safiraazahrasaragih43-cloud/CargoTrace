<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Core Users (Admin, Operator, Customer)
        User::updateOrCreate(
            ['email' => 'admin@cargotrace.com'],
            [
                'name' => 'System Administrator',
                'password' => Hash::make('password'),
                'role' => User::ROLE_ADMIN,
                'phone' => '+1-555-0100',
                'company_name' => 'CargoTrace Global HQ',
                'email_verified_at' => now(),
            ]
        );

        User::updateOrCreate(
            ['email' => 'operator@cargotrace.com'],
            [
                'name' => 'Alex Logistics Operator',
                'password' => Hash::make('password'),
                'role' => User::ROLE_OPERATOR,
                'phone' => '+1-555-0101',
                'company_name' => 'Pacific Freight Operations Hub',
                'email_verified_at' => now(),
            ]
        );

        User::updateOrCreate(
            ['email' => 'customer@cargotrace.com'],
            [
                'name' => 'Apex Retail Customer',
                'password' => Hash::make('password'),
                'role' => User::ROLE_CUSTOMER,
                'phone' => '+1-555-0102',
                'company_name' => 'Apex Retailers Global Ltd.',
                'email_verified_at' => now(),
            ]
        );

        // 2. Base Entities (Carriers, Locations, Shipments, Tracking)
        $this->call([
            CarrierSeeder::class,
            LocationSeeder::class,
            ShipmentSeeder::class,
            TrackingEventSeeder::class,
        ]);
    }
}
