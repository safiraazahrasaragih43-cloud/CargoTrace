<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\AppNotification;
use App\Services\NotificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class NotificationWebController extends Controller
{
    public function __construct(
        protected NotificationService $notificationService
    ) {}

    public function index(Request $request): View
    {
        $notifications = $this->notificationService->getUserNotifications($request->user(), 15);
        $unreadCount = $this->notificationService->unreadCount($request->user());

        return view('notifications.index', [
            'notifications' => $notifications,
            'unreadCount' => $unreadCount,
        ]);
    }

    public function markRead(Request $request, AppNotification $notification): RedirectResponse
    {
        if ($notification->user_id !== $request->user()->id) {
            abort(403, 'Akses tidak diizinkan.');
        }

        $this->notificationService->markAsRead($notification, $request->user());

        return redirect()
            ->route('notifications.index')
            ->with('status', 'Notifikasi ditandai telah dibaca.');
    }

    public function markAllRead(Request $request): RedirectResponse
    {
        $this->notificationService->markAllAsRead($request->user());

        return redirect()
            ->route('notifications.index')
            ->with('status', 'Semua notifikasi ditandai telah dibaca.');
    }
}
