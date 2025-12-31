<?php

namespace App\Http\Controllers\Admin;

use App\Models\Tenant;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class TenantController extends Controller
{
    public function index()
    {
        $tenants = Tenant::when(request()->q, function($query) {
            $query->where('name', 'like', '%'. request()->q . '%')
                  ->orWhere('email', 'like', '%'. request()->q . '%');
        })->latest()->paginate(10);

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
        ]);

        Tenant::create([
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

        return redirect()->route('admin.tenants.index');
    }

    public function show(Tenant $tenant)
    {
        $tenant->load(['users', 'students']);

        return inertia('Admin/Tenants/Show', [
            'tenant' => $tenant,
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
}
