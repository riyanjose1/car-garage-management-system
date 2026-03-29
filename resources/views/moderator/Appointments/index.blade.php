@extends('layouts.dashboard')

@section('content')
<div class="space-y-8">

    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between mb-4">
    <h1 class="text-2xl font-semibold text-white">Bookings</h1>

    <form method="GET"
          action="{{ route('moderator.appointments.index') }}"
          class="flex gap-2 w-full sm:w-[420px]">

        <input
            type="text"
            name="q"
            value="{{ $q ?? '' }}"
            placeholder="Search user, vehicle, plate, status..."
            class="w-full rounded-xl border border-white/10 bg-white/5 px-4 py-2 text-sm text-white
                   placeholder:text-white/30 focus:outline-none focus:ring-2 focus:ring-white/10"
        >

        <button
            class="rounded-xl bg-white px-4 py-2 text-sm font-medium text-black hover:bg-white/90 transition">
            Search
        </button>
    </form>
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

    {{-- List --}}
    @if($appointments->isEmpty())
        <div class="rounded-2xl border border-white/10 bg-white/5 p-8 text-center">
            <div class="text-white font-medium">No bookings found</div>
        </div>
    @else
        <div class="space-y-4">
            @foreach($appointments as $appointment)

                @php
                    $statusVal = $appointment->status ?? 'Pending';
                    $badgeClass = match (strtolower($statusVal)) {
                        'confirmed' => 'border-emerald-400/20 bg-emerald-500/10 text-emerald-200',
                        'completed' => 'border-sky-400/20 bg-sky-500/10 text-sky-200',
                        'cancelled', 'canceled' => 'border-red-400/20 bg-red-500/10 text-red-200',
                        default => 'border-white/10 bg-white/5 text-white/70',
                    };

                    $hasInvoice = $appointment->invoice ? true : false;
                    $invoiceId = $appointment->invoice?->id;
                @endphp

                <div class="rounded-2xl border border-white/10 bg-white/5 p-6">

                    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">

                        <div>
                            <div class="text-white font-semibold">
                                {{ $appointment->service_type }}
                                <span class="text-white/50">•</span>
                                {{ optional($appointment->vehicle)->brand }}
                                {{ optional($appointment->vehicle)->model }}
                            </div>

                            <div class="text-sm text-white/60 mt-1">
                                {{ optional($appointment->user)->name }}
                                • {{ \Carbon\Carbon::parse($appointment->service_date)->format('d M Y') }}
                                • {{ $appointment->service_time }}
                            </div>
                        </div>

                        <div class="flex flex-col gap-3">

                            <span class="inline-flex rounded-xl border px-3 py-1.5 text-xs {{ $badgeClass }}">
                                {{ $statusVal }}
                            </span>

                            <form method="POST"
                                  action="{{ route('moderator.appointments.status', $appointment) }}"
                                  class="statusForm flex flex-col gap-3"
                                  data-has-invoice="{{ $hasInvoice ? '1' : '0' }}"
                            >
                                @csrf
                                @method('PATCH')

                                <div class="flex gap-2">
                                    <select name="status"
                                            class="statusSelect rounded-xl border border-white/10 bg-white/5 px-3 py-2 text-sm text-white">
                                        @foreach(['Pending','Confirmed','Completed','Cancelled'] as $s)
                                            <option value="{{ $s }}" @selected($statusVal === $s)>{{ $s }}</option>
                                        @endforeach
                                    </select>

                                    <button class="rounded-xl bg-white px-4 py-2 text-sm font-medium text-black hover:bg-white/90 transition">
                                        Update
                                    </button>
                                </div>

                                {{-- Invoice Box --}}
                                <div class="invoiceBox hidden rounded-2xl border border-white/10 bg-black/20 p-4">
                                    <div class="text-white font-semibold text-sm mb-3">
                                        Invoice Details
                                    </div>

                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <label class="text-xs text-white/50">Labor Cost</label>
                                            <input name="labor_cost" type="number" step="0.01" min="0"
                                                   class="mt-2 w-full rounded-xl border border-white/10 bg-white/5 px-3 py-2 text-sm text-white">
                                        </div>

                                        <div>
                                            <label class="text-xs text-white/50">Parts Cost</label>
                                            <input name="parts_cost" type="number" step="0.01" min="0"
                                                   class="mt-2 w-full rounded-xl border border-white/10 bg-white/5 px-3 py-2 text-sm text-white">
                                        </div>
                                    </div>

                                    <div class="mt-4 text-sm text-white font-semibold flex justify-between">
                                        <span>Total</span>
                                        <span class="invoiceTotal">₹0.00</span>
                                    </div>
                                </div>

                            </form>

                            {{-- View Invoice --}}
                            @if($hasInvoice)
                                <button
                                    type="button"
                                    class="viewInvoiceBtn rounded-xl border border-white/10 bg-white/5 px-4 py-2 text-sm text-white hover:bg-white/10 transition"
                                    data-invoice-id="{{ $invoiceId }}"
                                    data-pdf-url="{{ route('moderator.invoices.pdf', $appointment->invoice) }}"
                                    data-customer="{{ optional($appointment->user)->name }}"
                                    data-vehicle="{{ optional($appointment->vehicle)->brand }} {{ optional($appointment->vehicle)->model }}"
                                    data-date="{{ \Carbon\Carbon::parse($appointment->service_date)->format('d M Y') }}"
                                    data-labor="{{ $appointment->invoice->labor_cost }}"
                                    data-parts="{{ $appointment->invoice->parts_cost }}"
                                    data-total="{{ $appointment->invoice->total_amount }}"
                                >
                                    View Invoice
                                </button>
                            @endif

                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

