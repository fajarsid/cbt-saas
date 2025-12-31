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
        // Add tenant_id to users table
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('tenant_id')->nullable()->after('id')->constrained()->nullOnDelete();
            $table->index('tenant_id');
        });

        // Add tenant_id to students table
        Schema::table('students', function (Blueprint $table) {
            $table->foreignId('tenant_id')->after('id')->constrained()->cascadeOnDelete();
            $table->index('tenant_id');
        });

        // Add tenant_id to classrooms table
        Schema::table('classrooms', function (Blueprint $table) {
            $table->foreignId('tenant_id')->after('id')->constrained()->cascadeOnDelete();
            $table->index('tenant_id');
        });

        // Add tenant_id to lessons table
        Schema::table('lessons', function (Blueprint $table) {
            $table->foreignId('tenant_id')->after('id')->constrained()->cascadeOnDelete();
            $table->index('tenant_id');
        });

        // Add tenant_id to exams table
        Schema::table('exams', function (Blueprint $table) {
            $table->foreignId('tenant_id')->after('id')->constrained()->cascadeOnDelete();
            $table->index('tenant_id');
        });

        // Add tenant_id to exam_sessions table
        Schema::table('exam_sessions', function (Blueprint $table) {
            $table->foreignId('tenant_id')->after('id')->constrained()->cascadeOnDelete();
            $table->index('tenant_id');
        });

        // Add tenant_id to questions table
        Schema::table('questions', function (Blueprint $table) {
            $table->foreignId('tenant_id')->after('id')->constrained()->cascadeOnDelete();
            $table->index('tenant_id');
        });

        // Add tenant_id to exam_groups table
        Schema::table('exam_groups', function (Blueprint $table) {
            $table->foreignId('tenant_id')->after('id')->constrained()->cascadeOnDelete();
            $table->index('tenant_id');
        });

        // Add tenant_id to answers table
        Schema::table('answers', function (Blueprint $table) {
            $table->foreignId('tenant_id')->after('id')->constrained()->cascadeOnDelete();
            $table->index('tenant_id');
        });

        // Add tenant_id to grades table
        Schema::table('grades', function (Blueprint $table) {
            $table->foreignId('tenant_id')->after('id')->constrained()->cascadeOnDelete();
            $table->index('tenant_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $tables = ['users', 'students', 'classrooms', 'lessons', 'exams', 'exam_sessions', 'questions', 'exam_groups', 'answers', 'grades'];

        foreach ($tables as $tableName) {
            Schema::table($tableName, function (Blueprint $table) {
                $table->dropConstrainedForeignId('tenant_id');
            });
        }
    }
};
