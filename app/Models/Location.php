<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Location extends Model
{
    use HasFactory;

    public const TYPE_PORT = 'PORT';
    public const TYPE_AIRPORT = 'AIRPORT';
    public const TYPE_WAREHOUSE = 'WAREHOUSE';
    public const TYPE_DISTRIBUTION_CENTER = 'DISTRIBUTION_CENTER';
    public const TYPE_CUSTOMS = 'CUSTOMS';
    public const TYPE_CUSTOMER_ADDRESS = 'CUSTOMER_ADDRESS';

    public const TYPES = [
        self::TYPE_PORT,
        self::TYPE_AIRPORT,
        self::TYPE_WAREHOUSE,
        self::TYPE_DISTRIBUTION_CENTER,
        self::TYPE_CUSTOMS,
        self::TYPE_CUSTOMER_ADDRESS,
    ];

    protected $fillable = [
        'name',
        'code',
        'type',
        'address',
        'city',
        'country',
        'latitude',
        'longitude',
    ];

    protected function casts(): array
    {
        return [
            'latitude' => 'float',
            'longitude' => 'float',
        ];
    }

    public function originShipments(): HasMany
    {
        return $this->hasMany(Shipment::class, 'origin_id');
    }

    public function destinationShipments(): HasMany
    {
        return $this->hasMany(Shipment::class, 'destination_id');
    }

    public function currentShipments(): HasMany
    {
        return $this->hasMany(Shipment::class, 'current_location_id');
    }
}