</div>

{{-- INVOICE MODAL --}}
<div id="invoiceModal" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-black/70 backdrop-blur-sm"></div>

    <div class="relative mx-auto mt-20 w-[90%] max-w-md">
        <div class="rounded-2xl border border-white/10 bg-[#0B0B0F] p-6 shadow-2xl">

            <div class="flex justify-between items-center mb-4">
                <div class="text-white font-semibold">Invoice</div>
                <button id="closeInvoiceModal" type="button" class="text-white/60 hover:text-white transition">✕</button>
            </div>

            <div class="space-y-3 text-sm">
                <div>
                    <div class="text-white/40">Customer</div>
                    <div id="invCustomer" class="text-white"></div>
                </div>

                <div>
                    <div class="text-white/40">Vehicle</div>
                    <div id="invVehicle" class="text-white"></div>
                </div>

                <div>
                    <div class="text-white/40">Service Date</div>
                    <div id="invDate" class="text-white"></div>
                </div>

                <div class="border-t border-white/10 pt-4 space-y-2">
                    <div class="flex justify-between">
                        <span class="text-white/60">Labor</span>
                        <span id="invLabor" class="text-white"></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-white/60">Parts</span>
                        <span id="invParts" class="text-white"></span>
                    </div>
                    <div class="flex justify-between font-semibold border-t border-white/10 pt-2">
                        <span class="text-white">Total</span>
                        <span id="invTotal" class="text-white"></span>
                    </div>
                </div>

                {{-- PDF Download --}}
                <div class="pt-4">
                    <a id="invPdfLink"
                       href="#"
                       target="_blank"
                       rel="noopener"
                       class="inline-flex w-full items-center justify-center rounded-xl bg-white px-4 py-2 text-sm font-medium text-black hover:bg-white/90 transition">
                        Download PDF
                    </a>
                    <p class="mt-2 text-xs text-white/40 text-center">
                        Opens in a new tab and downloads as PDF.
                    </p>
                </div>

            </div>

        </div>
    </div>
</div>

@endsection


@section('scripts')
<script>
(function () {

    // Invoice input box toggle (when selecting Completed)
    document.querySelectorAll('.statusForm').forEach(form => {
        const select = form.querySelector('.statusSelect');
        const invoiceBox = form.querySelector('.invoiceBox');
        const hasInvoice = form.dataset.hasInvoice === '1';

        const toggleInvoice = () => {
            if (select && invoiceBox) {
                if (select.value === 'Completed' && !hasInvoice) {
                    invoiceBox.classList.remove('hidden');
                } else {
                    invoiceBox.classList.add('hidden');
                }
            }
        };

        if (select) {
            select.addEventListener('change', toggleInvoice);
            toggleInvoice();
        }

        const laborInput = form.querySelector('input[name="labor_cost"]');
        const partsInput = form.querySelector('input[name="parts_cost"]');
        const totalEl = form.querySelector('.invoiceTotal');

        const updateTotal = () => {
            const labor = parseFloat(laborInput?.value || 0);
            const parts = parseFloat(partsInput?.value || 0);
            const total = labor + parts;
            if (totalEl) totalEl.textContent = '₹' + total.toFixed(2);
        };

        laborInput?.addEventListener('input', updateTotal);
        partsInput?.addEventListener('input', updateTotal);
    });

    // Invoice modal logic
    const modal = document.getElementById('invoiceModal');
    const closeBtn = document.getElementById('closeInvoiceModal');
    const pdfLink = document.getElementById('invPdfLink');

    const openModal = () => {
        if (!modal) return;
        modal.classList.remove('hidden');
        document.body.classList.add('overflow-hidden');
    };

    const closeModal = () => {
        if (!modal) return;
        modal.classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
    };

    document.querySelectorAll('.viewInvoiceBtn').forEach(btn => {
        btn.addEventListener('click', () => {
            const cust = document.getElementById('invCustomer');
            const veh = document.getElementById('invVehicle');
            const date = document.getElementById('invDate');
            const labor = document.getElementById('invLabor');
            const parts = document.getElementById('invParts');
            const total = document.getElementById('invTotal');

            if (cust) cust.textContent = btn.dataset.customer || '';
            if (veh) veh.textContent = btn.dataset.vehicle || '';
            if (date) date.textContent = btn.dataset.date || '';
            if (labor) labor.textContent = '₹' + (parseFloat(btn.dataset.labor || 0)).toFixed(2);
            if (parts) parts.textContent = '₹' + (parseFloat(btn.dataset.parts || 0)).toFixed(2);
            if (total) total.textContent = '₹' + (parseFloat(btn.dataset.total || 0)).toFixed(2);

            // Set PDF link from dataset (route already generated in blade)
            if (pdfLink) pdfLink.href = btn.dataset.pdfUrl || '#';

            openModal();
        });
    });

    closeBtn?.addEventListener('click', closeModal);

    // Click outside closes (overlay)
    const overlay = modal?.querySelector('div.absolute.inset-0');
    overlay?.addEventListener('click', closeModal);

    // ESC closes
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && modal && !modal.classList.contains('hidden')) closeModal();
    });

})();
</script>
@endsection