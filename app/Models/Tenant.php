<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Str;

class Tenant extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'domain',
        'email',
        'phone',
        'website',
        'logo',
        'favicon',
        'primary_color',
        'secondary_color',
        'address',
        'timezone',
        'date_format',
        'time_format',
        'status',
        'plan',
        'current_plan_id',
        'max_students',
        'max_exams',
        'trial_ends_at',
    ];

    protected function casts(): array
    {
        return [
            'trial_ends_at' => 'datetime',
        ];
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($tenant) {
            if (empty($tenant->slug)) {
                $tenant->slug = Str::slug($tenant->name);
            }
        });
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function students(): HasMany
    {
        return $this->hasMany(Student::class);
    }

    public function classrooms(): HasMany
    {
        return $this->hasMany(Classroom::class);
    }

    public function lessons(): HasMany
    {
        return $this->hasMany(Lesson::class);
    }

    public function exams(): HasMany
    {
        return $this->hasMany(Exam::class);
    }

    public function examSessions(): HasMany
    {
        return $this->hasMany(ExamSession::class);
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function isOnTrial(): bool
    {
        return $this->trial_ends_at && $this->trial_ends_at->isFuture();
    }

    public function canAddMoreStudents(): bool
    {
        return $this->students()->count() < $this->max_students;
    }

    public function canAddMoreExams(): bool
    {
        return $this->exams()->count() < $this->max_exams;
    }

    // New relationships for SaaS features

    public function currentPlan(): BelongsTo
    {
        return $this->belongsTo(Plan::class, 'current_plan_id');
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }

    public function activeSubscription(): HasOne
    {
        return $this->hasOne(Subscription::class)->where('status', 'active');
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    public function activityLogs(): HasMany
    {
        return $this->hasMany(ActivityLog::class);
    }

    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class);
    }

    public function notificationSetting(): HasOne
    {
        return $this->hasOne(NotificationSetting::class);
    }

    // Helper methods for quota

    public function getStudentsUsagePercentage(): int
    {
        if ($this->max_students <= 0) return 0;
        return (int) round(($this->students()->count() / $this->max_students) * 100);
    }

    public function getExamsUsagePercentage(): int
    {
        if ($this->max_exams <= 0) return 0;
        return (int) round(($this->exams()->count() / $this->max_exams) * 100);
    }

    public function getRemainingStudents(): int
    {
        return max(0, $this->max_students - $this->students()->count());
    }

    public function getRemainingExams(): int
    {
        return max(0, $this->max_exams - $this->exams()->count());
    }

    // Branding helpers

    public function getPrimaryColorAttribute($value): string
    {
        return $value ?? '#1F2937';
    }

    public function getSecondaryColorAttribute($value): string
    {
        return $value ?? '#3B82F6';
    }

    public function getTimezoneAttribute($value): string
    {
        return $value ?? 'Asia/Jakarta';
    }
}
