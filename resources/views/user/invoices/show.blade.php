@extends('layouts.dashboard')

@section('content')
<div class="space-y-8">

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h1 class="text-2xl font-semibold tracking-tight text-white">Invoice</h1>
            <p class="text-sm text-white/60 mt-1">{{ $invoice->invoice_number }}</p>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('user.invoices.index') }}"
                class="rounded-xl border border-white/10 bg-white/5 px-4 py-2 text-sm text-white hover:bg-white/10 transition">
                ← Back
            </a>

            <a href="{{ route('user.invoices.pdf', $invoice) }}"
                class="rounded-xl bg-white px-4 py-2 text-sm font-medium text-black hover:bg-white/90 transition">
                Download PDF
            </a>

            <button onclick="window.print()"
                    class="rounded-xl border border-white/10 bg-white/5 px-4 py-2 text-sm text-white hover:bg-white/10 transition">
                Print
            </button>
        </div>
    </div>

    @php
        $appt = $invoice->appointment;
        $date = $appt?->service_date ? \Carbon\Carbon::parse($appt->service_date)->format('d M Y') : '—';
        $time = $appt?->service_time ?? '—';
        $service = $appt?->service_type ?? 'Service';
        $vehicle = $appt?->vehicle ? trim(($appt->vehicle->brand ?? '').' '.($appt->vehicle->model ?? '')) : '—';
        $plate = $appt?->vehicle?->plate_number ?? '';
        $customer = auth()->user()->name;
    @endphp

    <div class="rounded-2xl border border-white/10 bg-white/5 p-6">
        <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-6">
            <div>
                <div class="text-white font-semibold text-lg">AutoServe</div>
                <div class="text-sm text-white/60 mt-1">Garage Management System</div>

                <div class="mt-4 text-sm text-white/70">
                    <div><span class="text-white/40">Billed To:</span> {{ $customer }}</div>
                    <div><span class="text-white/40">Service:</span> {{ $service }}</div>
                    <div><span class="text-white/40">Date/Time:</span> {{ $date }} • {{ $time }}</div>
                    <div>
                        <span class="text-white/40">Vehicle:</span>
                        {{ $vehicle }} @if($plate) • {{ $plate }} @endif
                    </div>
                </div>
            </div>

            <div class="rounded-2xl border border-white/10 bg-black/20 p-5 min-w-[240px]">
                <div class="text-xs text-white/50">Invoice Number</div>
                <div class="mt-1 text-white font-semibold">{{ $invoice->invoice_number }}</div>

                <div class="mt-4 text-xs text-white/50">Total Amount</div>
                <div class="mt-1 text-2xl font-semibold text-white">
                    ₹{{ number_format($invoice->total_amount, 2) }}
                </div>

                <div class="mt-3 text-xs text-white/40">
                    Generated: {{ $invoice->created_at->format('d M Y') }}
                </div>
            </div>
        </div>

        <div class="mt-6 border-t border-white/10 pt-6">
            <div class="text-white font-semibold mb-3">Charges</div>

            <div class="space-y-2 text-sm">
                <div class="flex items-center justify-between">
                    <div class="text-white/70">Labor</div>
                    <div class="text-white">₹{{ number_format($invoice->labor_cost, 2) }}</div>
                </div>

                <div class="flex items-center justify-between">
                    <div class="text-white/70">Parts</div>
                    <div class="text-white">₹{{ number_format($invoice->parts_cost, 2) }}</div>
                </div>

                <div class="border-t border-white/10 pt-3 flex items-center justify-between">
                    <div class="text-white font-semibold">Total</div>
                    <div class="text-white font-semibold">₹{{ number_format($invoice->total_amount, 2) }}</div>
                </div>
            </div>

            @if($invoice->notes)
                <div class="mt-6 rounded-2xl border border-white/10 bg-black/20 p-4">
                    <div class="text-xs text-white/50">Notes</div>
                    <div class="mt-2 text-sm text-white/70">{{ $invoice->notes }}</div>
                </div>
            @endif
        </div>
    </div>

    {{-- Print polish --}}
    <style>
        @media print {
            body { background: white !important; }
            aside, header, button, a { display: none !important; }
            main, section { padding: 0 !important; }
            .bg-slate-950 { background: white !important; }
        }
    </style>

</div>
@endsection