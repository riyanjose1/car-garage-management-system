@extends('layouts.dashboard')

@section('title', 'Admin')
@section('subtitle', 'Manage users, roles, and platform health.')

@section('content')
<div class="space-y-8">

    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-semibold tracking-tight text-white">Admin Dashboard</h1>
            <p class="text-sm text-white/60 mt-1">User management + platform stats.</p>
        </div>
    </div>

    @if(session('success'))
        <div class="rounded-xl border border-emerald-400/20 bg-emerald-500/10 px-4 py-3 text-sm text-emerald-200">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="rounded-xl border border-red-400/20 bg-red-500/10 px-4 py-3 text-sm text-red-200">
            <ul class="list-disc pl-5 space-y-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Metrics --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="rounded-2xl border border-white/10 bg-white/5 p-5">
            <div class="text-xs text-white/50">Users</div>
            <div class="mt-2 text-2xl font-semibold text-white">{{ $stats['users_total'] }}</div>
            <div class="mt-1 text-xs text-white/40">{{ $stats['customers'] }} customers</div>
        </div>

        <div class="rounded-2xl border border-white/10 bg-white/5 p-5">
            <div class="text-xs text-white/50">Admins</div>
            <div class="mt-2 text-2xl font-semibold text-white">{{ $stats['admins'] }}</div>
            <div class="mt-1 text-xs text-white/40">{{ $stats['moderators'] }} moderators</div>
        </div>

        <div class="rounded-2xl border border-white/10 bg-white/5 p-5">
            <div class="text-xs text-white/50">Appointments</div>
            <div class="mt-2 text-2xl font-semibold text-white">{{ $stats['appointments'] }}</div>
            <div class="mt-1 text-xs text-white/40">{{ $stats['vehicles'] }} vehicles</div>
        </div>

        <div class="rounded-2xl border border-white/10 bg-white/5 p-5">
            <div class="text-xs text-white/50">Invoices</div>
            <div class="mt-2 text-2xl font-semibold text-white">{{ $stats['invoices'] }}</div>
            <div class="mt-1 text-xs text-white/40">Generated on completion</div>
        </div>
    </div>

    {{-- Profit Chart --}}
    <div class="rounded-2xl border border-white/10 bg-white/5 p-6">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between mb-4">
            <div>
                <h2 class="text-lg font-semibold text-white">Profit & Loss (Per User)</h2>
                <p class="text-xs text-white/50 mt-1">
                    Profit is calculated from total invoice amount per user. Loss is shown as 0 for now.
                </p>
            </div>

            <button
                type="button"
                onclick="downloadProfitChart()"
                class="rounded-xl bg-white px-4 py-2 text-sm font-medium text-black hover:bg-white/90 transition"
            >
                Download Chart
            </button>

            <a href="{{ route('admin.profit.export') }}"
                class="rounded-xl border border-white/10 bg-white/5 px-4 py-2 text-sm text-white hover:bg-white/10 transition">
                Download Excel
            </a>
        </div>

        <div class="overflow-x-auto">
            <canvas id="profitChart" height="120"></canvas>
        </div>
    </div>

    {{-- Users --}}
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <h2 class="text-lg font-semibold text-white">Users</h2>

        <form method="GET" action="{{ route('admin.dashboard') }}" class="flex gap-2 w-full sm:w-[420px]">
            <input
                name="q"
                value="{{ $q ?? '' }}"
                placeholder="Search name, email, role..."
                class="w-full rounded-xl border border-white/10 bg-white/5 px-4 py-2 text-sm text-white placeholder:text-white/30
                       focus:outline-none focus:ring-2 focus:ring-white/10"
            >
            <button class="rounded-xl bg-white px-4 py-2 text-sm font-medium text-black hover:bg-white/90 transition">
                Search
            </button>
        </form>
    </div>

    <div class="rounded-2xl border border-white/10 bg-white/5 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-black/20 text-white/60">
                    <tr>
                        <th class="text-left font-medium px-5 py-4">Name</th>
                        <th class="text-left font-medium px-5 py-4">Email</th>
                        <th class="text-left font-medium px-5 py-4">Role</th>
                        <th class="text-right font-medium px-5 py-4">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/10">
                    @foreach($users as $u)
                        <tr class="hover:bg-white/5 transition">
                            <td class="px-5 py-4 text-white font-medium">
                                {{ $u->name }}
                                @if(auth()->id() === $u->id)
                                    <span class="ml-2 text-xs text-white/40">(you)</span>
                                @endif
                            </td>
                            <td class="px-5 py-4 text-white/70">{{ $u->email }}</td>
                            <td class="px-5 py-4">
                                @php
                                    $r = $u->role ?? 'user';
                                    $badge = match($r) {
                                        'admin' => 'border-emerald-400/20 bg-emerald-500/10 text-emerald-200',
                                        'moderator' => 'border-sky-400/20 bg-sky-500/10 text-sky-200',
                                        default => 'border-white/10 bg-white/5 text-white/70',
                                    };
                                @endphp
                                <span class="inline-flex rounded-xl border px-3 py-1.5 text-xs {{ $badge }}">
                                    {{ ucfirst($r) }}
                                </span>
                            </td>
                            <td class="px-5 py-4">
                                <form method="POST" action="{{ route('admin.users.role', $u->id) }}" class="flex justify-end gap-2">
                                    @csrf
                                    <select name="role"
                                            class="rounded-xl border border-white/10 bg-white/5 px-3 py-2 text-sm text-white focus:outline-none focus:ring-2 focus:ring-white/10">
                                        @foreach(['user' => 'User', 'moderator' => 'Moderator', 'admin' => 'Admin'] as $val => $label)
                                            <option value="{{ $val }}" class="bg-[#0B0B0F]" @selected(($u->role ?? 'user') === $val)>{{ $label }}</option>
                                        @endforeach
                                    </select>

                                    <button class="rounded-xl bg-white px-4 py-2 text-sm font-medium text-black hover:bg-white/90 transition">
                                        Update
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if($users->hasPages())
            <div class="border-t border-white/10 p-4">
                {{ $users->onEachSide(1)->links('vendor.pagination.autoserve') }}
            </div>
        @endif
    </div>

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

    const labels = chartData.map(r => r.month);         // X axis = Month
    const profitData = chartData.map(r => Number(r.profit || 0));   // Profit line
    const lossData = chartData.map(r => Number((r.revenue || 0) - (r.profit || 0))); // Loss line (cost)

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
        link.download = 'admin-profit-loss-monthly.png';
        link.href = canvas.toDataURL('image/png', 1.0);
        link.click();
    };
})();
</script>
@endsection