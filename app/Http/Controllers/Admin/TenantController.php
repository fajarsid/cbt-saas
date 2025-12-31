<?php

namespace App\Http\Controllers\Admin;

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class TenantController extends Controller
{
    public function index()
    {
        $tenants = Tenant::when(request()->q, function($query) {
            $query->where('name', 'like', '%'. request()->q . '%')
                  ->orWhere('email', 'like', '%'. request()->q . '%');
        })
        ->withCount(['students', 'exams', 'examSessions', 'users'])
        ->latest()
        ->paginate(10);

        $tenants->appends(['q' => request()->q]);

        return inertia('Admin/Tenants/Index', [
            'tenants' => $tenants,
        ]);
    }

    public function create()
    {
        return inertia('Admin/Tenants/Create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:tenants',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'status' => 'required|in:active,inactive,suspended',
            'plan' => 'required|in:free,basic,premium,enterprise',
            'max_students' => 'required|integer|min:1',
            'max_exams' => 'required|integer|min:1',
            // Admin user fields
            'admin_name' => 'required|string|max:255',
            'admin_email' => 'required|email|unique:users,email',
            'admin_password' => 'required|string|min:6',
        ]);

        DB::transaction(function () use ($request) {
            // Create tenant
            $tenant = Tenant::create([
                'name' => $request->name,
                'slug' => \Str::slug($request->name) . '-' . \Str::random(5),
                'email' => $request->email,
                'phone' => $request->phone,
                'address' => $request->address,
                'status' => $request->status,
                'plan' => $request->plan,
                'max_students' => $request->max_students,
                'max_exams' => $request->max_exams,
            ]);

            // Create admin user for tenant
            User::create([
                'tenant_id' => $tenant->id,
                'name' => $request->admin_name,
                'email' => $request->admin_email,
                'password' => $request->admin_password,
            ]);
        });

        return redirect()->route('admin.tenants.index');
    }

    public function show(Tenant $tenant)
    {
        // Load counts
        $stats = [
            'students_count' => $tenant->students()->count(),
            'exams_count' => $tenant->exams()->count(),
            'exam_sessions_count' => $tenant->examSessions()->count(),
            'classrooms_count' => $tenant->classrooms()->count(),
            'lessons_count' => $tenant->lessons()->count(),
            'users_count' => $tenant->users()->count(),
        ];

        // Get upcoming exam sessions
        $upcomingExams = $tenant->examSessions()
            ->with(['exam.lesson', 'exam.classroom'])
            ->where('start_time', '>', now())
            ->orderBy('start_time')
            ->limit(5)
            ->get();

        // Get admin users
        $admins = $tenant->users()->latest()->get();

        return inertia('Admin/Tenants/Show', [
            'tenant' => $tenant,
            'stats' => $stats,
            'upcomingExams' => $upcomingExams,
            'admins' => $admins,
        ]);
    }

    public function edit(Tenant $tenant)
    {
        return inertia('Admin/Tenants/Edit', [
            'tenant' => $tenant,
        ]);
    }

    public function update(Request $request, Tenant $tenant)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:tenants,email,' . $tenant->id,
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'status' => 'required|in:active,inactive,suspended',
            'plan' => 'required|in:free,basic,premium,enterprise',
            'max_students' => 'required|integer|min:1',
            'max_exams' => 'required|integer|min:1',
        ]);

        $tenant->update([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'address' => $request->address,
            'status' => $request->status,
            'plan' => $request->plan,
            'max_students' => $request->max_students,
            'max_exams' => $request->max_exams,
        ]);

        return redirect()->route('admin.tenants.index');
    }

    public function destroy(Tenant $tenant)
    {
        $tenant->delete();

        return redirect()->route('admin.tenants.index');
    }

    /**
     * Impersonate a tenant's admin user
     */
    public function impersonate(Tenant $tenant)
    {
        // Get first admin user of this tenant
        $tenantAdmin = $tenant->users()->first();

        if (!$tenantAdmin) {
            return back()->with('error', 'Tenant tidak memiliki admin user.');
        }

        // Store original admin id in session
        session()->put('impersonator_id', Auth::id());
        session()->put('impersonating_tenant_id', $tenant->id);

        // Login as tenant admin
        Auth::login($tenantAdmin);

        return redirect()->route('admin.dashboard');
    }

    /**
     * Stop impersonating and return to super admin
     */
    public function stopImpersonate()
    {
        $impersonatorId = session()->get('impersonator_id');

        if ($impersonatorId) {
            // Get the original admin
            $originalAdmin = User::find($impersonatorId);

            if ($originalAdmin) {
                // Clear impersonation session
                session()->forget('impersonator_id');
                session()->forget('impersonating_tenant_id');

                // Login back as original admin
                Auth::login($originalAdmin);

                return redirect()->route('admin.tenants.index');
            }
        }

        return redirect()->route('admin.dashboard');
    }
}
