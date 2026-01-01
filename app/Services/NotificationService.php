<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\Student;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Support\Str;

class NotificationService
{
    protected FonnteService $fonnteService;

    public function __construct(FonnteService $fonnteService)
    {
        $this->fonnteService = $fonnteService;
    }

    /**
     * Create notification for a user
     */
    public function notifyUser(User $user, string $type, string $title, string $message, array $data = [], string $channel = 'in_app'): Notification
    {
        return $this->createNotification($user->tenant_id, $user, $type, $title, $message, $data, $channel);
    }

    /**
     * Create notification for a student
     */
    public function notifyStudent(Student $student, string $type, string $title, string $message, array $data = [], string $channel = 'in_app'): Notification
    {
        return $this->createNotification($student->tenant_id, $student, $type, $title, $message, $data, $channel);
    }

    /**
     * Create notification for tenant (broadcast to all users)
     */
    public function notifyTenant(Tenant $tenant, string $type, string $title, string $message, array $data = [], string $channel = 'in_app'): array
    {
        $notifications = [];

        foreach ($tenant->users as $user) {
            $notifications[] = $this->notifyUser($user, $type, $title, $message, $data, $channel);
        }

        return $notifications;
    }

    /**
     * Create notification for students in a classroom
     */
    public function notifyClassroom(int $tenantId, int $classroomId, string $type, string $title, string $message, array $data = [], string $channel = 'in_app'): array
    {
        $students = Student::where('tenant_id', $tenantId)
            ->where('classroom_id', $classroomId)
            ->get();

        $notifications = [];
        foreach ($students as $student) {
            $notifications[] = $this->notifyStudent($student, $type, $title, $message, $data, $channel);
        }

        return $notifications;
    }

    /**
     * Send exam reminder
     */
    public function sendExamReminder(int $tenantId, int $examSessionId, array $studentIds): array
    {
        $students = Student::where('tenant_id', $tenantId)
            ->whereIn('id', $studentIds)
            ->get();

        $notifications = [];
        foreach ($students as $student) {
            $notifications[] = $this->notifyStudent(
                $student,
                'exam_reminder',
                'Pengingat Ujian',
                'Anda memiliki ujian yang akan segera dimulai. Silakan persiapkan diri Anda.',
                ['exam_session_id' => $examSessionId],
                'whatsapp'
            );
        }

        return $notifications;
    }

    /**
     * Send welcome notification
     */
    public function sendWelcomeNotification(Student $student): Notification
    {
        $tenant = $student->tenant;
        return $this->notifyStudent(
            $student,
            'welcome',
            'Selamat Datang!',
            "Selamat datang di {$tenant->name}. Akun Anda telah aktif dan siap digunakan.",
            [],
            'in_app'
        );
    }

    /**
     * Send exam result notification
     */
    public function sendExamResultNotification(Student $student, array $resultData): Notification
    {
        return $this->notifyStudent(
            $student,
            'exam_result',
            'Hasil Ujian',
            "Hasil ujian Anda telah tersedia. Nilai: {$resultData['score']}",
            $resultData,
            'whatsapp'
        );
    }

    /**
     * Create notification and optionally send via WhatsApp
     */
    protected function createNotification(?int $tenantId, $notifiable, string $type, string $title, string $message, array $data = [], string $channel = 'in_app'): Notification
    {
        $notification = Notification::create([
            'id' => Str::uuid(),
            'tenant_id' => $tenantId,
            'type' => $type,
            'notifiable_type' => get_class($notifiable),
            'notifiable_id' => $notifiable->id,
            'title' => $title,
            'message' => $message,
            'data' => $data,
            'channel' => $channel,
        ]);

        // Send via WhatsApp if channel is whatsapp
        if ($channel === 'whatsapp') {
            $this->fonnteService->sendNotification($notification);
        }

        return $notification;
    }

    /**
     * Mark notification as read
     */
    public function markAsRead(Notification $notification): void
    {
        $notification->markAsRead();
    }

    /**
     * Mark all notifications as read for a notifiable
     */
    public function markAllAsRead($notifiable): void
    {
        Notification::where('notifiable_type', get_class($notifiable))
            ->where('notifiable_id', $notifiable->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);
    }

    /**
     * Get unread count for notifiable
     */
    public function getUnreadCount($notifiable): int
    {
        return Notification::where('notifiable_type', get_class($notifiable))
            ->where('notifiable_id', $notifiable->id)
            ->whereNull('read_at')
            ->count();
    }
}
