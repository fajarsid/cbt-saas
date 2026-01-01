<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            // Branding
            $table->string('primary_color')->nullable()->after('logo');
            $table->string('secondary_color')->nullable()->after('primary_color');
            $table->string('favicon')->nullable()->after('secondary_color');

            // Additional settings
            $table->string('timezone')->default('Asia/Jakarta')->after('address');
            $table->string('date_format')->default('d/m/Y')->after('timezone');
            $table->string('time_format')->default('H:i')->after('date_format');

            // Contact
            $table->string('website')->nullable()->after('phone');

            // Subscription reference
            $table->foreignId('current_plan_id')->nullable()->after('plan');
        });
    }

    public function down(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->dropColumn([
                'primary_color',
                'secondary_color',
                'favicon',
                'timezone',
                'date_format',
                'time_format',
                'website',
                'current_plan_id',
            ]);
        });
    }
};
