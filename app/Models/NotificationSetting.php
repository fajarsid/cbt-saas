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
        'fonnte_device_status',
        'fonnte_last_check',
        'whatsapp_enabled',
        'email_enabled',
        'exam_reminder_enabled',
        'exam_reminder_hours',
        'result_notification_enabled',
        'welcome_notification_enabled',
        'message_templates',
    ];

    protected function casts(): array
    {
        return [
            'whatsapp_enabled' => 'boolean',
            'email_enabled' => 'boolean',
            'exam_reminder_enabled' => 'boolean',
            'result_notification_enabled' => 'boolean',
            'welcome_notification_enabled' => 'boolean',
            'message_templates' => 'array',
            'fonnte_last_check' => 'datetime',
        ];
    }

    protected $hidden = [
        'fonnte_api_key',
    ];

    /**
     * Default message templates
     */
    public static function getDefaultTemplates(): array
    {
        return [
            'exam_reminder' => [
                'title' => 'Pengingat Ujian',
                'message' => "Halo {nama_siswa}!\n\nAnda memiliki ujian *{nama_ujian}* yang akan dimulai pada:\n📅 {tanggal_ujian}\n⏰ {waktu_ujian}\n\nSilakan persiapkan diri Anda dengan baik.\n\nSalam,\n{nama_organisasi}",
            ],
            'exam_result' => [
                'title' => 'Hasil Ujian',
                'message' => "Halo {nama_siswa}!\n\nHasil ujian *{nama_ujian}* telah tersedia.\n\n📊 *Nilai Anda: {nilai}*\n\nUntuk melihat detail hasil ujian, silakan login ke akun Anda.\n\nSalam,\n{nama_organisasi}",
            ],
            'welcome' => [
                'title' => 'Selamat Datang',
                'message' => "Halo {nama_siswa}!\n\nSelamat datang di *{nama_organisasi}*.\n\nAkun Anda telah aktif dengan data berikut:\n📌 NISN: {nisn}\n📌 Kelas: {kelas}\n\nSilakan login menggunakan NISN Anda untuk mengikuti ujian.\n\nSalam,\n{nama_organisasi}",
            ],
            'announcement' => [
                'title' => 'Pengumuman',
                'message' => "*{judul_pengumuman}*\n\n{isi_pengumuman}\n\nSalam,\n{nama_organisasi}",
            ],
            'exam_start' => [
                'title' => 'Ujian Dimulai',
                'message' => "Halo {nama_siswa}!\n\nUjian *{nama_ujian}* telah dimulai.\n\n⏰ Waktu: {durasi} menit\n📝 Jumlah Soal: {jumlah_soal}\n\nSilakan login sekarang untuk mengerjakan ujian.\n\nSalam,\n{nama_organisasi}",
            ],
        ];
    }

    /**
     * Get message template by type
     */
    public function getTemplate(string $type): array
    {
        $templates = $this->message_templates ?? self::getDefaultTemplates();
        return $templates[$type] ?? ['title' => '', 'message' => ''];
    }

    /**
     * Get all templates with defaults
     */
    public function getAllTemplates(): array
    {
        $defaults = self::getDefaultTemplates();
        $custom = $this->message_templates ?? [];

        return array_merge($defaults, $custom);
    }

    /**
     * Parse template with variables
     */
    public function parseTemplate(string $type, array $variables): array
    {
        $template = $this->getTemplate($type);

        $title = $template['title'];
        $message = $template['message'];

        foreach ($variables as $key => $value) {
            $title = str_replace("{{$key}}", $value, $title);
            $message = str_replace("{{$key}}", $value, $message);
        }

        return [
            'title' => $title,
            'message' => $message,
        ];
    }

    /**
     * Available template variables
     */
    public static function getTemplateVariables(): array
    {
        return [
            'exam_reminder' => [
                '{nama_siswa}' => 'Nama siswa',
                '{nama_ujian}' => 'Nama ujian',
                '{tanggal_ujian}' => 'Tanggal ujian',
                '{waktu_ujian}' => 'Waktu mulai ujian',
                '{durasi}' => 'Durasi ujian dalam menit',
                '{nama_organisasi}' => 'Nama organisasi/sekolah',
            ],
            'exam_result' => [
                '{nama_siswa}' => 'Nama siswa',
                '{nama_ujian}' => 'Nama ujian',
                '{nilai}' => 'Nilai yang diperoleh',
                '{nama_organisasi}' => 'Nama organisasi/sekolah',
            ],
            'welcome' => [
                '{nama_siswa}' => 'Nama siswa',
                '{nisn}' => 'NISN siswa',
                '{kelas}' => 'Nama kelas',
                '{nama_organisasi}' => 'Nama organisasi/sekolah',
            ],
            'announcement' => [
                '{judul_pengumuman}' => 'Judul pengumuman',
                '{isi_pengumuman}' => 'Isi pengumuman',
                '{nama_organisasi}' => 'Nama organisasi/sekolah',
            ],
            'exam_start' => [
                '{nama_siswa}' => 'Nama siswa',
                '{nama_ujian}' => 'Nama ujian',
                '{durasi}' => 'Durasi ujian dalam menit',
                '{jumlah_soal}' => 'Jumlah soal',
                '{nama_organisasi}' => 'Nama organisasi/sekolah',
            ],
        ];
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }
}
