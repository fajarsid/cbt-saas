<?php

use App\Http\Controllers\Auth\TenantRegistrationController;
use Illuminate\Support\Facades\Route;

// Tenant Registration Routes (guest only)
Route::middleware('guest')->group(function () {
    Route::get('/register/tenant', [TenantRegistrationController::class, 'create'])->name('tenant.register');
    Route::post('/register/tenant', [TenantRegistrationController::class, 'store'])->name('tenant.register.store');
});

//prefix "admin"
Route::prefix('admin')->group(function() {

    //middleware "auth"
    Route::group(['middleware' => ['auth']], function () {

        //route dashboard
        Route::get('/dashboard', App\Http\Controllers\Admin\DashboardController::class)->name('admin.dashboard');

        //route resource tenants (organisasi)
        Route::resource('/tenants', \App\Http\Controllers\Admin\TenantController::class, ['as' => 'admin']);

        //tenant impersonation routes
        Route::post('/tenants/{tenant}/impersonate', [\App\Http\Controllers\Admin\TenantController::class, 'impersonate'])->name('admin.tenants.impersonate');
        Route::post('/stop-impersonate', [\App\Http\Controllers\Admin\TenantController::class, 'stopImpersonate'])->name('admin.stop-impersonate');

        //route resource plans (paket langganan)
        Route::resource('/plans', \App\Http\Controllers\Admin\PlanController::class, ['as' => 'admin']);

        //route resource subscriptions (langganan)
        Route::resource('/subscriptions', \App\Http\Controllers\Admin\SubscriptionController::class, ['as' => 'admin']);
        Route::post('/subscriptions/{subscription}/cancel', [\App\Http\Controllers\Admin\SubscriptionController::class, 'cancel'])->name('admin.subscriptions.cancel');
        Route::post('/subscriptions/{subscription}/renew', [\App\Http\Controllers\Admin\SubscriptionController::class, 'renew'])->name('admin.subscriptions.renew');

        //route resource invoices
        Route::resource('/invoices', \App\Http\Controllers\Admin\InvoiceController::class, ['as' => 'admin'])->only(['index', 'show']);
        Route::post('/invoices/{invoice}/mark-paid', [\App\Http\Controllers\Admin\InvoiceController::class, 'markAsPaid'])->name('admin.invoices.markPaid');
        Route::post('/invoices/{invoice}/send-reminder', [\App\Http\Controllers\Admin\InvoiceController::class, 'sendReminder'])->name('admin.invoices.sendReminder');
        Route::get('/invoices/{invoice}/download', [\App\Http\Controllers\Admin\InvoiceController::class, 'download'])->name('admin.invoices.download');

        //route activity logs
        Route::get('/activity-logs', [\App\Http\Controllers\Admin\ActivityLogController::class, 'index'])->name('admin.activity-logs.index');
        Route::get('/activity-logs/export', [\App\Http\Controllers\Admin\ActivityLogController::class, 'export'])->name('admin.activity-logs.export');
        Route::get('/activity-logs/{activityLog}', [\App\Http\Controllers\Admin\ActivityLogController::class, 'show'])->name('admin.activity-logs.show');

        //route resource lessons
        Route::resource('/lessons', \App\Http\Controllers\Admin\LessonController::class, ['as' => 'admin']);

        //route resource classrooms    
        Route::resource('/classrooms', \App\Http\Controllers\Admin\ClassroomController::class, ['as' => 'admin']);
        
        //route student import
        Route::get('/students/import', [\App\Http\Controllers\Admin\StudentController::class, 'import'])->name('admin.students.import');

        //route student store import
        Route::post('/students/import', [\App\Http\Controllers\Admin\StudentController::class, 'storeImport'])->name('admin.students.storeImport');

        //route resource students    
        Route::resource('/students', \App\Http\Controllers\Admin\StudentController::class, ['as' => 'admin']);
    
        //route resource exams    
        Route::resource('/exams', \App\Http\Controllers\Admin\ExamController::class, ['as' => 'admin']);
        
        //custom route for create question exam
        Route::get('/exams/{exam}/questions/create', [\App\Http\Controllers\Admin\ExamController::class, 'createQuestion'])->name('admin.exams.createQuestion');

        //custom route for store question exam
        Route::post('/exams/{exam}/questions/store', [\App\Http\Controllers\Admin\ExamController::class, 'storeQuestion'])->name('admin.exams.storeQuestion');
    
        //custom route for edit question exam
        Route::get('/exams/{exam}/questions/{question}/edit', [\App\Http\Controllers\Admin\ExamController::class, 'editQuestion'])->name('admin.exams.editQuestion');

        //custom route for update question exam
        Route::put('/exams/{exam}/questions/{question}/update', [\App\Http\Controllers\Admin\ExamController::class, 'updateQuestion'])->name('admin.exams.updateQuestion');
    
        //custom route for destroy question exam
        Route::delete('/exams/{exam}/questions/{question}/destroy', [\App\Http\Controllers\Admin\ExamController::class, 'destroyQuestion'])->name('admin.exams.destroyQuestion');
    
        //route student import
        Route::get('/exams/{exam}/questions/import', [\App\Http\Controllers\Admin\ExamController::class, 'import'])->name('admin.exam.questionImport');

        //route student import
        Route::post('/exams/{exam}/questions/import', [\App\Http\Controllers\Admin\ExamController::class, 'storeImport'])->name('admin.exam.questionStoreImport');
    
        //route resource exam_sessions    
        Route::resource('/exam_sessions', \App\Http\Controllers\Admin\ExamSessionController::class, ['as' => 'admin']);
    
        //custom route for enrolle create
        Route::get('/exam_sessions/{exam_session}/enrolle/create', [\App\Http\Controllers\Admin\ExamSessionController::class, 'createEnrolle'])->name('admin.exam_sessions.createEnrolle');

        //custom route for enrolle store
        Route::post('/exam_sessions/{exam_session}/enrolle/store', [\App\Http\Controllers\Admin\ExamSessionController::class, 'storeEnrolle'])->name('admin.exam_sessions.storeEnrolle');
        
        //custom route for enrolle destroy
        Route::delete('/exam_sessions/{exam_session}/enrolle/{exam_group}/destroy', [\App\Http\Controllers\Admin\ExamSessionController::class, 'destroyEnrolle'])->name('admin.exam_sessions.destroyEnrolle');
   
        //route index reports
        Route::get('/reports', [\App\Http\Controllers\Admin\ReportController::class, 'index'])->name('admin.reports.index');

        //route index reports filter
        Route::get('/reports/filter', [\App\Http\Controllers\Admin\ReportController::class, 'filter'])->name('admin.reports.filter');

        //route index reports export
        Route::get('/reports/export', [\App\Http\Controllers\Admin\ReportController::class, 'export'])->name('admin.reports.export');
        Route::resource('/users', \App\Http\Controllers\Admin\UserController::class, ['as' => 'admin']);

        // Tenant Settings routes (for tenant admins)
        Route::get('/settings', [\App\Http\Controllers\Tenant\SettingsController::class, 'index'])->name('settings.index');
        Route::post('/settings/general', [\App\Http\Controllers\Tenant\SettingsController::class, 'updateGeneral'])->name('settings.general');
        Route::post('/settings/branding', [\App\Http\Controllers\Tenant\SettingsController::class, 'updateBranding'])->name('settings.branding');
        Route::post('/settings/notification', [\App\Http\Controllers\Tenant\SettingsController::class, 'updateNotification'])->name('settings.notification');
        Route::post('/settings/test-whatsapp', [\App\Http\Controllers\Tenant\SettingsController::class, 'testWhatsApp'])->name('settings.testWhatsApp');
        Route::get('/settings/quota', [\App\Http\Controllers\Tenant\SettingsController::class, 'quota'])->name('settings.quota');

        // Notifications routes
        Route::get('/notifications', [\App\Http\Controllers\Tenant\NotificationController::class, 'index'])->name('notifications.index');
        Route::post('/notifications/{notification}/read', [\App\Http\Controllers\Tenant\NotificationController::class, 'markAsRead'])->name('notifications.read');
        Route::post('/notifications/read-all', [\App\Http\Controllers\Tenant\NotificationController::class, 'markAllAsRead'])->name('notifications.readAll');
        Route::delete('/notifications/{notification}', [\App\Http\Controllers\Tenant\NotificationController::class, 'destroy'])->name('notifications.destroy');
        Route::get('/notifications/unread-count', [\App\Http\Controllers\Tenant\NotificationController::class, 'unreadCount'])->name('notifications.unreadCount');
        Route::post('/notifications/announcement', [\App\Http\Controllers\Tenant\NotificationController::class, 'sendAnnouncement'])->name('notifications.announcement');

        // Export routes
        Route::get('/export', [\App\Http\Controllers\Tenant\ExportController::class, 'index'])->name('export.index');
        Route::get('/export/students', [\App\Http\Controllers\Tenant\ExportController::class, 'exportStudents'])->name('export.students');
        Route::get('/export/exam-results/{exam}', [\App\Http\Controllers\Tenant\ExportController::class, 'exportExamResults'])->name('export.examResults');
        Route::get('/export/backup', [\App\Http\Controllers\Tenant\ExportController::class, 'exportBackup'])->name('export.backup');

        // Notification Settings routes (WhatsApp/Fonnte configuration)
        Route::get('/notification-settings', [\App\Http\Controllers\Tenant\NotificationSettingsController::class, 'index'])->name('notification-settings.index');
        Route::post('/notification-settings/fonnte', [\App\Http\Controllers\Tenant\NotificationSettingsController::class, 'updateFonnte'])->name('notification-settings.fonnte');
        Route::post('/notification-settings/preferences', [\App\Http\Controllers\Tenant\NotificationSettingsController::class, 'updatePreferences'])->name('notification-settings.preferences');
        Route::post('/notification-settings/templates', [\App\Http\Controllers\Tenant\NotificationSettingsController::class, 'updateTemplates'])->name('notification-settings.templates');
        Route::post('/notification-settings/reset-template', [\App\Http\Controllers\Tenant\NotificationSettingsController::class, 'resetTemplate'])->name('notification-settings.resetTemplate');
        Route::post('/notification-settings/test-connection', [\App\Http\Controllers\Tenant\NotificationSettingsController::class, 'testConnection'])->name('notification-settings.testConnection');
        Route::post('/notification-settings/send-test', [\App\Http\Controllers\Tenant\NotificationSettingsController::class, 'sendTestMessage'])->name('notification-settings.sendTest');
        Route::get('/notification-settings/preview', [\App\Http\Controllers\Tenant\NotificationSettingsController::class, 'previewTemplate'])->name('notification-settings.preview');

        // Billing routes (for tenant admins)
        Route::get('/billing', [\App\Http\Controllers\Tenant\BillingController::class, 'index'])->name('billing.index');
        Route::get('/billing/plans', [\App\Http\Controllers\Tenant\BillingController::class, 'plans'])->name('billing.plans');
        Route::post('/billing/upgrade', [\App\Http\Controllers\Tenant\BillingController::class, 'upgradePlan'])->name('billing.upgrade');
        Route::get('/billing/invoices', [\App\Http\Controllers\Tenant\BillingController::class, 'invoices'])->name('billing.invoices');
        Route::get('/billing/invoice/{invoice}', [\App\Http\Controllers\Tenant\BillingController::class, 'invoice'])->name('billing.invoice');
        Route::post('/billing/invoice/{invoice}/confirm', [\App\Http\Controllers\Tenant\BillingController::class, 'confirmPayment'])->name('billing.confirm');
        Route::get('/billing/invoice/{invoice}/download', [\App\Http\Controllers\Tenant\BillingController::class, 'downloadInvoice'])->name('billing.download');
        Route::post('/billing/cancel', [\App\Http\Controllers\Tenant\BillingController::class, 'cancelSubscription'])->name('billing.cancel');

        // Tenant Activity Logs routes
        Route::get('/activity-logs', [\App\Http\Controllers\Tenant\ActivityLogController::class, 'index'])->name('activity-logs.index');
        Route::get('/activity-logs/export', [\App\Http\Controllers\Tenant\ActivityLogController::class, 'export'])->name('activity-logs.export');
        Route::get('/activity-logs/{activityLog}', [\App\Http\Controllers\Tenant\ActivityLogController::class, 'show'])->name('activity-logs.show');

    });
});

