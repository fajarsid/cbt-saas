<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Invoice;
use App\Models\Plan;
use App\Models\Subscription;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class BillingController extends Controller
{
    /**
     * Show billing overview
     */
    public function index(): Response
    {
        $tenant = auth()->user()->tenant;
        $subscription = $tenant->activeSubscription;
        $currentPlan = $tenant->currentPlan;

        return Inertia::render('Tenant/Billing/Index', [
            'subscription' => $subscription?->load('plan'),
            'currentPlan' => $currentPlan,
            'plans' => Plan::active()->ordered()->get(),
            'invoices' => Invoice::where('tenant_id', $tenant->id)
                ->orderBy('created_at', 'desc')
                ->take(10)
                ->get(),
            'usage' => [
                'students' => [
                    'current' => $tenant->students()->count(),
                    'max' => $tenant->max_students,
                    'percentage' => $tenant->getStudentsUsagePercentage(),
                ],
                'exams' => [
                    'current' => $tenant->exams()->count(),
                    'max' => $tenant->max_exams,
                    'percentage' => $tenant->getExamsUsagePercentage(),
                ],
            ],
        ]);
    }

    /**
     * Show available plans for upgrade
     */
    public function plans(): Response
    {
        $tenant = auth()->user()->tenant;

        return Inertia::render('Tenant/Billing/Plans', [
            'currentPlan' => $tenant->currentPlan,
            'plans' => Plan::active()->ordered()->get()->map(function ($plan) {
                return [
                    'id' => $plan->id,
                    'name' => $plan->name,
                    'slug' => $plan->slug,
                    'description' => $plan->description,
                    'price' => $plan->price,
                    'formatted_price' => $plan->formatted_price,
                    'billing_cycle' => $plan->billing_cycle,
                    'billing_cycle_text' => $plan->billing_cycle_text,
                    'max_students' => $plan->max_students,
                    'max_exams' => $plan->max_exams,
                    'max_users' => $plan->max_users,
                    'features' => $plan->getAllFeatures(),
                    'is_featured' => $plan->is_featured,
                    'trial_days' => $plan->trial_days,
                ];
            }),
        ]);
    }

    /**
     * Request plan upgrade (creates pending invoice)
     */
    public function upgradePlan(Request $request)
    {
        $tenant = auth()->user()->tenant;

        $validated = $request->validate([
            'plan_id' => 'required|exists:plans,id',
        ]);

        $plan = Plan::findOrFail($validated['plan_id']);

        // Check if already on this plan
        if ($tenant->current_plan_id === $plan->id) {
            return back()->with('error', 'Anda sudah berlangganan paket ini.');
        }

        // Create subscription if not exists
        $subscription = Subscription::firstOrCreate(
            ['tenant_id' => $tenant->id, 'status' => 'pending'],
            [
                'plan_id' => $plan->id,
                'price' => $plan->price,
                'starts_at' => now(),
                'ends_at' => $plan->billing_cycle === 'monthly'
                    ? now()->addMonth()
                    : now()->addYear(),
            ]
        );

        // Create invoice
        $invoice = Invoice::create([
            'tenant_id' => $tenant->id,
            'subscription_id' => $subscription->id,
            'invoice_number' => Invoice::generateInvoiceNumber(),
            'status' => 'pending',
            'subtotal' => $plan->price,
            'tax' => 0,
            'total' => $plan->price,
            'due_date' => now()->addDays(7),
            'notes' => "Upgrade ke paket {$plan->name}",
        ]);

        ActivityLog::log('billing', "Request upgrade ke paket {$plan->name}", $subscription, auth()->user());

        return redirect()->route('billing.invoice', $invoice->id)
            ->with('success', 'Invoice berhasil dibuat. Silakan lakukan pembayaran.');
    }

    /**
     * Show invoices list
     */
    public function invoices(): Response
    {
        $tenant = auth()->user()->tenant;

        return Inertia::render('Tenant/Billing/Invoices', [
            'invoices' => Invoice::where('tenant_id', $tenant->id)
                ->with('subscription.plan')
                ->orderBy('created_at', 'desc')
                ->paginate(20),
        ]);
    }

    /**
     * Show single invoice
     */
    public function invoice(Invoice $invoice): Response
    {
        $tenant = auth()->user()->tenant;

        // Verify invoice belongs to tenant
        if ($invoice->tenant_id !== $tenant->id) {
            abort(403);
        }

        return Inertia::render('Tenant/Billing/Invoice', [
            'invoice' => $invoice->load(['subscription.plan', 'tenant']),
            'paymentMethods' => [
                ['id' => 'bank_transfer', 'name' => 'Transfer Bank', 'banks' => [
                    ['name' => 'BCA', 'account' => '1234567890', 'holder' => 'PT CBT Indonesia'],
                    ['name' => 'Mandiri', 'account' => '0987654321', 'holder' => 'PT CBT Indonesia'],
                ]],
                ['id' => 'ewallet', 'name' => 'E-Wallet', 'options' => ['GoPay', 'OVO', 'Dana']],
            ],
        ]);
    }

    /**
     * Confirm payment (upload proof)
     */
    public function confirmPayment(Request $request, Invoice $invoice)
    {
        $tenant = auth()->user()->tenant;

        if ($invoice->tenant_id !== $tenant->id) {
            abort(403);
        }

        $validated = $request->validate([
            'payment_method' => 'required|string',
            'payment_reference' => 'nullable|string|max:255',
            'payment_proof' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
        ]);

        // Handle payment proof upload
        if ($request->hasFile('payment_proof')) {
            $path = $request->file('payment_proof')->store('payment-proofs', 'public');
            $validated['payment_reference'] = $path;
        }

        $invoice->update([
            'payment_method' => $validated['payment_method'],
            'payment_reference' => $validated['payment_reference'] ?? $invoice->payment_reference,
            'notes' => ($invoice->notes ?? '') . "\n\nKonfirmasi pembayaran: " . now()->format('d/m/Y H:i'),
        ]);

        ActivityLog::log('billing', "Konfirmasi pembayaran invoice #{$invoice->invoice_number}", $invoice, auth()->user());

        return back()->with('success', 'Konfirmasi pembayaran berhasil dikirim. Admin akan memverifikasi pembayaran Anda.');
    }

    /**
     * Download invoice PDF
     */
    public function downloadInvoice(Invoice $invoice)
    {
        $tenant = auth()->user()->tenant;

        if ($invoice->tenant_id !== $tenant->id) {
            abort(403);
        }

        // For now, return a simple response
        // In production, you would generate a proper PDF
        return response()->json([
            'message' => 'PDF generation not implemented yet',
            'invoice' => $invoice,
        ]);
    }

    /**
     * Cancel subscription
     */
    public function cancelSubscription(Request $request)
    {
        $tenant = auth()->user()->tenant;
        $subscription = $tenant->activeSubscription;

        if (!$subscription) {
            return back()->with('error', 'Tidak ada langganan aktif.');
        }

        $validated = $request->validate([
            'reason' => 'nullable|string|max:500',
        ]);

        $subscription->cancel();

        ActivityLog::log(
            'billing',
            'Membatalkan langganan. Alasan: ' . ($validated['reason'] ?? 'Tidak disebutkan'),
            $subscription,
            auth()->user()
        );

        return back()->with('success', 'Langganan berhasil dibatalkan. Anda masih dapat menggunakan layanan sampai periode berakhir.');
    }
}
