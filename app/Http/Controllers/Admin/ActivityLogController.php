<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ActivityLogController extends Controller
{
    /**
     * Display activity logs for admin (all tenants)
     */
    public function index(Request $request): Response
    {
        $logs = ActivityLog::with(['tenant', 'causer'])
            ->when($request->tenant_id, function ($query, $tenantId) {
                $query->where('tenant_id', $tenantId);
            })
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
            ->paginate(50);

        return Inertia::render('Admin/ActivityLogs/Index', [
            'logs' => $logs,
            'tenants' => Tenant::select('id', 'name')->get(),
            'logTypes' => ActivityLog::select('log_type')->distinct()->pluck('log_type'),
            'filters' => $request->only(['tenant_id', 'log_type', 'date_from', 'date_to']),
        ]);
    }

    /**
     * Show activity log detail
     */
    public function show(ActivityLog $activityLog): Response
    {
        $activityLog->load(['tenant', 'subject', 'causer']);

        return Inertia::render('Admin/ActivityLogs/Show', [
            'log' => $activityLog,
        ]);
    }

    /**
     * Export activity logs
     */
    public function export(Request $request)
    {
        $logs = ActivityLog::with(['tenant', 'causer'])
            ->when($request->tenant_id, function ($query, $tenantId) {
                $query->where('tenant_id', $tenantId);
            })
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

        $filename = 'activity-logs-' . now()->format('Y-m-d-His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($logs) {
            $file = fopen('php://output', 'w');

            // Header
            fputcsv($file, ['ID', 'Tenant', 'Type', 'Description', 'Causer', 'IP Address', 'Created At']);

            foreach ($logs as $log) {
                fputcsv($file, [
                    $log->id,
                    $log->tenant?->name ?? 'System',
                    $log->log_type,
                    $log->description,
                    $log->causer?->name ?? 'System',
                    $log->ip_address,
                    $log->created_at->format('Y-m-d H:i:s'),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