//route homepage
// Route::get('/', function () {

//     if(auth()->guard('student')->check()) {
//         return redirect()->route('student.dashboard');
//     }
//     return \Inertia\Inertia::render('Student/Login/Index');
// });

Route::get('/', function () {
    return \Inertia\Inertia::render('Front/LandingPage');
});

//login students
Route::post('/students/login', \App\Http\Controllers\Student\LoginController::class)->name('student.login');

//prefix "student"
Route::prefix('student')->group(function() {

    //middleware "student"
    Route::group(['middleware' => 'student'], function () {
        
        //route dashboard
        Route::get('/dashboard', App\Http\Controllers\Student\DashboardController::class)->name('student.dashboard');
    
        //route exam confirmation
        Route::get('/exam-confirmation/{id}', [App\Http\Controllers\Student\ExamController::class, 'confirmation'])->name('student.exams.confirmation');
    
        //route exam start
        Route::get('/exam-start/{id}', [App\Http\Controllers\Student\ExamController::class, 'startExam'])->name('student.exams.startExam');
        
         //route exam show
         Route::get('/exam/{id}/{page}', [App\Http\Controllers\Student\ExamController::class, 'show'])->name('student.exams.show');
    
        //route exam update duration
        Route::put('/exam-duration/update/{grade_id}', [App\Http\Controllers\Student\ExamController::class, 'updateDuration'])->name('student.exams.update_duration');
        
        //route answer question
        Route::post('/exam-answer', [App\Http\Controllers\Student\ExamController::class, 'answerQuestion'])->name('student.exams.answerQuestion');
        
        //route exam end
        Route::post('/exam-end', [App\Http\Controllers\Student\ExamController::class, 'endExam'])->name('student.exams.endExam');
        
        //route exam result
        Route::get('/exam-result/{exam_group_id}', [App\Http\Controllers\Student\ExamController::class, 'resultExam'])->name('student.exams.resultExam');
    });

});