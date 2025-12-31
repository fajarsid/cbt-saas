<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->json('permissions')->nullable();
            $table->boolean('is_system')->default(false); // System roles can't be deleted
            $table->timestamps();
        });

        // Seed default roles
        DB::table('roles')->insert([
            [
                'name' => 'Super Admin',
                'slug' => 'super-admin',
                'description' => 'Platform administrator with full access',
                'permissions' => json_encode(['*']),
                'is_system' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Tenant Admin',
                'slug' => 'tenant-admin',
                'description' => 'Tenant administrator with full access to their organization',
                'permissions' => json_encode([
                    'users.manage',
                    'students.manage',
                    'exams.manage',
                    'reports.view',
                    'settings.manage',
                ]),
                'is_system' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Supervisor',
                'slug' => 'supervisor',
                'description' => 'Can manage exams and view reports',
                'permissions' => json_encode([
                    'exams.manage',
                    'students.view',
                    'reports.view',
                ]),
                'is_system' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Proctor',
                'slug' => 'proctor',
                'description' => 'Can monitor exams in real-time',
                'permissions' => json_encode([
                    'exams.view',
                    'exams.monitor',
                    'students.view',
                ]),
                'is_system' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('roles');
    }
};
