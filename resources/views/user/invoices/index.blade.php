@extends('layouts.dashboard')

@section('content')
<div class="space-y-8">

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h1 class="text-2xl font-semibold tracking-tight text-white">Invoices</h1>
            <p class="text-sm text-white/60 mt-1">Your completed service invoices.</p>
        </div>

        <a href="{{ route('user.dashboard') }}"
           class="rounded-xl border border-white/10 bg-white/5 px-4 py-2 text-sm text-white hover:bg-white/10 transition">
            ← Back to Dashboard
        </a>
    </div>

    @if($invoices->isEmpty())
        <div class="rounded-2xl border border-white/10 bg-white/5 p-10 text-center">
            <div class="text-white font-medium">No invoices yet</div>
            <div class="text-white/50 text-sm mt-2">Invoices appear after a booking is marked Completed.</div>
        </div>
    @else
        <div class="space-y-3">
            @foreach($invoices as $invoice)
                @php
                    $appt = $invoice->appointment;
                    $date = $appt?->service_date ? \Carbon\Carbon::parse($appt->service_date)->format('d M Y') : '—';
                    $time = $appt?->service_time ?? '—';
                    $service = $appt?->service_type ?? 'Service';
                @endphp

                <a href="{{ route('user.invoices.show', $invoice) }}"
                   class="block rounded-2xl border border-white/10 bg-white/5 p-5 hover:bg-white/7 transition">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                        <div>
                            <div class="text-white font-semibold">
                                {{ $invoice->invoice_number }}
                                <span class="text-white/50 font-normal">•</span>
                                <span class="text-white/70 font-normal">{{ $service }}</span>
                            </div>
                            <div class="text-sm text-white/60 mt-1">
                                {{ $date }} • {{ $time }}
                            </div>
                        </div>

                        <div class="text-right">
                            <div class="text-xs text-white/50">Total</div>
                            <div class="text-white font-semibold">
                                ₹{{ number_format($invoice->total_amount, 2) }}
                            </div>
                        </div>
                    </div>
                </a>
            @endforeach
        </div>
    @endif

</div>
@endsection