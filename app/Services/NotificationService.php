<?php

namespace App\Services;

use App\Models\AppNotification;
use App\Models\Shipment;
use App\Models\User;
use App\Traits\SanitizesQueriesTrait;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Validation\ValidationException;

class NotificationService
{
    use SanitizesQueriesTrait;

    /**
     * Send an in-app notification to a specific user.
     */
    public function sendToUser(User $user, ?Shipment $shipment, string $type, string $title, string $message): AppNotification
    {
        return AppNotification::create([
            'user_id' => $user->id,
            'shipment_id' => $shipment?->id,
            'type' => $type,
            'title' => $title,
            'message' => $message,
        ]);
    }

    /**
     * Notify shipment customer and optionally staff (admins/operators) about operational updates.
     */
    public function notifyShipmentStakeholders(
        Shipment $shipment,
        string $type,
        string $title,
        string $message,
        bool $includeStaff = false
    ): void {
        $recipientIds = collect();

        if ($shipment->customer_id) {
            $recipientIds->push($shipment->customer_id);
        }

        if ($includeStaff) {
            $staffIds = User::whereIn('role', [User::ROLE_ADMIN, User::ROLE_OPERATOR])->pluck('id');
            $recipientIds = $recipientIds->merge($staffIds);
        }

        foreach ($recipientIds->unique() as $userId) {
            AppNotification::create([
                'user_id' => $userId,
                'shipment_id' => $shipment->id,
                'type' => $type,
                'title' => $title,
                'message' => $message,
            ]);
        }
    }

    /**
     * Get paginated notifications for a user.
     */
    public function getUserNotifications(User $user, int $perPage = 15): LengthAwarePaginator
    {
        return AppNotification::with('shipment')
            ->where('user_id', $user->id)
            ->latest()
            ->paginate($this->clampPerPage($perPage));
    }

    /**
     * Get unread count for badge indicators.
     */
    public function unreadCount(User $user): int
    {
        return AppNotification::where('user_id', $user->id)
            ->whereNull('read_at')
            ->count();
    }

    /**
     * Mark a single notification as read (owned by user).
     */
    public function markAsRead(AppNotification $notification, User $user): AppNotification
    {
        if ($notification->user_id !== $user->id) {
            throw ValidationException::withMessages([
                'notification' => ['Unauthorized to mark this notification as read.'],
            ]);
        }

        if (! $notification->read_at) {
            $notification->update(['read_at' => now()]);
        }

        return $notification->fresh('shipment');
    }

    /**
     * Mark all unread notifications for the user as read.
     */
    public function markAllAsRead(User $user): int
    {
        return AppNotification::where('user_id', $user->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);
    }
}

