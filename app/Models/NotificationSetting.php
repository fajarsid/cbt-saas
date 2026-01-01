<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NotificationSetting extends Model
{
    protected $fillable = [
        'tenant_id',
        'fonnte_api_key',
        'fonnte_sender',
        'whatsapp_enabled',
        'email_enabled',
        'exam_reminder_enabled',
        'exam_reminder_hours',
        'result_notification_enabled',
    ];

    protected function casts(): array
    {
        return [
            'whatsapp_enabled' => 'boolean',
            'email_enabled' => 'boolean',
            'exam_reminder_enabled' => 'boolean',
            'result_notification_enabled' => 'boolean',
        ];
    }

    protected $hidden = [
        'fonnte_api_key',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }
}
