<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class PlanController extends Controller
{
    /**
     * Display a listing of plans.
     */
    public function index(): Response
    {
        $plans = Plan::withCount('subscriptions')
            ->orderBy('price')
            ->get();

        return Inertia::render('Admin/Plans/Index', [
            'plans' => $plans,
        ]);
    }

    /**
     * Show the form for creating a new plan.
     */
    public function create(): Response
    {
        return Inertia::render('Admin/Plans/Create');
    }

    /**
     * Store a newly created plan.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:plans',
            'description' => 'nullable|string|max:1000',
            'price' => 'required|numeric|min:0',
            'billing_cycle' => 'required|in:monthly,yearly',
            'max_students' => 'required|integer|min:1',
            'max_exams' => 'required|integer|min:1',
            'max_users' => 'required|integer|min:1',
            'features' => 'nullable|array',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
            'trial_days' => 'integer|min:0',
        ]);

        $validated['is_active'] = $validated['is_active'] ?? true;
        $validated['is_featured'] = $validated['is_featured'] ?? false;
        $validated['trial_days'] = $validated['trial_days'] ?? 14;

        Plan::create($validated);

        return redirect()->route('admin.plans.index')
            ->with('success', 'Paket berhasil dibuat.');
    }

    /**
     * Show the form for editing a plan.
     */
    public function edit(Plan $plan): Response
    {
        return Inertia::render('Admin/Plans/Edit', [
            'plan' => $plan,
        ]);
    }

    /**
     * Update the specified plan.
     */
    public function update(Request $request, Plan $plan)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:plans,slug,' . $plan->id,
            'description' => 'nullable|string|max:1000',
            'price' => 'required|numeric|min:0',
            'billing_cycle' => 'required|in:monthly,yearly',
            'max_students' => 'required|integer|min:1',
            'max_exams' => 'required|integer|min:1',
            'max_users' => 'required|integer|min:1',
            'features' => 'nullable|array',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
            'trial_days' => 'integer|min:0',
        ]);

        $plan->update($validated);

        return redirect()->route('admin.plans.index')
            ->with('success', 'Paket berhasil diperbarui.');
    }

    /**
     * Remove the specified plan.
     */
    public function destroy(Plan $plan)
    {
        if ($plan->subscriptions()->exists()) {
            return back()->with('error', 'Tidak dapat menghapus paket yang masih memiliki subscription aktif.');
        }

        $plan->delete();

        return redirect()->route('admin.plans.index')
            ->with('success', 'Paket berhasil dihapus.');
    }
}
