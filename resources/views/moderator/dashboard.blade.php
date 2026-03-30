@extends('layouts.dashboard')

@section('title', 'Dashboard')
@section('subtitle', 'Review and manage service requests.')

@section('content')
@php
    $stats = $stats ?? [
        'total' => 0,
        'pending' => 0,
        'confirmed' => 0,
        'completed' => 0,
        'cancelled' => 0,
    ];

    $latest = $latest ?? collect();
@endphp

<div class="space-y-8">

    {{-- Header --}}
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-semibold tracking-tight text-white">Moderator Dashboard</h1>
            <p class="text-sm text-white/60 mt-1">Manage bookings, update statuses, and view schedules.</p>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('moderator.appointments.index') }}"
               class="rounded-xl border border-white/10 bg-white/5 px-4 py-2 text-sm text-white hover:bg-white/10 transition">
                Bookings
            </a>

            <a href="{{ route('moderator.calendar') }}"
               class="rounded-xl bg-white px-4 py-2 text-sm font-medium text-black hover:bg-white/90 transition">
                Schedule
            </a>
        </div>
    </div>

    {{-- Metrics --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
        @foreach([
            'Total' => $stats['total'],
            'Pending' => $stats['pending'],
            'Confirmed' => $stats['confirmed'],
            'Completed' => $stats['completed'],
            'Cancelled' => $stats['cancelled']
        ] as $label => $value)
            <div class="rounded-2xl border border-white/10 bg-white/5 p-5">
                <div class="text-xs text-white/50">{{ $label }}</div>
                <div class="mt-2 text-2xl font-semibold text-white">{{ $value }}</div>
            </div>
        @endforeach
    </div>

    {{-- PROFIT CHART --}}
    <div class="rounded-2xl border border-white/10 bg-white/5 p-6">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h2 class="text-lg font-semibold text-white">Profit & Loss Overview</h2>
                <p class="text-xs text-white/50 mt-1">Calculated from completed service invoices</p>
            </div>

            <button onclick="downloadProfitChart()"
                class="rounded-xl bg-white px-4 py-2 text-sm font-medium text-black hover:bg-white/90 transition">
                Download Chart
            </button>

            <a href="{{ route('moderator.profit.export') }}"
                class="rounded-xl border border-white/10 bg-white/5 px-4 py-2 text-sm text-white hover:bg-white/10 transition">
                Download Excel
            </a>
        </div>

        <canvas id="profitChart" height="120"></canvas>
    </div>

    {{-- Latest --}}
<div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
    <div class="flex items-center justify-between sm:justify-start gap-4">
        <h2 class="text-lg font-semibold text-white">Latest Bookings</h2>
        <a href="{{ route('moderator.appointments.index') }}"
           class="text-sm text-white/60 hover:text-white transition">
            View all →
        </a>
    </div>

    <form method="GET" action="{{ route('moderator.dashboard') }}" class="flex gap-2 w-full sm:w-[420px]">
        <input
            name="q"
            value="{{ $q ?? '' }}"
            placeholder="Search customer, vehicle, plate, service..."
            class="w-full rounded-xl border border-white/10 bg-white/5 px-4 py-2 text-sm text-white placeholder:text-white/30
                   focus:outline-none focus:ring-2 focus:ring-white/10"
        >
        <button class="rounded-xl bg-white px-4 py-2 text-sm font-medium text-black hover:bg-white/90 transition">
            Search
        </button>
    </form>
</div>

    @if($latest->isEmpty())
        <div class="rounded-2xl border border-white/10 bg-white/5 p-8 text-center">
            <div class="text-white font-medium">No bookings yet</div>
        </div>
    @else
        <div class="space-y-3">
            @foreach($latest as $a)
                <div class="rounded-2xl border border-white/10 bg-white/5 p-5">
                    <div class="text-white font-semibold">
                        {{ $a->service_type }} • {{ optional($a->vehicle)->brand }} {{ optional($a->vehicle)->model }}
                    </div>
                    <div class="text-sm text-white/60 mt-1">
                        {{ optional($a->user)->name }} • {{ $a->service_date }} • {{ $a->service_time }}
                    </div>
                </div>
            @endforeach
        </div>
    @endif

</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
(function () {
    const canvas = document.getElementById('profitChart');
    if (!canvas) return;

    // Monthly data from controller
    const chartData = @json($chartData ?? []);

    const labels = chartData.map(r => r.month); // X axis = Month
    const profitData = chartData.map(r => Number(r.profit || 0));
    const lossData = chartData.map(r => Number((r.revenue || 0) - (r.profit || 0))); // cost as loss

    const chart = new Chart(canvas.getContext('2d'), {
        type: 'line',
        data: {
            labels,
            datasets: [
                {
                    label: 'Profit (₹)',
                    data: profitData,
                    borderColor: '#22c55e',
                    backgroundColor: 'rgba(34,197,94,0.12)',
                    tension: 0.35,
                    fill: true
                },
                {
                    label: 'Loss (₹)',
                    data: lossData,
                    borderColor: '#ef4444',
                    backgroundColor: 'rgba(239,68,68,0.08)',
                    tension: 0.35,
                    fill: false
                }
            ]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { labels: { color: '#ffffff' } },
                tooltip: {
                    callbacks: {
                        label: (ctx) => `${ctx.dataset.label}: ₹${Number(ctx.raw || 0).toFixed(2)}`
                    }
                }
            },
            scales: {
                x: { ticks: { color: '#ffffff' }, title: { display: true, text: 'Month', color: '#ffffff' } },
                y: { ticks: { color: '#ffffff' }, title: { display: true, text: 'Amount (₹)', color: '#ffffff' }, beginAtZero: true }
            }
        }
    });

    window.downloadProfitChart = function () {
        const link = document.createElement('a');
        link.download = 'moderator-profit-loss-monthly.png';
        link.href = canvas.toDataURL('image/png', 1.0);
        link.click();
    };
})();
</script>
@endsection
