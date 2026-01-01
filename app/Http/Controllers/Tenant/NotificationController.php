<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class NotificationController extends Controller
{
    protected NotificationService $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * List all notifications for current user
     */
    public function index(Request $request): Response
    {
        $user = auth()->user();

        $notifications = Notification::where('notifiable_type', get_class($user))
            ->where('notifiable_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return Inertia::render('Tenant/Notifications/Index', [
            'notifications' => $notifications,
            'unreadCount' => $this->notificationService->getUnreadCount($user),
        ]);
    }

    /**
     * Mark notification as read
     */
    public function markAsRead(Notification $notification)
    {
        $notification->markAsRead();

        return back()->with('success', 'Notifikasi ditandai sudah dibaca.');
    }

    /**
     * Mark all notifications as read
     */
    public function markAllAsRead()
    {
        $user = auth()->user();
        $this->notificationService->markAllAsRead($user);

        return back()->with('success', 'Semua notifikasi ditandai sudah dibaca.');
    }

    /**
     * Delete notification
     */
    public function destroy(Notification $notification)
    {
        $notification->delete();

        return back()->with('success', 'Notifikasi dihapus.');
    }

    /**
     * Get unread count (for AJAX)
     */
    public function unreadCount()
    {
        $user = auth()->user();
        return response()->json([
            'count' => $this->notificationService->getUnreadCount($user),
        ]);
    }

    /**
     * Send announcement to all students
     */
    public function sendAnnouncement(Request $request)
    {
        $tenant = auth()->user()->tenant;

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'message' => 'required|string|max:1000',
            'channel' => 'required|in:in_app,whatsapp,both',
            'classroom_id' => 'nullable|exists:classrooms,id',
        ]);

        $students = $tenant->students();
        if ($validated['classroom_id']) {
            $students->where('classroom_id', $validated['classroom_id']);
        }

        $count = 0;
        foreach ($students->get() as $student) {
            if ($validated['channel'] === 'both') {
                $this->notificationService->notifyStudent($student, 'announcement', $validated['title'], $validated['message'], [], 'in_app');
                $this->notificationService->notifyStudent($student, 'announcement', $validated['title'], $validated['message'], [], 'whatsapp');
            } else {
                $this->notificationService->notifyStudent($student, 'announcement', $validated['title'], $validated['message'], [], $validated['channel']);
            }
            $count++;
        }

        return back()->with('success', "Pengumuman berhasil dikirim ke {$count} peserta.");
    }
}
