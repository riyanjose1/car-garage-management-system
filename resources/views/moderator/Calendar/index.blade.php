@extends('layouts.dashboard')

@section('content')
<div class="space-y-8">

    {{-- Header --}}
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-semibold tracking-tight text-white">
                Weekly Schedule
            </h1>
            <p class="text-sm text-white/60 mt-1">
                {{ $start->format('d M') }} – {{ $end->format('d M Y') }}
            </p>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('moderator.calendar', ['week' => $start->copy()->subWeek()->toDateString()]) }}"
               class="rounded-xl border border-white/10 bg-white/5 px-4 py-2 text-sm text-white hover:bg-white/10 transition">
                ← Previous
            </a>

            <a href="{{ route('moderator.calendar', ['week' => now()->toDateString()]) }}"
               class="rounded-xl border border-white/10 bg-white/5 px-4 py-2 text-sm text-white hover:bg-white/10 transition">
                Today
            </a>

            <a href="{{ route('moderator.calendar', ['week' => $start->copy()->addWeek()->toDateString()]) }}"
               class="rounded-xl border border-white/10 bg-white/5 px-4 py-2 text-sm text-white hover:bg-white/10 transition">
                Next →
            </a>

            <a href="{{ route('moderator.appointments.index') }}"
               class="rounded-xl bg-white px-4 py-2 text-sm font-medium text-black hover:bg-white/90 transition">
                List View
            </a>
        </div>
    </div>

    {{-- Content --}}
    @if($appointments->isEmpty())
        <div class="rounded-2xl border border-white/10 bg-white/5 p-10 text-center">
            <div class="text-white font-medium">No bookings this week</div>
            <div class="text-white/50 text-sm mt-2">
                Try navigating to another week.
            </div>
        </div>
    @else
        <div class="space-y-6">

            @foreach($appointments as $date => $items)
                <div class="rounded-2xl border border-white/10 bg-white/5 p-6">

                    {{-- Day Header --}}
                    <div class="mb-4">
                        <div class="text-white font-semibold">
                            {{ \Carbon\Carbon::parse($date)->format('l, d M Y') }}
                        </div>
                        <div class="text-xs text-white/40">
                            {{ $items->count() }} bookings
                        </div>
                    </div>

                    {{-- Appointments --}}
                    <div class="space-y-3">
                        @foreach($items as $appointment)

                            @php
                                $status = strtolower($appointment->status);
                                $badgeClass = match ($status) {
                                    'confirmed' => 'border-emerald-400/20 bg-emerald-500/10 text-emerald-200',
                                    'completed' => 'border-sky-400/20 bg-sky-500/10 text-sky-200',
                                    'cancelled', 'canceled' => 'border-red-400/20 bg-red-500/10 text-red-200',
                                    default => 'border-white/10 bg-white/5 text-white/70',
                                };

                                $vehicleText = trim((optional($appointment->vehicle)->brand ?? '') . ' ' . (optional($appointment->vehicle)->model ?? ''));
                                $plateText = optional($appointment->vehicle)->plate_number ?? '';
                                $customerName = optional($appointment->user)->name ?? '—';
                                $serviceDatePretty = \Carbon\Carbon::parse($appointment->service_date)->format('d M Y');
                                $hasInvoice = $appointment->invoice ? '1' : '0';
                            @endphp

                            <button
                                type="button"
                                class="bookingCard w-full text-left flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 rounded-xl border border-white/10 bg-black/20 p-4
                                       hover:bg-white/5 hover:border-white/15 transition"
                                data-id="{{ $appointment->id }}"
                                data-time="{{ $appointment->service_time }}"
                                data-date="{{ $serviceDatePretty }}"
                                data-service="{{ $appointment->service_type }}"
                                data-status="{{ $appointment->status }}"
                                data-vehicle="{{ $vehicleText }}"
                                data-plate="{{ $plateText }}"
                                data-customer="{{ $customerName }}"
                                data-notes="{{ $appointment->notes ?? '' }}"
                                data-status-url="{{ route('moderator.appointments.status', $appointment) }}"
                                data-has-invoice="{{ $hasInvoice }}"
                            >
                                <div>
                                    <div class="text-white font-medium">
                                        {{ $appointment->service_time }}
                                        <span class="text-white/40">•</span>
                                        {{ $appointment->service_type }}
                                    </div>

                                    <div class="text-sm text-white/60 mt-1">
                                        {{ $vehicleText }}
                                        @if($plateText)
                                            <span class="text-white/40">•</span>
                                            {{ $plateText }}
                                        @endif
                                        <span class="text-white/40">•</span>
                                        {{ $customerName }}
                                    </div>

                                    @if($appointment->notes)
                                        <div class="mt-2 text-sm text-white/70">
                                            <span class="text-white/40">Notes:</span>
                                            {{ $appointment->notes }}
                                        </div>
                                    @endif
                                </div>

                                <span class="statusBadge inline-flex items-center rounded-xl border px-3 py-1.5 text-xs {{ $badgeClass }}">
                                    {{ $appointment->status }}
                                </span>
                            </button>
                        @endforeach
                    </div>

                </div>
            @endforeach

        </div>
    @endif

