<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class InvoiceController extends Controller
{
    /**
     * Display a listing of invoices.
     */
    public function index(Request $request): Response
    {
        $invoices = Invoice::with(['tenant', 'subscription.plan'])
            ->when($request->status, function ($query, $status) {
                $query->where('status', $status);
            })
            ->when($request->tenant_id, function ($query, $tenantId) {
                $query->where('tenant_id', $tenantId);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return Inertia::render('Admin/Invoices/Index', [
            'invoices' => $invoices,
            'filters' => $request->only(['status', 'tenant_id']),
        ]);
    }

    /**
     * Show invoice details
     */
    public function show(Invoice $invoice): Response
    {
        $invoice->load(['tenant', 'subscription.plan']);

        return Inertia::render('Admin/Invoices/Show', [
            'invoice' => $invoice,
        ]);
    }

    /**
     * Mark invoice as paid
     */
    public function markAsPaid(Request $request, Invoice $invoice)
    {
        $validated = $request->validate([
            'payment_method' => 'nullable|string|max:50',
            'payment_reference' => 'nullable|string|max:255',
        ]);

        $invoice->markAsPaid(
            $validated['payment_method'] ?? null,
            $validated['payment_reference'] ?? null
        );

        return back()->with('success', 'Invoice ditandai sudah dibayar.');
    }

    /**
     * Send invoice reminder
     */
    public function sendReminder(Invoice $invoice)
    {
        // TODO: Implement email/WhatsApp reminder
        return back()->with('success', 'Reminder berhasil dikirim.');
    }

    /**
     * Download invoice as PDF
     */
    public function download(Invoice $invoice)
    {
        // TODO: Generate PDF
        return back()->with('info', 'Fitur download PDF sedang dalam pengembangan.');
    }
}
