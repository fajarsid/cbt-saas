<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\NotificationSetting;
use App\Models\Tenant;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FonnteService
{
    protected string $baseUrl = 'https://api.fonnte.com';

    /**
     * Send WhatsApp message via Fonnte
     */
    public function send(string $phone, string $message, ?string $apiKey = null): array
    {
        if (!$apiKey) {
            return [
                'success' => false,
                'message' => 'API key tidak tersedia',
            ];
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

            if ($response->successful() && isset($data['status']) && $data['status'] === true) {
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
            Log::error('Fonnte send error: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Send WhatsApp message for a tenant
     */
    public function sendForTenant(Tenant $tenant, string $phone, string $message): array
    {
        $settings = $tenant->notificationSetting;

        if (!$settings || !$settings->whatsapp_enabled) {
            return [
                'success' => false,
                'message' => 'WhatsApp tidak aktif untuk tenant ini',
            ];
        }

        return $this->send($phone, $message, $settings->fonnte_api_key);
    }

    /**
     * Send notification via WhatsApp
     */
    public function sendNotification(Notification $notification): array
    {
        if (!$notification->notifiable || !method_exists($notification->notifiable, 'getPhoneNumber')) {
            return [
                'success' => false,
                'message' => 'Notifiable tidak memiliki nomor telepon',
            ];
        }

        $phone = $notification->notifiable->getPhoneNumber();
        if (!$phone) {
            return [
                'success' => false,
                'message' => 'Nomor telepon tidak tersedia',
            ];
        }

        $message = $this->formatNotificationMessage($notification);
        $tenant = $notification->tenant;

        $result = $this->sendForTenant($tenant, $phone, $message);

        // Update notification status
        $notification->update([
            'whatsapp_status' => $result['success'] ? 'sent' : 'failed',
            'whatsapp_message_id' => $result['message_id'] ?? null,
            'sent_at' => $result['success'] ? now() : null,
        ]);

        return $result;
    }

    /**
     * Send bulk messages
     */
    public function sendBulk(array $targets, string $message, ?string $apiKey = null): array
    {
        if (!$apiKey) {
            return [
                'success' => false,
                'message' => 'API key tidak tersedia',
            ];
        }

        try {
            $formattedTargets = array_map(fn($phone) => $this->formatPhone($phone), $targets);

            $response = Http::withHeaders([
                'Authorization' => $apiKey,
            ])->post("{$this->baseUrl}/send", [
                'target' => implode(',', $formattedTargets),
                'message' => $message,
                'countryCode' => '62',
            ]);

            $data = $response->json();

            if ($response->successful() && isset($data['status']) && $data['status'] === true) {
                return [
                    'success' => true,
                    'message' => 'Pesan berhasil dikirim ke ' . count($targets) . ' penerima',
                ];
            }

            return [
                'success' => false,
                'message' => $data['reason'] ?? 'Gagal mengirim pesan',
            ];
        } catch (\Exception $e) {
            Log::error('Fonnte bulk send error: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Check device status
     */
    public function getDeviceStatus(?string $apiKey = null): array
    {
        if (!$apiKey) {
            return [
                'success' => false,
                'message' => 'API key tidak tersedia',
            ];
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => $apiKey,
            ])->post("{$this->baseUrl}/device");

            $data = $response->json();

            return [
                'success' => $response->successful(),
                'data' => $data,
            ];
        } catch (\Exception $e) {
            Log::error('Fonnte device status error: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Format phone number for Indonesia
     */
    protected function formatPhone(string $phone): string
    {
        // Remove all non-numeric characters
        $phone = preg_replace('/[^0-9]/', '', $phone);

        // Remove leading zero and add country code
        if (str_starts_with($phone, '0')) {
            $phone = '62' . substr($phone, 1);
        }

        // Add country code if not present
        if (!str_starts_with($phone, '62')) {
            $phone = '62' . $phone;
        }

        return $phone;
    }

    /**
     * Format notification message
     */
    protected function formatNotificationMessage(Notification $notification): string
    {
        $tenantName = $notification->tenant->name ?? 'CBT System';

        return "*{$notification->title}*\n\n{$notification->message}\n\n_{$tenantName}_";
    }
}
