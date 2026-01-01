<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class SubscriptionController extends Controller
{
    /**
     * Display a listing of subscriptions.
     */
    public function index(Request $request): Response
    {
        $subscriptions = Subscription::with(['tenant', 'plan'])
            ->when($request->status, function ($query, $status) {
                $query->where('status', $status);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return Inertia::render('Admin/Subscriptions/Index', [
            'subscriptions' => $subscriptions,
            'filters' => $request->only(['status']),
        ]);
    }

    /**
     * Create subscription for a tenant
     */
    public function create(): Response
    {
        return Inertia::render('Admin/Subscriptions/Create', [
            'tenants' => Tenant::where('status', 'active')->get(),
            'plans' => Plan::where('is_active', true)->get(),
        ]);
    }

    /**
     * Store a new subscription
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'tenant_id' => 'required|exists:tenants,id',
            'plan_id' => 'required|exists:plans,id',
            'starts_at' => 'required|date',
            'ends_at' => 'required|date|after:starts_at',
            'notes' => 'nullable|string',
        ]);

        $plan = Plan::findOrFail($validated['plan_id']);
        $tenant = Tenant::findOrFail($validated['tenant_id']);

        // Create subscription
        $subscription = Subscription::create([
            'tenant_id' => $validated['tenant_id'],
            'plan_id' => $validated['plan_id'],
            'status' => 'active',
            'starts_at' => $validated['starts_at'],
            'ends_at' => $validated['ends_at'],
            'price' => $plan->price,
        ]);

        // Update tenant quotas
        $tenant->update([
            'current_plan_id' => $plan->id,
            'max_students' => $plan->max_students,
            'max_exams' => $plan->max_exams,
            'plan' => $plan->slug,
        ]);

        // Create invoice
        Invoice::create([
            'tenant_id' => $tenant->id,
            'subscription_id' => $subscription->id,
            'invoice_number' => Invoice::generateInvoiceNumber(),
            'status' => 'pending',
            'subtotal' => $plan->price,
            'tax' => 0,
            'total' => $plan->price,
            'currency' => 'IDR',
            'notes' => $validated['notes'] ?? null,
            'due_date' => now()->addDays(7),
        ]);

        return redirect()->route('admin.subscriptions.index')
            ->with('success', 'Subscription berhasil dibuat.');
    }

    /**
     * Show subscription details
     */
    public function show(Subscription $subscription): Response
    {
        $subscription->load(['tenant', 'plan', 'invoices']);

        return Inertia::render('Admin/Subscriptions/Show', [
            'subscription' => $subscription,
        ]);
    }

    /**
     * Cancel subscription
     */
    public function cancel(Subscription $subscription)
    {
        $subscription->cancel();

        return back()->with('success', 'Subscription berhasil dibatalkan.');
    }

    /**
     * Renew subscription
     */
    public function renew(Request $request, Subscription $subscription)
    {
        $validated = $request->validate([
            'ends_at' => 'required|date|after:today',
        ]);

        $subscription->renew($validated['ends_at']);

        // Create new invoice
        Invoice::create([
            'tenant_id' => $subscription->tenant_id,
            'subscription_id' => $subscription->id,
            'invoice_number' => Invoice::generateInvoiceNumber(),
            'status' => 'pending',
            'subtotal' => $subscription->price,
            'tax' => 0,
            'total' => $subscription->price,
            'currency' => 'IDR',
            'due_date' => now()->addDays(7),
        ]);

        return back()->with('success', 'Subscription berhasil diperpanjang.');
    }
}
