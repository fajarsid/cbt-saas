<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\NotificationSetting;
use App\Services\FonnteService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class SettingsController extends Controller
{
    /**
     * Show general settings page
     */
    public function index(): Response
    {
        $tenant = auth()->user()->tenant;
        $notificationSetting = $tenant->notificationSetting ?? new NotificationSetting();

        return Inertia::render('Tenant/Settings/Index', [
            'tenant' => $tenant,
            'notificationSetting' => $notificationSetting,
        ]);
    }

    /**
     * Update general settings
     */
    public function updateGeneral(Request $request)
    {
        $tenant = auth()->user()->tenant;

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20',
            'website' => 'nullable|url|max:255',
            'address' => 'nullable|string|max:500',
            'timezone' => 'required|string|max:50',
            'date_format' => 'required|string|max:20',
            'time_format' => 'required|string|max:20',
        ]);

        $tenant->update($validated);

        ActivityLog::log('update', 'Mengubah pengaturan umum', $tenant, auth()->user());

        return back()->with('success', 'Pengaturan berhasil disimpan.');
    }

    /**
     * Update branding settings
     */
    public function updateBranding(Request $request)
    {
        $tenant = auth()->user()->tenant;

        $validated = $request->validate([
            'primary_color' => 'nullable|string|max:7',
            'secondary_color' => 'nullable|string|max:7',
            'logo' => 'nullable|image|max:2048',
            'favicon' => 'nullable|image|max:512',
        ]);

        // Handle logo upload
        if ($request->hasFile('logo')) {
            if ($tenant->logo) {
                Storage::disk('public')->delete($tenant->logo);
            }
            $validated['logo'] = $request->file('logo')->store('tenants/logos', 'public');
        }

        // Handle favicon upload
        if ($request->hasFile('favicon')) {
            if ($tenant->favicon) {
                Storage::disk('public')->delete($tenant->favicon);
            }
            $validated['favicon'] = $request->file('favicon')->store('tenants/favicons', 'public');
        }

        $tenant->update($validated);

        ActivityLog::log('update', 'Mengubah pengaturan branding', $tenant, auth()->user());

        return back()->with('success', 'Branding berhasil disimpan.');
    }

    /**
     * Update notification settings
     */
    public function updateNotification(Request $request, FonnteService $fonnteService)
    {
        $tenant = auth()->user()->tenant;

        $validated = $request->validate([
            'fonnte_api_key' => 'nullable|string|max:255',
            'fonnte_sender' => 'nullable|string|max:20',
            'whatsapp_enabled' => 'boolean',
            'email_enabled' => 'boolean',
            'exam_reminder_enabled' => 'boolean',
            'exam_reminder_hours' => 'integer|min:1|max:72',
            'result_notification_enabled' => 'boolean',
        ]);

        $setting = NotificationSetting::updateOrCreate(
            ['tenant_id' => $tenant->id],
            $validated
        );

        ActivityLog::log('update', 'Mengubah pengaturan notifikasi', $setting, auth()->user());

        return back()->with('success', 'Pengaturan notifikasi berhasil disimpan.');
    }

    /**
     * Test WhatsApp connection
     */
    public function testWhatsApp(Request $request, FonnteService $fonnteService)
    {
        $tenant = auth()->user()->tenant;
        $setting = $tenant->notificationSetting;

        if (!$setting || !$setting->fonnte_api_key) {
            return back()->with('error', 'API key Fonnte belum dikonfigurasi.');
        }

        // Check device status
        $status = $fonnteService->getDeviceStatus($setting->fonnte_api_key);

        if (!$status['success']) {
            return back()->with('error', 'Gagal terhubung ke Fonnte: ' . ($status['message'] ?? 'Unknown error'));
        }

        // Send test message
        $testPhone = $request->input('test_phone', $setting->fonnte_sender);
        if ($testPhone) {
            $result = $fonnteService->send(
                $testPhone,
                "Test message dari {$tenant->name}. Koneksi WhatsApp berhasil!",
                $setting->fonnte_api_key
            );

            if ($result['success']) {
                return back()->with('success', 'Test message berhasil dikirim!');
            }

            return back()->with('error', 'Gagal mengirim test message: ' . $result['message']);
        }

        return back()->with('success', 'Koneksi Fonnte berhasil! Device status: ' . json_encode($status['data']));
    }

    /**
     * Show quota usage
     */
    public function quota(): Response
    {
        $tenant = auth()->user()->tenant;

        return Inertia::render('Tenant/Settings/Quota', [
            'tenant' => $tenant,
            'usage' => [
                'students' => [
                    'current' => $tenant->students()->count(),
                    'max' => $tenant->max_students,
                    'percentage' => $tenant->getStudentsUsagePercentage(),
                ],
                'exams' => [
                    'current' => $tenant->exams()->count(),
                    'max' => $tenant->max_exams,
                    'percentage' => $tenant->getExamsUsagePercentage(),
                ],
            ],
            'subscription' => $tenant->activeSubscription,
            'plan' => $tenant->currentPlan,
        ]);
    }
}