</div>

{{-- =========================
    BOOKING DETAILS MODAL
========================= --}}
<div id="bookingDetailsModal" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-black/70 backdrop-blur-sm"></div>

    <div class="relative mx-auto mt-16 w-[92%] max-w-2xl">
        <div class="rounded-2xl border border-white/10 bg-[#0B0B0F] shadow-2xl">
            <div class="flex items-center justify-between px-6 py-5 border-b border-white/10">
                <div>
                    <div class="text-white font-semibold">Booking Details</div>
                    <div id="bdSubtitle" class="text-xs text-white/50 mt-1">—</div>
                </div>
                <button type="button" id="closeBookingDetails"
                        class="text-white/60 hover:text-white transition">
                    ✕
                </button>
            </div>

            <div class="p-6 space-y-5">
                {{-- Toast --}}
                <div id="bdToast" class="hidden rounded-xl border border-emerald-400/20 bg-emerald-500/10 px-4 py-3 text-sm text-emerald-200">
                    Saved ✅
                </div>

                {{-- Error --}}
                <div id="bdError" class="hidden rounded-xl border border-red-400/20 bg-red-500/10 px-4 py-3 text-sm text-red-200">
                    —
                </div>

                {{-- Top info --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="rounded-2xl border border-white/10 bg-white/5 p-4">
                        <div class="text-xs text-white/50">Service</div>
                        <div id="bdService" class="mt-2 text-white font-semibold">—</div>
                        <div id="bdDateTime" class="mt-1 text-sm text-white/60">—</div>
                    </div>

                    <div class="rounded-2xl border border-white/10 bg-white/5 p-4">
                        <div class="text-xs text-white/50">Customer</div>
                        <div id="bdCustomer" class="mt-2 text-white font-semibold">—</div>
                        <div id="bdVehicle" class="mt-1 text-sm text-white/60">—</div>
                    </div>
                </div>

                {{-- Notes --}}
                <div class="rounded-2xl border border-white/10 bg-white/5 p-4">
                    <div class="text-xs text-white/50">Notes</div>
                    <div id="bdNotes" class="mt-2 text-sm text-white/70">—</div>
                </div>

                {{-- Status update --}}
                <form id="bdStatusForm" method="POST" class="space-y-4">
                    @csrf
                    @method('PATCH')

                    <div>
                        <label class="text-xs text-white/50">Update Status</label>
                        <select id="bdStatusSelect" name="status"
                                class="mt-2 w-full rounded-xl border border-white/10 bg-white/5 px-4 py-2 text-sm text-white
                                       focus:outline-none focus:ring-2 focus:ring-white/10">
                            <option value="Pending" class="bg-[#0B0B0F]">Pending</option>
                            <option value="Confirmed" class="bg-[#0B0B0F]">Confirmed</option>
                            <option value="Completed" class="bg-[#0B0B0F]">Completed</option>
                            <option value="Cancelled" class="bg-[#0B0B0F]">Cancelled</option>
                        </select>
                    </div>

                    {{-- Invoice fields (only when Completed + no invoice exists yet) --}}
                    <div id="invoiceFields" class="hidden rounded-2xl border border-white/10 bg-white/5 p-4">
                        <div class="text-white font-semibold text-sm">Invoice Details</div>
                        <div class="text-xs text-white/50 mt-1">
                            Enter costs to generate the invoice.
                        </div>

                        <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="text-xs text-white/50">Labor Cost</label>
                                <input id="laborCost" name="labor_cost" type="number" step="0.01" min="0"
                                       class="mt-2 w-full rounded-xl border border-white/10 bg-black/20 px-4 py-2 text-sm text-white
                                              focus:outline-none focus:ring-2 focus:ring-white/10"
                                       placeholder="e.g., 1500">
                            </div>

                            <div>
                                <label class="text-xs text-white/50">Parts Cost</label>
                                <input id="partsCost" name="parts_cost" type="number" step="0.01" min="0"
                                       class="mt-2 w-full rounded-xl border border-white/10 bg-black/20 px-4 py-2 text-sm text-white
                                              focus:outline-none focus:ring-2 focus:ring-white/10"
                                       placeholder="e.g., 800">
                            </div>
                        </div>

                        <div class="mt-4">
                            <label class="text-xs text-white/50">Invoice Notes (optional)</label>
                            <textarea id="invoiceNotes" name="invoice_notes" rows="3"
                                      class="mt-2 w-full rounded-xl border border-white/10 bg-black/20 px-4 py-2 text-sm text-white
                                             placeholder:text-white/30 focus:outline-none focus:ring-2 focus:ring-white/10"
                                      placeholder="Work done, parts changed, etc."></textarea>
                        </div>

                        <div class="mt-4 flex items-center justify-between">
                            <div class="text-xs text-white/50">Total</div>
                            <div id="invoiceTotal" class="text-sm text-white font-semibold">₹0.00</div>
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-2">
                        <button type="button" id="bdCancelBtn"
                                class="rounded-xl border border-white/10 bg-white/5 px-4 py-2 text-sm text-white hover:bg-white/10 transition">
                            Close
                        </button>

                        <button id="bdSaveBtn" type="submit"
                                class="rounded-xl bg-white px-4 py-2 text-sm font-medium text-black hover:bg-white/90 transition">
                            Save
                        </button>
                    </div>
                </form>

                <div class="text-xs text-white/40">
                    Tip: Press <span class="text-white/60">Esc</span> to close.
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    const modal = document.getElementById('bookingDetailsModal');
    const overlay = modal?.querySelector('div.absolute.inset-0');
    const closeBtn = document.getElementById('closeBookingDetails');
    const cancelBtn = document.getElementById('bdCancelBtn');

    const bdSubtitle = document.getElementById('bdSubtitle');
    const bdService = document.getElementById('bdService');
    const bdDateTime = document.getElementById('bdDateTime');
    const bdCustomer = document.getElementById('bdCustomer');
    const bdVehicle = document.getElementById('bdVehicle');
    const bdNotes = document.getElementById('bdNotes');

    const bdStatusForm = document.getElementById('bdStatusForm');
    const bdStatusSelect = document.getElementById('bdStatusSelect');
    const bdSaveBtn = document.getElementById('bdSaveBtn');

    const bdToast = document.getElementById('bdToast');
    const bdError = document.getElementById('bdError');

    const invoiceFields = document.getElementById('invoiceFields');
    const laborCost = document.getElementById('laborCost');
    const partsCost = document.getElementById('partsCost');
    const invoiceNotes = document.getElementById('invoiceNotes');
    const invoiceTotal = document.getElementById('invoiceTotal');

    let lastClickedCard = null;
    let lastHasInvoice = false;

    const statusToBadgeClass = (status) => {
        const s = String(status || '').toLowerCase();
        if (s === 'confirmed') return 'border-emerald-400/20 bg-emerald-500/10 text-emerald-200';
        if (s === 'completed') return 'border-sky-400/20 bg-sky-500/10 text-sky-200';
        if (s === 'cancelled' || s === 'canceled') return 'border-red-400/20 bg-red-500/10 text-red-200';
        return 'border-white/10 bg-white/5 text-white/70';
    };

    const openModal = () => {
        if (!modal) return;
        modal.classList.remove('hidden');
        document.body.classList.add('overflow-hidden');
    };

    const closeModal = () => {
        if (!modal) return;
        modal.classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
        if (bdToast) bdToast.classList.add('hidden');
        if (bdError) bdError.classList.add('hidden');
    };

    const showToast = () => {
        if (!bdToast) return;
        bdToast.classList.remove('hidden');
        setTimeout(() => bdToast.classList.add('hidden'), 1400);
    };

    const showError = (msg) => {
        if (!bdError) return;
        bdError.textContent = msg || 'Something went wrong.';
        bdError.classList.remove('hidden');
    };

    const hideError = () => {
        if (!bdError) return;
        bdError.classList.add('hidden');
    };

    const calcTotal = () => {
        const l = parseFloat(laborCost?.value || '0') || 0;
        const p = parseFloat(partsCost?.value || '0') || 0;
        const t = l + p;
        if (invoiceTotal) invoiceTotal.textContent = `₹${t.toFixed(2)}`;
        return t;
    };

    const setInvoiceVisibility = () => {
        const isCompleted = (bdStatusSelect?.value === 'Completed');
        // show invoice fields only if marking completed and invoice does not already exist
        if (invoiceFields) {
            if (isCompleted && !lastHasInvoice) {
                invoiceFields.classList.remove('hidden');
            } else {
                invoiceFields.classList.add('hidden');
                if (laborCost) laborCost.value = '';
                if (partsCost) partsCost.value = '';
                if (invoiceNotes) invoiceNotes.value = '';
                calcTotal();
            }
        }
    };

    laborCost?.addEventListener('input', calcTotal);
    partsCost?.addEventListener('input', calcTotal);
    bdStatusSelect?.addEventListener('change', setInvoiceVisibility);

    // Click booking -> open modal
    document.querySelectorAll('.bookingCard').forEach(btn => {
        btn.addEventListener('click', () => {
            lastClickedCard = btn;
            lastHasInvoice = btn.dataset.hasInvoice === '1';

            const service = btn.dataset.service || '—';
            const date = btn.dataset.date || '—';
            const time = btn.dataset.time || '—';
            const customer = btn.dataset.customer || '—';
            const vehicle = btn.dataset.vehicle || '—';
            const plate = btn.dataset.plate || '';
            const notes = (btn.dataset.notes || '').trim();
            const status = btn.dataset.status || 'Pending';
            const statusUrl = btn.dataset.statusUrl || '';

            bdSubtitle.textContent = `${date} • ${time}`;
            bdService.textContent = service;
            bdDateTime.textContent = `${date} • ${time}`;
            bdCustomer.textContent = customer;
            bdVehicle.textContent = plate ? `${vehicle} • ${plate}` : vehicle;
            bdNotes.textContent = notes.length ? notes : '—';

            if (bdStatusSelect) bdStatusSelect.value = status;

            if (bdStatusForm && statusUrl) {
                bdStatusForm.action = statusUrl;
            }

            hideError();
            if (bdToast) bdToast.classList.add('hidden');

            calcTotal();
            setInvoiceVisibility();

            openModal();
        });
    });

    // AJAX submit (no reload)
    if (bdStatusForm) {
        bdStatusForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            hideError();

            if (!bdStatusForm.action) return;

            // If trying to complete and invoice doesn't exist, enforce total > 0 client-side too
            const chosen = bdStatusSelect?.value || 'Pending';
            if (chosen === 'Completed' && !lastHasInvoice) {
                const total = calcTotal();
                if (total <= 0) {
                    showError('Enter labor or parts cost to generate an invoice.');
                    return;
                }
            }

            const formData = new FormData(bdStatusForm);
            const csrf = formData.get('_token');

            if (bdSaveBtn) {
                bdSaveBtn.disabled = true;
                bdSaveBtn.textContent = 'Saving...';
            }

            try {
                const res = await fetch(bdStatusForm.action, {
                    method: 'POST', // _method=PATCH present
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': csrf
                    },
                    body: formData
                });

                if (!res.ok) {
                    let msg = 'Failed to save.';
                    try {
                        const data = await res.json();
                        if (data?.message) msg = data.message;
                    } catch (_) {}
                    showError(msg);
                    return;
                }

                const data = await res.json();
                const newStatus = data.status || formData.get('status') || 'Pending';

                // Update clicked card badge + dataset
                if (lastClickedCard) {
                    lastClickedCard.dataset.status = newStatus;

                    const badge = lastClickedCard.querySelector('.statusBadge');
                    if (badge) {
                        badge.className = 'statusBadge inline-flex items-center rounded-xl border px-3 py-1.5 text-xs ' + statusToBadgeClass(newStatus);
                        badge.textContent = newStatus;
                    }

                    // If invoice created, mark it so we don't ask again
                    if (data.invoice) {
                        lastClickedCard.dataset.hasInvoice = '1';
                        lastHasInvoice = true;
                    }
                }

                // Hide invoice box after save if invoice is now created
                setInvoiceVisibility();
                showToast();
            } catch (err) {
                showError('Network error. Please try again.');
            } finally {
                if (bdSaveBtn) {
                    bdSaveBtn.disabled = false;
                    bdSaveBtn.textContent = 'Save';
                }
            }
        });
    }

    // Close handlers
    closeBtn?.addEventListener('click', closeModal);
    cancelBtn?.addEventListener('click', closeModal);
    overlay?.addEventListener('click', closeModal);

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && modal && !modal.classList.contains('hidden')) {
            closeModal();
        }
    });
})();
</script>
@endsection
