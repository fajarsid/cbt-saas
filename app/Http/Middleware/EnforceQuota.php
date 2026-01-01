<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnforceQuota
{
    /**
     * Handle an incoming request.
     * Check if tenant has exceeded their quota limits
     */
    public function handle(Request $request, Closure $next, string $type = null): Response
    {
        $tenant = $request->user()?->tenant;

        if (!$tenant) {
            return $next($request);
        }

        // Check based on type
        if ($type === 'students' && !$tenant->canAddMoreStudents()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Kuota peserta telah habis. Silakan upgrade paket Anda.',
                    'quota_exceeded' => true,
                    'type' => 'students',
                    'current' => $tenant->students()->count(),
                    'max' => $tenant->max_students,
                ], 403);
            }

            return back()->with('error', 'Kuota peserta telah habis. Silakan upgrade paket Anda.');
        }

        if ($type === 'exams' && !$tenant->canAddMoreExams()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Kuota ujian telah habis. Silakan upgrade paket Anda.',
                    'quota_exceeded' => true,
                    'type' => 'exams',
                    'current' => $tenant->exams()->count(),
                    'max' => $tenant->max_exams,
                ], 403);
            }

            return back()->with('error', 'Kuota ujian telah habis. Silakan upgrade paket Anda.');
        }

        return $next($request);
    }
}
