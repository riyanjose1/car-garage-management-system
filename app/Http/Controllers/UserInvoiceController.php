<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class UserInvoiceController extends Controller
{
    public function index()
    {
        $invoices = Invoice::with(['appointment.vehicle'])
            ->where('user_id', auth()->id())
            ->latest()
            ->get();

        return view('user.invoices.index', compact('invoices'));
    }

    public function show(Invoice $invoice)
    {
        if ($invoice->user_id !== auth()->id()) {
            abort(403);
        }

        $invoice->load(['appointment.vehicle', 'appointment.user']);

        return view('user.invoices.show', compact('invoice'));
    }

    public function downloadPdf(Invoice $invoice)
    {
        // Security check
        if ($invoice->user_id !== auth()->id()) {
            abort(403);
        }

        $invoice->load(['appointment.vehicle', 'appointment.user']);

        $pdf = Pdf::loadView('pdf.invoice', [
            'invoice' => $invoice,
        ])->setPaper('a4', 'portrait');

        $fileName = $invoice->invoice_number . '.pdf';

        return $pdf->download($fileName);
    }
}