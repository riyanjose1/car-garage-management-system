<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Invoice;

class InvoiceController extends Controller
{
    public function index()
    {
        $invoices = Invoice::with('appointment')
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

        return view('user.invoices.show', compact('invoice'));
    }
}
