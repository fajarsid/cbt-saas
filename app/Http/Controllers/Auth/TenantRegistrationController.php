<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;
use Inertia\Inertia;
use Inertia\Response;

class TenantRegistrationController extends Controller
{
    public function create(): Response
    {
        return Inertia::render('Auth/TenantRegister');
    }

    public function store(Request $request)
    {
        $request->validate([
            'organization_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:tenants,email'],
            'admin_name' => ['required', 'string', 'max:255'],
            'admin_email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', Password::defaults()],
            'phone' => ['nullable', 'string', 'max:20'],
        ]);

        try {
            DB::beginTransaction();

            // Create tenant
            $tenant = Tenant::create([
                'name' => $request->organization_name,
                'slug' => Str::slug($request->organization_name) . '-' . Str::random(5),
                'email' => $request->email,
                'phone' => $request->phone,
                'status' => 'active',
                'plan' => 'free',
                'max_students' => 50,
                'max_exams' => 10,
                'trial_ends_at' => now()->addDays(14),
            ]);

            // Get tenant admin role
            $tenantAdminRole = Role::tenantAdmin();

            // Create admin user
            $user = User::create([
                'tenant_id' => $tenant->id,
                'role_id' => $tenantAdminRole?->id,
                'name' => $request->admin_name,
                'email' => $request->admin_email,
                'password' => Hash::make($request->password),
            ]);

            DB::commit();

            // Log in the user
            auth()->login($user);

            return redirect()->route('admin.dashboard')
                ->with('success', 'Selamat! Organisasi Anda berhasil didaftarkan.');

        } catch (\Exception $e) {
            DB::rollBack();
            
            return back()->with('error', 'Terjadi kesalahan saat mendaftarkan organisasi. Silakan coba lagi.');
        }
    }
}
