<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('notification_settings', function (Blueprint $table) {
            $table->json('message_templates')->nullable()->after('result_notification_enabled');
            $table->boolean('welcome_notification_enabled')->default(true)->after('result_notification_enabled');
            $table->string('fonnte_device_status')->nullable()->after('fonnte_sender');
            $table->timestamp('fonnte_last_check')->nullable()->after('fonnte_device_status');
        });
    }

    public function down(): void
    {
        Schema::table('notification_settings', function (Blueprint $table) {
            $table->dropColumn([
                'message_templates',
                'welcome_notification_enabled',
                'fonnte_device_status',
                'fonnte_last_check',
            ]);
        });
    }
};
