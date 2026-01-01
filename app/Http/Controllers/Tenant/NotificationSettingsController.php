<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\NotificationSetting;
use App\Services\FonnteService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class NotificationSettingsController extends Controller
{
    /**
     * Show notification settings page
     */
    public function index(): Response
    {
        $tenant = auth()->user()->tenant;
        $setting = $tenant->notificationSetting ?? new NotificationSetting();

        // Get templates with defaults
        $templates = $setting->getAllTemplates();
        $templateVariables = NotificationSetting::getTemplateVariables();

        return Inertia::render('Tenant/NotificationSettings/Index', [
            'tenant' => $tenant,
            'setting' => [
                'fonnte_api_key' => $setting->fonnte_api_key ? '********' : null,
                'fonnte_api_key_exists' => !empty($setting->fonnte_api_key),
                'fonnte_sender' => $setting->fonnte_sender,
                'fonnte_device_status' => $setting->fonnte_device_status,
                'fonnte_last_check' => $setting->fonnte_last_check?->format('Y-m-d H:i:s'),
                'whatsapp_enabled' => $setting->whatsapp_enabled,
                'email_enabled' => $setting->email_enabled,
                'exam_reminder_enabled' => $setting->exam_reminder_enabled,
                'exam_reminder_hours' => $setting->exam_reminder_hours ?? 24,
                'result_notification_enabled' => $setting->result_notification_enabled,
                'welcome_notification_enabled' => $setting->welcome_notification_enabled ?? true,
            ],
            'templates' => $templates,
            'templateVariables' => $templateVariables,
            'defaultTemplates' => NotificationSetting::getDefaultTemplates(),
        ]);
    }

    /**
     * Update Fonnte configuration
     */
    public function updateFonnte(Request $request, FonnteService $fonnteService)
    {
        $tenant = auth()->user()->tenant;

        $validated = $request->validate([
            'fonnte_api_key' => 'nullable|string|max:255',
            'fonnte_sender' => 'nullable|string|max:20',
            'whatsapp_enabled' => 'boolean',
        ]);

        $setting = NotificationSetting::updateOrCreate(
            ['tenant_id' => $tenant->id],
            [
                'fonnte_sender' => $validated['fonnte_sender'],
                'whatsapp_enabled' => $validated['whatsapp_enabled'] ?? false,
            ]
        );

        // Only update API key if a new one is provided (not the masked value)
        if (!empty($validated['fonnte_api_key']) && $validated['fonnte_api_key'] !== '********') {
            $setting->update(['fonnte_api_key' => $validated['fonnte_api_key']]);
        }

        // Check device status if API key exists
        if ($setting->fonnte_api_key) {
            $status = $fonnteService->getDeviceStatus($setting->fonnte_api_key);
            $setting->update([
                'fonnte_device_status' => $status['success'] ? 'connected' : 'disconnected',
                'fonnte_last_check' => now(),
            ]);
        }

        ActivityLog::log('update', 'Mengubah konfigurasi Fonnte WhatsApp', $setting, auth()->user());

        return back()->with('success', 'Konfigurasi Fonnte berhasil disimpan.');
    }

    /**
     * Update notification preferences
     */
    public function updatePreferences(Request $request)
    {
        $tenant = auth()->user()->tenant;

        $validated = $request->validate([
            'exam_reminder_enabled' => 'boolean',
            'exam_reminder_hours' => 'integer|min:1|max:168',
            'result_notification_enabled' => 'boolean',
            'welcome_notification_enabled' => 'boolean',
            'email_enabled' => 'boolean',
        ]);

        NotificationSetting::updateOrCreate(
            ['tenant_id' => $tenant->id],
            $validated
        );

        ActivityLog::log('update', 'Mengubah preferensi notifikasi', null, auth()->user());

        return back()->with('success', 'Preferensi notifikasi berhasil disimpan.');
    }

    /**
     * Update message templates
     */
    public function updateTemplates(Request $request)
    {
        $tenant = auth()->user()->tenant;

        $validated = $request->validate([
            'templates' => 'required|array',
            'templates.*.title' => 'required|string|max:255',
            'templates.*.message' => 'required|string|max:2000',
        ]);

        $setting = NotificationSetting::updateOrCreate(
            ['tenant_id' => $tenant->id],
            ['message_templates' => $validated['templates']]
        );

        ActivityLog::log('update', 'Mengubah template pesan notifikasi', $setting, auth()->user());

        return back()->with('success', 'Template pesan berhasil disimpan.');
    }

    /**
     * Reset template to default
     */
    public function resetTemplate(Request $request)
    {
        $tenant = auth()->user()->tenant;
        $type = $request->input('type');

        $setting = $tenant->notificationSetting;
        if (!$setting) {
            return back()->with('error', 'Pengaturan tidak ditemukan.');
        }

        $templates = $setting->message_templates ?? [];
        $defaults = NotificationSetting::getDefaultTemplates();

        if (isset($defaults[$type])) {
            $templates[$type] = $defaults[$type];
            $setting->update(['message_templates' => $templates]);

            ActivityLog::log('update', "Reset template pesan: {$type}", $setting, auth()->user());

            return back()->with('success', 'Template berhasil di-reset ke default.');
        }

        return back()->with('error', 'Template tidak ditemukan.');
    }

    /**
     * Test WhatsApp connection
     */
    public function testConnection(FonnteService $fonnteService)
    {
        $tenant = auth()->user()->tenant;
        $setting = $tenant->notificationSetting;

        if (!$setting || !$setting->fonnte_api_key) {
            return back()->with('error', 'API key Fonnte belum dikonfigurasi.');
        }

        $status = $fonnteService->getDeviceStatus($setting->fonnte_api_key);

        $setting->update([
            'fonnte_device_status' => $status['success'] ? 'connected' : 'disconnected',
            'fonnte_last_check' => now(),
        ]);

        if ($status['success']) {
            return back()->with('success', 'Koneksi berhasil! Device status: ' . ($status['data']['device'] ?? 'OK'));
        }

        return back()->with('error', 'Koneksi gagal: ' . ($status['message'] ?? 'Unknown error'));
    }

    /**
     * Send test message
     */
    public function sendTestMessage(Request $request, FonnteService $fonnteService)
    {
        $tenant = auth()->user()->tenant;
        $setting = $tenant->notificationSetting;

        if (!$setting || !$setting->fonnte_api_key) {
            return back()->with('error', 'API key Fonnte belum dikonfigurasi.');
        }

        $validated = $request->validate([
            'phone' => 'required|string|max:20',
            'message' => 'required|string|max:1000',
        ]);

        $result = $fonnteService->send(
            $validated['phone'],
            $validated['message'],
            $setting->fonnte_api_key
        );

        if ($result['success']) {
            ActivityLog::log('notification', 'Mengirim test message WhatsApp ke ' . $validated['phone'], null, auth()->user());
            return back()->with('success', 'Pesan berhasil dikirim!');
        }

        return back()->with('error', 'Gagal mengirim pesan: ' . $result['message']);
    }

    /**
     * Preview template with sample data
     */
    public function previewTemplate(Request $request)
    {
        $tenant = auth()->user()->tenant;
        $type = $request->input('type');
        $setting = $tenant->notificationSetting ?? new NotificationSetting();

        // Sample data for preview
        $sampleData = [
            'nama_siswa' => 'Ahmad Fadillah',
            'nama_ujian' => 'Ujian Tengah Semester Matematika',
            'tanggal_ujian' => now()->addDay()->format('d F Y'),
            'waktu_ujian' => '08:00 WIB',
            'durasi' => '90',
            'jumlah_soal' => '40',
            'nilai' => '85',
            'nisn' => '1234567890',
            'kelas' => 'XII IPA 1',
            'judul_pengumuman' => 'Pengumuman Penting',
            'isi_pengumuman' => 'Ini adalah contoh isi pengumuman.',
            'nama_organisasi' => $tenant->name,
        ];

        $result = $setting->parseTemplate($type, $sampleData);

        return response()->json($result);
    }
}
