<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tenants', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('domain')->nullable()->unique();
            $table->string('email')->unique();
            $table->string('phone')->nullable();
            $table->string('logo')->nullable();
            $table->text('address')->nullable();
            $table->enum('status', ['active', 'inactive', 'suspended'])->default('active');
            $table->enum('plan', ['free', 'basic', 'pro', 'enterprise'])->default('free');
            $table->integer('max_students')->default(50);
            $table->integer('max_exams')->default(10);
            $table->timestamp('trial_ends_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tenants');
    }
};
