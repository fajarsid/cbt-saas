# CBT SaaS Platform - Complete Development Guide

> Computer Based Test (CBT) Software as a Service dengan Multi-Tenancy
> Stack: Laravel 12 + Vue 3 + Inertia.js + Tailwind CSS

---

## Table of Contents
1. [Architecture Overview](#1-architecture-overview)
2. [Database Schema](#2-database-schema)
3. [Module Details](#3-module-details)
4. [Implementation Order](#4-implementation-order)
5. [Route Structure](#5-route-structure)
6. [Code Examples](#6-code-examples)

---

## 1. Architecture Overview

```
┌─────────────────────────────────────────────────────────────┐
│                     SUPER ADMIN                              │
│  (Manage Tenants, Plans, Subscriptions, View All Data)      │
└─────────────────────────────────────────────────────────────┘
                              │
         ┌────────────────────┼────────────────────┐
         ▼                    ▼                    ▼
┌─────────────────┐  ┌─────────────────┐  ┌─────────────────┐
│    TENANT A     │  │    TENANT B     │  │    TENANT C     │
│  (Sekolah ABC)  │  │  (Bimbel XYZ)   │  │  (Kampus 123)   │
├─────────────────┤  ├─────────────────┤  ├─────────────────┤
│ - Admins        │  │ - Admins        │  │ - Admins        │
│ - Students      │  │ - Students      │  │ - Students      │
│ - Classrooms    │  │ - Classrooms    │  │ - Classrooms    │
│ - Lessons       │  │ - Lessons       │  │ - Lessons       │
│ - Exams         │  │ - Exams         │  │ - Exams         │
│ - Exam Sessions │  │ - Exam Sessions │  │ - Exam Sessions │
└─────────────────┘  └─────────────────┘  └─────────────────┘
```

### User Roles:
1. **Super Admin** - Pemilik platform, manage semua tenant
2. **Tenant Admin** - Admin per organisasi, manage data sendiri
3. **Student** - Peserta ujian, mengerjakan ujian

### Key Concepts:
- **Multi-tenancy**: Data terisolasi per tenant menggunakan `tenant_id`
- **Subscription-based**: Tenant bayar berdasarkan plan (Free, Basic, Premium)
- **Quota System**: Limit students & exams per plan

---

## 2. Database Schema

### 2.1 Tenants (Organisasi)
```sql
CREATE TABLE tenants (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    slug VARCHAR(255) UNIQUE NOT NULL,
    domain VARCHAR(255) NULL,
    email VARCHAR(255) NOT NULL,
    phone VARCHAR(20) NULL,
    website VARCHAR(255) NULL,
    logo VARCHAR(255) NULL,
    favicon VARCHAR(255) NULL,
    primary_color VARCHAR(7) DEFAULT '#1F2937',
    secondary_color VARCHAR(7) DEFAULT '#3B82F6',
    address TEXT NULL,
    timezone VARCHAR(50) DEFAULT 'Asia/Jakarta',
    date_format VARCHAR(20) DEFAULT 'd/m/Y',
    time_format VARCHAR(20) DEFAULT 'H:i',
    status ENUM('active', 'inactive', 'suspended') DEFAULT 'active',
    plan VARCHAR(50) DEFAULT 'free',
    current_plan_id BIGINT NULL,
    max_students INT DEFAULT 50,
    max_exams INT DEFAULT 10,
    trial_ends_at TIMESTAMP NULL,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,

    FOREIGN KEY (current_plan_id) REFERENCES plans(id)
);
```

### 2.2 Users (Admin per Tenant)
```sql
CREATE TABLE users (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    tenant_id BIGINT NULL,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('super_admin', 'admin') DEFAULT 'admin',
    is_active BOOLEAN DEFAULT TRUE,
    email_verified_at TIMESTAMP NULL,
    remember_token VARCHAR(100) NULL,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,

    FOREIGN KEY (tenant_id) REFERENCES tenants(id) ON DELETE CASCADE
);
```

### 2.3 Plans (Paket Langganan)
```sql
CREATE TABLE plans (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    slug VARCHAR(255) UNIQUE NOT NULL,
    description TEXT NULL,
    price DECIMAL(12,2) DEFAULT 0,
    billing_cycle ENUM('monthly', 'yearly') DEFAULT 'monthly',
    max_students INT DEFAULT 50,
    max_exams INT DEFAULT 10,
    max_users INT DEFAULT 5,
    whatsapp_enabled BOOLEAN DEFAULT FALSE,
    custom_branding BOOLEAN DEFAULT FALSE,
    export_enabled BOOLEAN DEFAULT FALSE,
    api_access BOOLEAN DEFAULT FALSE,
    features JSON NULL,
    is_active BOOLEAN DEFAULT TRUE,
    is_featured BOOLEAN DEFAULT FALSE,
    trial_days INT DEFAULT 14,
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);

-- Default Plans
INSERT INTO plans (name, slug, price, max_students, max_exams, max_users) VALUES
('Free', 'free', 0, 50, 10, 1),
('Basic', 'basic', 99000, 200, 50, 3),
('Premium', 'premium', 299000, 1000, 200, 10),
('Enterprise', 'enterprise', 999000, 10000, 1000, 50);
```

### 2.4 Subscriptions
```sql
CREATE TABLE subscriptions (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    tenant_id BIGINT NOT NULL,
    plan_id BIGINT NOT NULL,
    price DECIMAL(12,2) NULL,
    status ENUM('active', 'cancelled', 'expired', 'past_due') DEFAULT 'active',
    starts_at TIMESTAMP NOT NULL,
    ends_at TIMESTAMP NULL,
    trial_ends_at TIMESTAMP NULL,
    cancelled_at TIMESTAMP NULL,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,

    FOREIGN KEY (tenant_id) REFERENCES tenants(id) ON DELETE CASCADE,
    FOREIGN KEY (plan_id) REFERENCES plans(id),
    INDEX (tenant_id, status)
);
```

### 2.5 Invoices
```sql
CREATE TABLE invoices (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    tenant_id BIGINT NOT NULL,
    subscription_id BIGINT NULL,
    invoice_number VARCHAR(50) UNIQUE NOT NULL,
    status ENUM('pending', 'paid', 'overdue', 'cancelled') DEFAULT 'pending',
    subtotal DECIMAL(12,2) NOT NULL,
    tax DECIMAL(12,2) DEFAULT 0,
    total DECIMAL(12,2) NOT NULL,
    currency VARCHAR(3) DEFAULT 'IDR',
    notes TEXT NULL,
    due_date TIMESTAMP NOT NULL,
    paid_at TIMESTAMP NULL,
    payment_method VARCHAR(50) NULL,
    payment_reference VARCHAR(255) NULL,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,

    FOREIGN KEY (tenant_id) REFERENCES tenants(id) ON DELETE CASCADE,
    FOREIGN KEY (subscription_id) REFERENCES subscriptions(id) ON DELETE SET NULL,
    INDEX (tenant_id, status)
);
```

### 2.6 Lessons (Mata Pelajaran)
```sql
CREATE TABLE lessons (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    tenant_id BIGINT NOT NULL,
    name VARCHAR(255) NOT NULL,
    description TEXT NULL,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,

    FOREIGN KEY (tenant_id) REFERENCES tenants(id) ON DELETE CASCADE
);
```

### 2.7 Classrooms (Kelas)
```sql
CREATE TABLE classrooms (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    tenant_id BIGINT NOT NULL,
    name VARCHAR(255) NOT NULL,
    description TEXT NULL,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,

    FOREIGN KEY (tenant_id) REFERENCES tenants(id) ON DELETE CASCADE
);
```

### 2.8 Students (Peserta)
```sql
CREATE TABLE students (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    tenant_id BIGINT NOT NULL,
    classroom_id BIGINT NULL,
    nisn VARCHAR(20) NOT NULL,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NULL,
    phone VARCHAR(20) NULL,
    password VARCHAR(255) NOT NULL,
    gender ENUM('L', 'P') NOT NULL,
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP,
    updated_at TIMESTAMP,

    FOREIGN KEY (tenant_id) REFERENCES tenants(id) ON DELETE CASCADE,
    FOREIGN KEY (classroom_id) REFERENCES classrooms(id) ON DELETE SET NULL,
    UNIQUE KEY unique_nisn_tenant (tenant_id, nisn)
);
```

### 2.9 Exams (Ujian)
```sql
CREATE TABLE exams (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    tenant_id BIGINT NOT NULL,
    lesson_id BIGINT NOT NULL,
    title VARCHAR(255) NOT NULL,
    description TEXT NULL,
    duration INT NOT NULL, -- in minutes
    random_question BOOLEAN DEFAULT FALSE,
    random_answer BOOLEAN DEFAULT FALSE,
    show_answer BOOLEAN DEFAULT FALSE,
    show_result BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,

    FOREIGN KEY (tenant_id) REFERENCES tenants(id) ON DELETE CASCADE,
    FOREIGN KEY (lesson_id) REFERENCES lessons(id) ON DELETE CASCADE
);
```

### 2.10 Questions (Soal)
```sql
CREATE TABLE questions (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    exam_id BIGINT NOT NULL,
    question TEXT NOT NULL,
    option_a TEXT NOT NULL,
    option_b TEXT NOT NULL,
    option_c TEXT NULL,
    option_d TEXT NULL,
    option_e TEXT NULL,
    correct_answer ENUM('a', 'b', 'c', 'd', 'e') NOT NULL,
    points INT DEFAULT 1,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,

    FOREIGN KEY (exam_id) REFERENCES exams(id) ON DELETE CASCADE
);
```

### 2.11 Exam Sessions (Sesi Ujian)
```sql
CREATE TABLE exam_sessions (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    tenant_id BIGINT NOT NULL,
    exam_id BIGINT NOT NULL,
    title VARCHAR(255) NOT NULL,
    start_time TIMESTAMP NOT NULL,
    end_time TIMESTAMP NOT NULL,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,

    FOREIGN KEY (tenant_id) REFERENCES tenants(id) ON DELETE CASCADE,
    FOREIGN KEY (exam_id) REFERENCES exams(id) ON DELETE CASCADE
);
```

### 2.12 Exam Groups (Enrollment Peserta ke Sesi)
```sql
CREATE TABLE exam_groups (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    exam_session_id BIGINT NOT NULL,
    student_id BIGINT NOT NULL,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,

    FOREIGN KEY (exam_session_id) REFERENCES exam_sessions(id) ON DELETE CASCADE,
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
    UNIQUE KEY unique_enrollment (exam_session_id, student_id)
);
```

### 2.13 Grades (Nilai)
```sql
CREATE TABLE grades (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    exam_group_id BIGINT NOT NULL,
    exam_session_id BIGINT NOT NULL,
    student_id BIGINT NOT NULL,
    total_correct INT DEFAULT 0,
    grade DECIMAL(5,2) DEFAULT 0,
    start_time TIMESTAMP NULL,
    end_time TIMESTAMP NULL,
    duration INT NULL, -- in minutes
    is_finished BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,

    FOREIGN KEY (exam_group_id) REFERENCES exam_groups(id) ON DELETE CASCADE,
    FOREIGN KEY (exam_session_id) REFERENCES exam_sessions(id) ON DELETE CASCADE,
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE
);
```

### 2.14 Answers (Jawaban Peserta)
```sql
CREATE TABLE answers (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    grade_id BIGINT NOT NULL,
    question_id BIGINT NOT NULL,
    answer ENUM('a', 'b', 'c', 'd', 'e') NULL,
    is_correct BOOLEAN DEFAULT FALSE,
    answered_at TIMESTAMP NULL,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,

    FOREIGN KEY (grade_id) REFERENCES grades(id) ON DELETE CASCADE,
    FOREIGN KEY (question_id) REFERENCES questions(id) ON DELETE CASCADE
);
```

### 2.15 Notifications
```sql
CREATE TABLE notifications (
    id CHAR(36) PRIMARY KEY, -- UUID
    tenant_id BIGINT NULL,
    type VARCHAR(50) NOT NULL, -- exam_reminder, result, welcome, announcement
    notifiable_type VARCHAR(255) NOT NULL,
    notifiable_id BIGINT NOT NULL,
    title VARCHAR(255) NOT NULL,
    message TEXT NOT NULL,
    data JSON NULL,
    channel ENUM('in_app', 'whatsapp', 'email') DEFAULT 'in_app',
    whatsapp_status VARCHAR(20) NULL, -- pending, sent, delivered, failed
    whatsapp_message_id VARCHAR(255) NULL,
    read_at TIMESTAMP NULL,
    sent_at TIMESTAMP NULL,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,

    FOREIGN KEY (tenant_id) REFERENCES tenants(id) ON DELETE CASCADE,
    INDEX (notifiable_type, notifiable_id, read_at),
    INDEX (tenant_id, created_at)
);
```

### 2.16 Notification Settings
```sql
CREATE TABLE notification_settings (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    tenant_id BIGINT NOT NULL UNIQUE,
    fonnte_api_key VARCHAR(255) NULL,
    fonnte_sender VARCHAR(20) NULL,
    fonnte_device_status VARCHAR(20) NULL,
    fonnte_last_check TIMESTAMP NULL,
    whatsapp_enabled BOOLEAN DEFAULT FALSE,
    email_enabled BOOLEAN DEFAULT TRUE,
    exam_reminder_enabled BOOLEAN DEFAULT TRUE,
    exam_reminder_hours INT DEFAULT 24,
    result_notification_enabled BOOLEAN DEFAULT TRUE,
    welcome_notification_enabled BOOLEAN DEFAULT TRUE,
    message_templates JSON NULL,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,

    FOREIGN KEY (tenant_id) REFERENCES tenants(id) ON DELETE CASCADE
);
```

### 2.17 Activity Logs
```sql
CREATE TABLE activity_logs (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    tenant_id BIGINT NULL,
    log_type VARCHAR(50) NOT NULL, -- create, update, delete, login, export
    description TEXT NOT NULL,
    subject_type VARCHAR(255) NULL,
    subject_id BIGINT NULL,
    causer_type VARCHAR(255) NULL,
    causer_id BIGINT NULL,
    properties JSON NULL,
    ip_address VARCHAR(45) NULL,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,

    FOREIGN KEY (tenant_id) REFERENCES tenants(id) ON DELETE CASCADE,
    INDEX (tenant_id, log_type, created_at)
);
```

---

## 3. Module Details

### 3.1 Multi-Tenancy Foundation

**Files to Create:**
```
app/
├── Models/Tenant.php
├── Traits/BelongsToTenant.php
├── Scopes/TenantScope.php
└── Http/Middleware/TenantMiddleware.php
```

**BelongsToTenant Trait:**
```php
<?php

namespace App\Traits;

use App\Models\Tenant;
use App\Scopes\TenantScope;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait BelongsToTenant
{
    protected static function bootBelongsToTenant(): void
    {
        static::addGlobalScope(new TenantScope);

        static::creating(function ($model) {
            if (auth()->check() && auth()->user()->tenant_id) {
                $model->tenant_id = auth()->user()->tenant_id;
            }
        });
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }
}
```

**TenantScope:**
```php
<?php

namespace App\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class TenantScope implements Scope
{
    public function apply(Builder $builder, Model $model): void
    {
        if (auth()->check() && auth()->user()->tenant_id) {
            $builder->where($model->getTable() . '.tenant_id', auth()->user()->tenant_id);
        }
    }
}
```

**Usage in Models:**
```php
class Student extends Model
{
    use BelongsToTenant;
    // ...
}
```

---

### 3.2 Plans & Subscription

**Subscription Flow:**
1. Super Admin creates Plans
2. Tenant registers (gets Free plan or Trial)
3. Tenant upgrades to paid plan
4. System creates Subscription + Invoice
5. Tenant pays invoice
6. System updates tenant quotas

**Plan Features Matrix:**
| Feature | Free | Basic | Premium | Enterprise |
|---------|------|-------|---------|------------|
| Max Students | 50 | 200 | 1,000 | 10,000 |
| Max Exams | 10 | 50 | 200 | 1,000 |
| Max Admins | 1 | 3 | 10 | 50 |
| WhatsApp Notif | ❌ | ✅ | ✅ | ✅ |
| Custom Branding | ❌ | ❌ | ✅ | ✅ |
| Export Data | ❌ | ✅ | ✅ | ✅ |
| API Access | ❌ | ❌ | ❌ | ✅ |

---

### 3.3 Notification System + WhatsApp (Fonnte)

**Fonnte Integration:**
- API URL: `https://api.fonnte.com/send`
- Auth: Header `Authorization: {API_KEY}`

**Message Templates:**
```json
{
    "exam_reminder": {
        "title": "Pengingat Ujian",
        "message": "Halo {nama_siswa}!\n\nAnda memiliki ujian *{nama_ujian}* yang akan dimulai pada:\n📅 {tanggal_ujian}\n⏰ {waktu_ujian}\n\nSilakan persiapkan diri Anda.\n\n_{nama_organisasi}_"
    },
    "exam_result": {
        "title": "Hasil Ujian",
        "message": "Halo {nama_siswa}!\n\nHasil ujian *{nama_ujian}* telah tersedia.\n\n📊 *Nilai: {nilai}*\n\n_{nama_organisasi}_"
    },
    "welcome": {
        "title": "Selamat Datang",
        "message": "Halo {nama_siswa}!\n\nSelamat datang di *{nama_organisasi}*.\n\n📌 NISN: {nisn}\n📌 Kelas: {kelas}\n\n_{nama_organisasi}_"
    },
    "announcement": {
        "title": "Pengumuman",
        "message": "*{judul_pengumuman}*\n\n{isi_pengumuman}\n\n_{nama_organisasi}_"
    },
    "exam_start": {
        "title": "Ujian Dimulai",
        "message": "Halo {nama_siswa}!\n\nUjian *{nama_ujian}* telah dimulai.\n\n⏰ Durasi: {durasi} menit\n📝 Soal: {jumlah_soal}\n\nSilakan login sekarang.\n\n_{nama_organisasi}_"
    }
}
```

**Template Variables:**
| Variable | Description |
|----------|-------------|
| {nama_siswa} | Nama student |
| {nama_ujian} | Nama exam |
| {tanggal_ujian} | Tanggal exam (d F Y) |
| {waktu_ujian} | Waktu mulai |
| {durasi} | Durasi dalam menit |
| {jumlah_soal} | Jumlah soal |
| {nilai} | Nilai hasil ujian |
| {nisn} | NISN student |
| {kelas} | Nama classroom |
| {nama_organisasi} | Nama tenant |

---

### 3.4 Quota Enforcement

**Middleware:**
```php
// Usage in routes
Route::post('/students', [StudentController::class, 'store'])
    ->middleware('quota:students');

Route::post('/exams', [ExamController::class, 'store'])
    ->middleware('quota:exams');
```

**Check in Tenant Model:**
```php
public function canAddMoreStudents(): bool
{
    return $this->students()->count() < $this->max_students;
}

public function canAddMoreExams(): bool
{
    return $this->exams()->count() < $this->max_exams;
}
```

---

### 3.5 Impersonation (Login as Tenant)

**Flow:**
1. Super Admin clicks "Login as Tenant"
2. Store original admin ID in session
3. Set new tenant context
4. Redirect to tenant dashboard
5. Show banner "You are viewing as [Tenant Name]"
6. Click "Back to Admin" to restore

```php
// Store impersonation
session(['impersonating' => [
    'original_user_id' => auth()->id(),
    'tenant_id' => $tenant->id,
    'tenant_name' => $tenant->name,
]]);

// Check impersonation
$isImpersonating = session()->has('impersonating');
```

---

### 3.6 Data Export

**Export Types:**
1. **Students CSV** - All students with classroom
2. **Exam Results CSV** - Per exam, all student grades
3. **Full Backup ZIP** - students.csv + classrooms.csv + exams.csv

**CSV Format (UTF-8 BOM for Excel):**
```php
// Add BOM for Excel UTF-8
fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));
```

---

## 4. Implementation Order

### Phase 1: Foundation (Week 1)
```
1. Install Laravel 12 + Vue Starter Kit
2. Setup database & migrations
3. Implement Multi-tenancy (Tenant model, trait, scope, middleware)
4. Authentication (login, register)
5. Tenant Registration
```

### Phase 2: Core CBT (Week 2-3)
```
6. Lessons CRUD
7. Classrooms CRUD
8. Students CRUD + Import CSV
9. Exams CRUD + Questions + Import CSV
10. Exam Sessions + Enrollment
11. Student Exam Taking (start, answer, submit)
12. Grades calculation
13. Reports & Export
```

### Phase 3: SaaS Features (Week 4)
```
14. Plans CRUD
15. Subscriptions management
16. Invoices
17. Quota Enforcement middleware
18. Tenant Management (for Super Admin)
19. Impersonation
```

### Phase 4: Advanced (Week 5)
```
20. Notification System
21. WhatsApp Integration (Fonnte)
22. Message Templates
23. Activity Logging
24. Tenant Branding/Settings
25. Data Export/Backup
```

---

## 5. Route Structure

```php
// ================================
// PUBLIC ROUTES
// ================================
Route::get('/', [LandingController::class, 'index']);
Route::get('/pricing', [LandingController::class, 'pricing']);

// Tenant Registration
Route::get('/register/tenant', [TenantRegisterController::class, 'create']);
Route::post('/register/tenant', [TenantRegisterController::class, 'store']);

// Student Login
Route::post('/student/login', [StudentLoginController::class, 'login']);

// ================================
// ADMIN ROUTES (authenticated)
// ================================
Route::prefix('admin')->middleware(['auth'])->group(function () {

    // Dashboard
    Route::get('/dashboard', DashboardController::class);

    // Super Admin Only
    Route::middleware(['super_admin'])->group(function () {
        // Tenants
        Route::resource('tenants', TenantController::class);
        Route::post('tenants/{tenant}/impersonate', [TenantController::class, 'impersonate']);
        Route::post('stop-impersonate', [TenantController::class, 'stopImpersonate']);

        // Plans
        Route::resource('plans', PlanController::class);

        // Subscriptions
        Route::resource('subscriptions', SubscriptionController::class);
        Route::post('subscriptions/{subscription}/cancel', [SubscriptionController::class, 'cancel']);
        Route::post('subscriptions/{subscription}/renew', [SubscriptionController::class, 'renew']);

        // Invoices
        Route::get('invoices', [InvoiceController::class, 'index']);
        Route::get('invoices/{invoice}', [InvoiceController::class, 'show']);
        Route::post('invoices/{invoice}/mark-paid', [InvoiceController::class, 'markAsPaid']);

        // Activity Logs
        Route::get('activity-logs', [ActivityLogController::class, 'index']);
        Route::get('activity-logs/export', [ActivityLogController::class, 'export']);
    });

    // Tenant Admin
    // Lessons
    Route::resource('lessons', LessonController::class);

    // Classrooms
    Route::resource('classrooms', ClassroomController::class);

    // Students
    Route::resource('students', StudentController::class);
    Route::get('students/import', [StudentController::class, 'import']);
    Route::post('students/import', [StudentController::class, 'storeImport']);

    // Exams
    Route::resource('exams', ExamController::class);
    Route::get('exams/{exam}/questions/create', [ExamController::class, 'createQuestion']);
    Route::post('exams/{exam}/questions', [ExamController::class, 'storeQuestion']);
    Route::get('exams/{exam}/questions/{question}/edit', [ExamController::class, 'editQuestion']);
    Route::put('exams/{exam}/questions/{question}', [ExamController::class, 'updateQuestion']);
    Route::delete('exams/{exam}/questions/{question}', [ExamController::class, 'destroyQuestion']);
    Route::get('exams/{exam}/questions/import', [ExamController::class, 'importQuestions']);
    Route::post('exams/{exam}/questions/import', [ExamController::class, 'storeImportQuestions']);

    // Exam Sessions
    Route::resource('exam-sessions', ExamSessionController::class);
    Route::get('exam-sessions/{exam_session}/enroll', [ExamSessionController::class, 'createEnroll']);
    Route::post('exam-sessions/{exam_session}/enroll', [ExamSessionController::class, 'storeEnroll']);
    Route::delete('exam-sessions/{exam_session}/enroll/{student}', [ExamSessionController::class, 'destroyEnroll']);

    // Reports
    Route::get('reports', [ReportController::class, 'index']);
    Route::get('reports/export', [ReportController::class, 'export']);

    // Users
    Route::resource('users', UserController::class);

    // Settings
    Route::get('settings', [SettingsController::class, 'index']);
    Route::post('settings/general', [SettingsController::class, 'updateGeneral']);
    Route::post('settings/branding', [SettingsController::class, 'updateBranding']);

    // Notification Settings
    Route::get('notification-settings', [NotificationSettingsController::class, 'index']);
    Route::post('notification-settings/fonnte', [NotificationSettingsController::class, 'updateFonnte']);
    Route::post('notification-settings/preferences', [NotificationSettingsController::class, 'updatePreferences']);
    Route::post('notification-settings/templates', [NotificationSettingsController::class, 'updateTemplates']);
    Route::post('notification-settings/test-connection', [NotificationSettingsController::class, 'testConnection']);
    Route::post('notification-settings/send-test', [NotificationSettingsController::class, 'sendTestMessage']);

    // Export
    Route::get('export', [ExportController::class, 'index']);
    Route::get('export/students', [ExportController::class, 'exportStudents']);
    Route::get('export/exam-results/{exam}', [ExportController::class, 'exportExamResults']);
    Route::get('export/backup', [ExportController::class, 'exportBackup']);

    // Notifications
    Route::get('notifications', [NotificationController::class, 'index']);
    Route::post('notifications/{notification}/read', [NotificationController::class, 'markAsRead']);
    Route::post('notifications/read-all', [NotificationController::class, 'markAllAsRead']);
});

// ================================
// STUDENT ROUTES
// ================================
Route::prefix('student')->middleware(['student'])->group(function () {
    Route::get('/dashboard', [StudentDashboardController::class, 'index']);
    Route::get('/exam/{id}/confirm', [StudentExamController::class, 'confirm']);
    Route::post('/exam/{id}/start', [StudentExamController::class, 'start']);
    Route::get('/exam/{id}/question/{page}', [StudentExamController::class, 'show']);
    Route::post('/exam/answer', [StudentExamController::class, 'answer']);
    Route::post('/exam/{id}/finish', [StudentExamController::class, 'finish']);
    Route::get('/exam/{id}/result', [StudentExamController::class, 'result']);
});
```

---

## 6. Code Examples

### 6.1 Tenant Model
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Tenant extends Model
{
    protected $fillable = [
        'name', 'slug', 'domain', 'email', 'phone', 'website',
        'logo', 'favicon', 'primary_color', 'secondary_color',
        'address', 'timezone', 'date_format', 'time_format',
        'status', 'plan', 'current_plan_id', 'max_students',
        'max_exams', 'trial_ends_at',
    ];

    protected $casts = [
        'trial_ends_at' => 'datetime',
    ];

    // Relationships
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

    public function notificationSetting(): HasOne
    {
        return $this->hasOne(NotificationSetting::class);
    }

    // Helpers
    public function canAddMoreStudents(): bool
    {
        return $this->students()->count() < $this->max_students;
    }

    public function canAddMoreExams(): bool
    {
        return $this->exams()->count() < $this->max_exams;
    }

    public function getStudentsUsagePercentage(): int
    {
        if ($this->max_students <= 0) return 0;
        return (int) round(($this->students()->count() / $this->max_students) * 100);
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function isOnTrial(): bool
    {
        return $this->trial_ends_at && $this->trial_ends_at->isFuture();
    }
}
```

### 6.2 FonnteService
```php
<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FonnteService
{
    protected string $baseUrl = 'https://api.fonnte.com';

    public function send(string $phone, string $message, ?string $apiKey = null): array
    {
        if (!$apiKey) {
            return ['success' => false, 'message' => 'API key tidak tersedia'];
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => $apiKey,
            ])->post("{$this->baseUrl}/send", [
                'target' => $this->formatPhone($phone),
                'message' => $message,
                'countryCode' => '62',
            ]);

            $data = $response->json();

            if ($response->successful() && ($data['status'] ?? false)) {
                return [
                    'success' => true,
                    'message_id' => $data['id'] ?? null,
                    'message' => 'Pesan berhasil dikirim',
                ];
            }

            return [
                'success' => false,
                'message' => $data['reason'] ?? 'Gagal mengirim pesan',
            ];
        } catch (\Exception $e) {
            Log::error('Fonnte error: ' . $e->getMessage());
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    public function getDeviceStatus(?string $apiKey = null): array
    {
        if (!$apiKey) {
            return ['success' => false, 'message' => 'API key tidak tersedia'];
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => $apiKey,
            ])->post("{$this->baseUrl}/device");

            return [
                'success' => $response->successful(),
                'data' => $response->json(),
            ];
        } catch (\Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    protected function formatPhone(string $phone): string
    {
        $phone = preg_replace('/[^0-9]/', '', $phone);

        if (str_starts_with($phone, '0')) {
            $phone = '62' . substr($phone, 1);
        }

        if (!str_starts_with($phone, '62')) {
            $phone = '62' . $phone;
        }

        return $phone;
    }
}
```

### 6.3 EnforceQuota Middleware
```php
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnforceQuota
{
    public function handle(Request $request, Closure $next, string $type = null)
    {
        $tenant = $request->user()?->tenant;

        if (!$tenant) {
            return $next($request);
        }

        if ($type === 'students' && !$tenant->canAddMoreStudents()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Kuota peserta habis. Upgrade paket Anda.',
                    'quota_exceeded' => true,
                    'type' => 'students',
                ], 403);
            }
            return back()->with('error', 'Kuota peserta habis.');
        }

        if ($type === 'exams' && !$tenant->canAddMoreExams()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Kuota ujian habis. Upgrade paket Anda.',
                    'quota_exceeded' => true,
                    'type' => 'exams',
                ], 403);
            }
            return back()->with('error', 'Kuota ujian habis.');
        }

        return $next($request);
    }
}
```

---

## Quick Start Commands

```bash
# Create new Laravel project with Vue starter kit
laravel new cbt-saas
cd cbt-saas
php artisan breeze:install vue

# Or with composer
composer create-project laravel/laravel cbt-saas
cd cbt-saas
composer require laravel/breeze --dev
php artisan breeze:install vue

# Install dependencies
npm install
npm run dev

# Create database & run migrations
php artisan migrate

# Create storage link
php artisan storage:link

# Generate app key
php artisan key:generate
```

---

## Notes

1. **Selalu gunakan `BelongsToTenant` trait** untuk model yang perlu isolasi data
2. **Test impersonation** dengan user super_admin
3. **Fonnte API** perlu device WhatsApp yang sudah terhubung
4. **Backup database** sebelum production
5. **Set timezone** di `config/app.php` ke `Asia/Jakarta`

---

*Guide ini dibuat untuk referensi development. Copy ke project baru dan sesuaikan dengan kebutuhan.*
