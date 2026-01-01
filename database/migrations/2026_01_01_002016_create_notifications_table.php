<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignId('tenant_id')->nullable()->constrained()->cascadeOnDelete();
            $table->string('type'); // announcement, reminder, alert, welcome, etc.
            $table->nullableMorphs('notifiable'); // who receives (user, student, tenant)
            $table->string('title');
            $table->text('message');
            $table->json('data')->nullable(); // additional data
            $table->string('channel')->default('in_app'); // in_app, whatsapp, email
            $table->string('whatsapp_status')->nullable(); // pending, sent, delivered, failed
            $table->string('whatsapp_message_id')->nullable(); // Fonnte message ID
            $table->timestamp('read_at')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamps();

            $table->index(['notifiable_type', 'notifiable_id', 'read_at']);
            $table->index(['tenant_id', 'created_at']);
        });

        // Notification settings per tenant (for Fonnte config)
        Schema::create('notification_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->string('fonnte_api_key')->nullable();
            $table->string('fonnte_sender')->nullable(); // WhatsApp number
            $table->boolean('whatsapp_enabled')->default(false);
            $table->boolean('email_enabled')->default(true);
            $table->boolean('exam_reminder_enabled')->default(true);
            $table->integer('exam_reminder_hours')->default(24); // hours before exam
            $table->boolean('result_notification_enabled')->default(true);
            $table->timestamps();

            $table->unique('tenant_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notification_settings');
        Schema::dropIfExists('notifications');
    }
};
