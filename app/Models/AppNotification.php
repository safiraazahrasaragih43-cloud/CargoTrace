<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AppNotification extends Model
{
    use HasFactory;

    protected $table = 'notifications';

    public const TYPE_TRACKING_UPDATE = 'TRACKING_UPDATE';
    public const TYPE_ETA_DELAY = 'ETA_DELAY';
    public const TYPE_EXCEPTION_ALERT = 'EXCEPTION_ALERT';
    public const TYPE_EXCEPTION_RESOLVED = 'EXCEPTION_RESOLVED';

    protected $fillable = [
        'user_id',
        'shipment_id',
        'type',
        'title',
        'message',
        'read_at',
    ];

    protected function casts(): array
    {
        return [
            'read_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function shipment(): BelongsTo
    {
        return $this->belongsTo(Shipment::class);
    }

    public function isRead(): bool
    {
        return $this->read_at !== null;
    }
}

