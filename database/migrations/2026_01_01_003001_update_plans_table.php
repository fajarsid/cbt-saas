<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('plans', function (Blueprint $table) {
            // Rename billing_period to billing_cycle for consistency
            $table->renameColumn('billing_period', 'billing_cycle');

            // Add missing fields
            $table->integer('max_users')->default(5)->after('max_admins');
            $table->integer('trial_days')->default(14)->after('is_active');
            $table->boolean('is_featured')->default(false)->after('is_active');
        });

        // Add price column to subscriptions for historical pricing
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->decimal('price', 12, 2)->nullable()->after('plan_id');
        });
    }

    public function down(): void
    {
        Schema::table('plans', function (Blueprint $table) {
            $table->renameColumn('billing_cycle', 'billing_period');
            $table->dropColumn(['max_users', 'trial_days', 'is_featured']);
        });

        Schema::table('subscriptions', function (Blueprint $table) {
            $table->dropColumn('price');
        });
    }
};
