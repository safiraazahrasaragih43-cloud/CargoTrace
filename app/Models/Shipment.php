<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Shipment extends Model
{
    use HasFactory, SoftDeletes;

    public const STATUS_CREATED = 'CREATED';
    public const STATUS_BOOKED = 'BOOKED';
    public const STATUS_PICKED_UP = 'PICKED_UP';
    public const STATUS_IN_TRANSIT = 'IN_TRANSIT';
    public const STATUS_OUT_FOR_DELIVERY = 'OUT_FOR_DELIVERY';
    public const STATUS_DELIVERED = 'DELIVERED';
    public const STATUS_EXCEPTION = 'EXCEPTION';
    public const STATUS_CANCELLED = 'CANCELLED';

    public const STATUSES = [
        self::STATUS_CREATED,
        self::STATUS_BOOKED,
        self::STATUS_PICKED_UP,
        self::STATUS_IN_TRANSIT,
        self::STATUS_OUT_FOR_DELIVERY,
        self::STATUS_DELIVERED,
        self::STATUS_EXCEPTION,
        self::STATUS_CANCELLED,
    ];

    public const TERMINAL_STATUSES = [
        self::STATUS_DELIVERED,
        self::STATUS_CANCELLED,
    ];

    /**
     * Allowed State Transitions:
     * Current status => Allowed next statuses
     */
    public const ALLOWED_TRANSITIONS = [
        self::STATUS_CREATED => [self::STATUS_BOOKED, self::STATUS_CANCELLED],
        self::STATUS_BOOKED => [self::STATUS_PICKED_UP, self::STATUS_CANCELLED],
        self::STATUS_PICKED_UP => [self::STATUS_IN_TRANSIT, self::STATUS_EXCEPTION],
        self::STATUS_IN_TRANSIT => [self::STATUS_OUT_FOR_DELIVERY, self::STATUS_EXCEPTION],
        self::STATUS_OUT_FOR_DELIVERY => [self::STATUS_DELIVERED, self::STATUS_EXCEPTION],
        self::STATUS_EXCEPTION => [self::STATUS_IN_TRANSIT, self::STATUS_OUT_FOR_DELIVERY, self::STATUS_CANCELLED],
        self::STATUS_DELIVERED => [],
        self::STATUS_CANCELLED => [],
    ];

    protected $fillable = [
        'tracking_number',
        'customer_id',
        'carrier_id',
        'origin_id',
        'destination_id',
        'current_location_id',
        'status',
        'departure_at',
        'expected_delivery_at',
        'estimated_delivery_at',
        'actual_delivery_at',
        'delay_hours',
        'is_delayed',
        'delay_reason',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'departure_at' => 'datetime',
            'expected_delivery_at' => 'datetime',
            'estimated_delivery_at' => 'datetime',
            'actual_delivery_at' => 'datetime',
            'delay_hours' => 'integer',
            'is_delayed' => 'boolean',
        ];
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function carrier(): BelongsTo
    {
        return $this->belongsTo(Carrier::class);
    }

    public function origin(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'origin_id');
    }

    public function destination(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'destination_id');
    }

    public function currentLocation(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'current_location_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(ShipmentItem::class);
    }

    public function trackingEvents(): HasMany
    {
        return $this->hasMany(TrackingEvent::class)->orderBy('event_time', 'asc');
    }

    public function latestTrackingEvent(): HasOne
    {
        return $this->hasOne(TrackingEvent::class)->latestOfMany('event_time');
    }

    public function exceptions(): HasMany
    {
        return $this->hasMany(ShipmentException::class)->orderBy('detected_at', 'desc');
    }

    public function openExceptions(): HasMany
    {
        return $this->hasMany(ShipmentException::class)
            ->where('status', '!=', ShipmentException::STATUS_RESOLVED)
            ->orderBy('detected_at', 'desc');
    }

    public function isTerminal(): bool
    {
        return in_array($this->status, self::TERMINAL_STATUSES, true);
    }

    public function canTransitionTo(string $targetStatus): bool
    {
        if ($this->isTerminal()) {
            return false;
        }

        if ($targetStatus === $this->status) {
            return true;
        }

        $allowed = self::ALLOWED_TRANSITIONS[$this->status] ?? [];

        return in_array($targetStatus, $allowed, true);
    }
}

