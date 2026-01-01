<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ActivityLogController extends Controller
{
    /**
     * Display activity logs for current tenant
     */
    public function index(Request $request): Response
    {
        $tenant = auth()->user()->tenant;

        $logs = ActivityLog::where('tenant_id', $tenant->id)
            ->with('causer')
            ->when($request->log_type, function ($query, $type) {
                $query->where('log_type', $type);
            })
            ->when($request->search, function ($query, $search) {
                $query->where('description', 'like', "%{$search}%");
            })
            ->when($request->date_from, function ($query, $date) {
                $query->whereDate('created_at', '>=', $date);
            })
            ->when($request->date_to, function ($query, $date) {
                $query->whereDate('created_at', '<=', $date);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(30)
            ->withQueryString();

        // Get unique log types for this tenant
        $logTypes = ActivityLog::where('tenant_id', $tenant->id)
            ->select('log_type')
            ->distinct()
            ->pluck('log_type');

        return Inertia::render('Tenant/ActivityLog/Index', [
            'logs' => $logs,
            'logTypes' => $logTypes,
            'filters' => $request->only(['log_type', 'search', 'date_from', 'date_to']),
        ]);
    }

    /**
     * Show activity log detail
     */
    public function show(ActivityLog $activityLog): Response
    {
        $tenant = auth()->user()->tenant;

        // Verify log belongs to tenant
        if ($activityLog->tenant_id !== $tenant->id) {
            abort(403);
        }

        $activityLog->load(['causer', 'subject']);

        return Inertia::render('Tenant/ActivityLog/Show', [
            'log' => $activityLog,
        ]);
    }

    /**
     * Export activity logs for current tenant
     */
    public function export(Request $request)
    {
        $tenant = auth()->user()->tenant;

        $logs = ActivityLog::where('tenant_id', $tenant->id)
            ->with('causer')
            ->when($request->log_type, function ($query, $type) {
                $query->where('log_type', $type);
            })
            ->when($request->date_from, function ($query, $date) {
                $query->whereDate('created_at', '>=', $date);
            })
            ->when($request->date_to, function ($query, $date) {
                $query->whereDate('created_at', '<=', $date);
            })
            ->orderBy('created_at', 'desc')
            ->get();

        $filename = "activity-logs-{$tenant->slug}-" . now()->format('Y-m-d') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($logs) {
            $file = fopen('php://output', 'w');

            // BOM for Excel UTF-8
            fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));

            // Header
            fputcsv($file, ['ID', 'Tipe', 'Deskripsi', 'User', 'IP Address', 'Waktu']);

            foreach ($logs as $log) {
                fputcsv($file, [
                    $log->id,
                    $log->log_type,
                    $log->description,
                    $log->causer?->name ?? 'System',
                    $log->ip_address,
                    $log->created_at->format('d/m/Y H:i:s'),
                ]);
            }

            fclose($file);
        };

        ActivityLog::log('export', 'Export log aktivitas', null, auth()->user());

        return response()->stream($callback, 200, $headers);
    }
}
