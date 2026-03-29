@extends('layouts.dashboard')

@section('content')
<div class="space-y-8">

    {{-- Top Header Row --}}
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-semibold tracking-tight text-white">Dashboard</h1>
            <p class="text-sm text-white/60 mt-1">Manage your vehicles and service bookings.</p>
        </div>

        <div class="flex items-center gap-3">
            <button
                id="openBookServiceBtn"
                class="inline-flex items-center justify-center rounded-xl px-4 py-2 text-sm font-medium
                       bg-white/10 text-white hover:bg-white/15 border border-white/10
                       transition"
            >
                Book Service
            </button>

            <button
                id="openAddVehicleBtn"
                class="inline-flex items-center justify-center rounded-xl px-4 py-2 text-sm font-medium
                       bg-white text-black hover:bg-white/90 transition"
            >
                Add Vehicle
            </button>
        </div>
    </div>

    {{-- Metrics --}}
    <<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        <div class="rounded-2xl border border-white/10 bg-white/5 p-5">
            <div class="text-xs text-white/50">Vehicles</div>
            <div class="mt-2 text-2xl font-semibold text-white">{{ $vehicles->count() }}</div>
            <div class="mt-1 text-xs text-white/40">Total vehicles in your garage</div>
        </div>

        <div class="rounded-2xl border border-white/10 bg-white/5 p-5">
            <div class="text-xs text-white/50">Bookings</div>
            <div class="mt-2 text-2xl font-semibold text-white">{{ $appointments->count() }}</div>
            <div class="mt-1 text-xs text-white/40">Total service requests</div>
        </div>

        <div class="rounded-2xl border border-white/10 bg-white/5 p-5">
            <div class="text-xs text-white/50">Invoices</div>
            <div class="mt-2 text-2xl font-semibold text-white">—</div>
            <div class="mt-1 text-xs text-white/40">Coming next</div>
        </div>
    </div>

    {{-- Vehicles Section --}}
    <div class="flex items-center justify-between">
        <h2 class="text-lg font-semibold text-white">Your Vehicles</h2>
        <div class="text-xs text-white/50">{{ $vehicles->count() }} total</div>
    </div>

    @if(session('success'))
        <div class="rounded-xl border border-emerald-400/20 bg-emerald-500/10 px-4 py-3 text-sm text-emerald-200">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="rounded-xl border border-red-400/20 bg-red-500/10 px-4 py-3 text-sm text-red-200">
            <div class="font-medium">Please fix the following:</div>
            <ul class="mt-2 list-disc pl-5 space-y-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if($vehicles->isEmpty())
        <div class="rounded-2xl border border-white/10 bg-white/5 p-8 text-center">
            <div class="text-white font-medium">No vehicles yet</div>
            <div class="text-white/50 text-sm mt-2">Add a vehicle to start booking services.</div>
        </div>
    @else
        {{-- Pinterest/Masonry-ish grid --}}
        {{-- Vehicles grid --}}
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
    @foreach($vehicles as $vehicle)
        <div class="group rounded-2xl border border-white/10 bg-white/5 p-5 hover:bg-white/7 transition flex flex-col h-full">

            <div class="flex items-start justify-between gap-3">
                <div>
                    <div class="text-white font-semibold">
                        {{ $vehicle->brand }} {{ $vehicle->model }}
                    </div>
                    <div class="text-sm text-white/60 mt-1">
                        {{ $vehicle->plate_number ?? '—' }}
                    </div>
                </div>

                <div class="flex items-center gap-2 opacity-100 sm:opacity-0 sm:group-hover:opacity-100 transition">
                    <button
                        type="button"
                        class="editVehicleBtn rounded-xl border border-white/10 bg-white/5 px-3 py-1.5 text-xs text-white hover:bg-white/10 transition"
                        data-id="{{ $vehicle->id }}"
                        data-brand="{{ $vehicle->brand }}"
                        data-model="{{ $vehicle->model }}"
                        data-plate="{{ $vehicle->plate_number }}"
                        data-year="{{ $vehicle->year }}"
                    >
                        Edit
                    </button>

                    <form action="{{ url('/vehicles/'.$vehicle->id) }}" method="POST" onsubmit="return confirm('Delete this vehicle?');">
                        @csrf
                        @method('DELETE')
                        <button
                            type="submit"
                            class="rounded-xl border border-white/10 bg-white/5 px-3 py-1.5 text-xs text-white/80 hover:bg-white/10 hover:text-white transition"
                        >
                            Delete
                        </button>
                    </form>
                </div>
            </div>

            <div class="mt-4 grid grid-cols-2 gap-3 text-sm">
                <div class="rounded-xl border border-white/10 bg-black/20 p-3">
                    <div class="text-xs text-white/50">Year</div>
                    <div class="mt-1 text-white">{{ $vehicle->year ?? '—' }}</div>
                </div>
                <div class="rounded-xl border border-white/10 bg-black/20 p-3">
                    <div class="text-xs text-white/50">Plate</div>
                    <div class="mt-1 text-white">{{ $vehicle->plate_number ?? '—' }}</div>
                </div>
            </div>

            <div class="mt-auto pt-4">
                <button
                    type="button"
                    class="quickBookBtn w-full rounded-xl border border-white/10 bg-white/5 px-4 py-2 text-sm text-white hover:bg-white/10 transition"
                    data-vehicle-id="{{ $vehicle->id }}"
                >
                    Book service for this vehicle
                </button>
            </div>

        </div>
    @endforeach
</div>
    @endif

    {{-- Bookings Section --}}
    <div class="pt-2">
        <div class="flex items-center justify-between">
            <h2 class="text-lg font-semibold text-white">Your Bookings</h2>
            <div class="text-xs text-white/50">{{ $appointments->count() }} total</div>
        </div>

        @if($appointments->isEmpty())
            <div class="mt-4 rounded-2xl border border-white/10 bg-white/5 p-8 text-center">
                <div class="text-white font-medium">No bookings yet</div>
                <div class="text-white/50 text-sm mt-2">Use “Book Service” to request your first service.</div>
            </div>
        @else
            <div class="mt-4 space-y-3">
                @foreach($appointments as $appointment)
                    <div class="rounded-2xl border border-white/10 bg-white/5 p-5 hover:bg-white/7 transition">
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                            <div>
                                <div class="text-white font-semibold">
                                    {{ $appointment->service_type ?? 'Service' }}
                                    <span class="text-white/50 font-normal">•</span>
                                    <span class="text-white/70 font-normal">
                                        {{ optional($appointment->vehicle)->brand }} {{ optional($appointment->vehicle)->model }}
                                    </span>
                                </div>

                                <div class="text-sm text-white/60 mt-1">
                                    @php
                                        $date = $appointment->service_date ?? null;
                                        $time = $appointment->service_time ?? null;
                                    @endphp
                                    {{ $date ? \Carbon\Carbon::parse($date)->format('d M Y') : '—' }}
                                    @if($time) • {{ $time }} @endif
                                </div>

                                @if(!empty($appointment->notes))
                                    <div class="mt-3 text-sm text-white/70">
                                        <span class="text-white/40">Notes:</span> {{ $appointment->notes }}
                                    </div>
                                @endif
                            </div>

                            <div class="flex items-center gap-2">
                                @php
                                    $status = strtolower($appointment->status ?? 'pending');
                                    $badgeClass = match ($status) {
                                        'confirmed' => 'border-emerald-400/20 bg-emerald-500/10 text-emerald-200',
                                        'completed' => 'border-sky-400/20 bg-sky-500/10 text-sky-200',
                                        'cancelled', 'canceled' => 'border-red-400/20 bg-red-500/10 text-red-200',
                                        default => 'border-white/10 bg-white/5 text-white/70',
                                    };
                                @endphp

                                <span class="inline-flex items-center rounded-xl border px-3 py-1.5 text-xs {{ $badgeClass }}">
                                    {{ ucfirst($appointment->status ?? 'Pending') }}
                                </span>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

</div>

{{-- =========================
    ADD VEHICLE MODAL
========================= --}}
<div id="addVehicleModal" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-black/70 backdrop-blur-sm"></div>

    <div class="relative mx-auto mt-16 w-[92%] max-w-xl">
        <div class="rounded-2xl border border-white/10 bg-[#0B0B0F] shadow-2xl">
            <div class="flex items-center justify-between px-6 py-5 border-b border-white/10">
                <div>
                    <div class="text-white font-semibold">Add Vehicle</div>
                    <div class="text-xs text-white/50 mt-1">Save a vehicle to book services faster.</div>
                </div>
                <button type="button" class="closeModalBtn text-white/60 hover:text-white transition" data-close="#addVehicleModal">✕</button>
            </div>

            <form action="{{ url('/vehicles') }}" method="POST" class="p-6 space-y-4">
                @csrf

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="text-xs text-white/50">Brand</label>
                        <input name="brand" required class="mt-2 w-full rounded-xl border border-white/10 bg-white/5 px-4 py-2 text-sm text-white placeholder:text-white/30 focus:outline-none focus:ring-2 focus:ring-white/10" placeholder="e.g., Honda">
                    </div>
                    <div>
                        <label class="text-xs text-white/50">Model</label>
                        <input name="model" required class="mt-2 w-full rounded-xl border border-white/10 bg-white/5 px-4 py-2 text-sm text-white placeholder:text-white/30 focus:outline-none focus:ring-2 focus:ring-white/10" placeholder="e.g., City">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="text-xs text-white/50">Plate Number</label>
                        <input name="plate_number" required class="mt-2 w-full rounded-xl border border-white/10 bg-white/5 px-4 py-2 text-sm text-white placeholder:text-white/30 focus:outline-none focus:ring-2 focus:ring-white/10" placeholder="e.g., KL-07-AB-1234">
                    </div>
                    <div>
                        <label class="text-xs text-white/50">Year (optional)</label>
                        <input name="year" type="number" min="1950" max="2100" class="mt-2 w-full rounded-xl border border-white/10 bg-white/5 px-4 py-2 text-sm text-white placeholder:text-white/30 focus:outline-none focus:ring-2 focus:ring-white/10" placeholder="e.g., 2019">
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3 pt-2">
                    <button type="button" class="closeModalBtn rounded-xl border border-white/10 bg-white/5 px-4 py-2 text-sm text-white hover:bg-white/10 transition" data-close="#addVehicleModal">
                        Cancel
                    </button>
                    <button type="submit" class="rounded-xl bg-white px-4 py-2 text-sm font-medium text-black hover:bg-white/90 transition">
                        Save Vehicle
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- =========================
    EDIT VEHICLE MODAL
========================= --}}
<div id="editVehicleModal" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-black/70 backdrop-blur-sm"></div>

    <div class="relative mx-auto mt-16 w-[92%] max-w-xl">
        <div class="rounded-2xl border border-white/10 bg-[#0B0B0F] shadow-2xl">
            <div class="flex items-center justify-between px-6 py-5 border-b border-white/10">
                <div>
                    <div class="text-white font-semibold">Edit Vehicle</div>
                    <div class="text-xs text-white/50 mt-1">Update your vehicle details.</div>
                </div>
                <button type="button" class="closeModalBtn text-white/60 hover:text-white transition" data-close="#editVehicleModal">✕</button>
            </div>

            <form id="editVehicleForm" method="POST" class="p-6 space-y-4">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="text-xs text-white/50">Brand</label>
                        <input id="editBrand" name="brand" required class="mt-2 w-full rounded-xl border border-white/10 bg-white/5 px-4 py-2 text-sm text-white placeholder:text-white/30 focus:outline-none focus:ring-2 focus:ring-white/10">
                    </div>
                    <div>
                        <label class="text-xs text-white/50">Model</label>
                        <input id="editModel" name="model" required class="mt-2 w-full rounded-xl border border-white/10 bg-white/5 px-4 py-2 text-sm text-white placeholder:text-white/30 focus:outline-none focus:ring-2 focus:ring-white/10">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="text-xs text-white/50">Plate Number</label>
                        <input id="editPlate" name="plate_number" required class="mt-2 w-full rounded-xl border border-white/10 bg-white/5 px-4 py-2 text-sm text-white placeholder:text-white/30 focus:outline-none focus:ring-2 focus:ring-white/10">
                    </div>
                    <div>
                        <label class="text-xs text-white/50">Year (optional)</label>
                        <input id="editYear" name="year" type="number" min="1950" max="2100" class="mt-2 w-full rounded-xl border border-white/10 bg-white/5 px-4 py-2 text-sm text-white placeholder:text-white/30 focus:outline-none focus:ring-2 focus:ring-white/10">
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3 pt-2">
                    <button type="button" class="closeModalBtn rounded-xl border border-white/10 bg-white/5 px-4 py-2 text-sm text-white hover:bg-white/10 transition" data-close="#editVehicleModal">
                        Cancel
                    </button>
                    <button type="submit" class="rounded-xl bg-white px-4 py-2 text-sm font-medium text-black hover:bg-white/90 transition">
                        Update Vehicle
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- =========================
    BOOK SERVICE MODAL
========================= --}}
<div id="bookServiceModal" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-black/70 backdrop-blur-sm"></div>

    <div class="relative mx-auto mt-16 w-[92%] max-w-xl">
        <div class="rounded-2xl border border-white/10 bg-[#0B0B0F] shadow-2xl">
            <div class="flex items-center justify-between px-6 py-5 border-b border-white/10">
                <div>
                    <div class="text-white font-semibold">Book Service</div>
                    <div class="text-xs text-white/50 mt-1">Request a service slot for your vehicle.</div>
                </div>
                <button type="button" class="closeModalBtn text-white/60 hover:text-white transition" data-close="#bookServiceModal">✕</button>
            </div>

            <form action="{{ url('/appointments') }}" method="POST" class="p-6 space-y-4">
                @csrf

                <div>
                    <label class="text-xs text-white/50">Vehicle</label>
                    <select
                        id="bookingVehicleSelect"
                        name="vehicle_id"
                        required
                        class="mt-2 w-full rounded-xl border border-white/10 bg-white/5 px-4 py-2 text-sm text-white
                               focus:outline-none focus:ring-2 focus:ring-white/10"
                    >
                        <option value="" class="bg-[#0B0B0F]">Select a vehicle</option>
                        @foreach($vehicles as $vehicle)
                            <option value="{{ $vehicle->id }}" class="bg-[#0B0B0F]">
                                {{ $vehicle->brand }} {{ $vehicle->model }} {{ $vehicle->plate_number ? '• '.$vehicle->plate_number : '' }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="text-xs text-white/50">Date</label>
                        <input
                            name="service_date"
                            type="date"
                            required
                            class="mt-2 w-full rounded-xl border border-white/10 bg-white/5 px-4 py-2 text-sm text-white
                                   focus:outline-none focus:ring-2 focus:ring-white/10"
                        >
                    </div>

                    <div>
                        <label class="text-xs text-white/50">Time</label>
                        <input
                            name="service_time"
                            type="time"
                            required
                            class="mt-2 w-full rounded-xl border border-white/10 bg-white/5 px-4 py-2 text-sm text-white
                                   focus:outline-none focus:ring-2 focus:ring-white/10"
                        >
                    </div>
                </div>

                <div>
                    <label class="text-xs text-white/50">Service Type</label>
                    <select
                        name="service_type"
                        required
                        class="mt-2 w-full rounded-xl border border-white/10 bg-white/5 px-4 py-2 text-sm text-white
                               focus:outline-none focus:ring-2 focus:ring-white/10"
                    >
                        <option value="" class="bg-[#0B0B0F]">Choose a service</option>
                        <option value="General Service" class="bg-[#0B0B0F]">General Service</option>
                        <option value="Oil Change" class="bg-[#0B0B0F]">Oil Change</option>
                        <option value="Brake Check" class="bg-[#0B0B0F]">Brake Check</option>
                        <option value="AC Service" class="bg-[#0B0B0F]">AC Service</option>
                        <option value="Wheel Alignment" class="bg-[#0B0B0F]">Wheel Alignment</option>
                        <option value="Diagnostics" class="bg-[#0B0B0F]">Diagnostics</option>
                    </select>
                </div>

                <div>
                    <label class="text-xs text-white/50">Notes (optional)</label>
                    <textarea
                        name="notes"
                        rows="3"
                        class="mt-2 w-full rounded-xl border border-white/10 bg-white/5 px-4 py-2 text-sm text-white placeholder:text-white/30
                               focus:outline-none focus:ring-2 focus:ring-white/10"
                        placeholder="Describe the issue, sounds, warning lights, etc."
                    ></textarea>
                </div>

                <div class="flex items-center justify-end gap-3 pt-2">
                    <button type="button" class="closeModalBtn rounded-xl border border-white/10 bg-white/5 px-4 py-2 text-sm text-white hover:bg-white/10 transition" data-close="#bookServiceModal">
                        Cancel
                    </button>
                    <button type="submit" class="rounded-xl bg-white px-4 py-2 text-sm font-medium text-black hover:bg-white/90 transition">
                        Request Booking
                    </button>
                </div>

                <p class="text-xs text-white/40 pt-1">
                    Your request will be created with status <span class="text-white/60">Pending</span>.
                </p>
            </form>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
(function () {
    const openModal = (selector) => {
        const el = document.querySelector(selector);
        if (!el) return;
        el.classList.remove('hidden');
        document.body.classList.add('overflow-hidden');
    };

    const closeModal = (selector) => {
        const el = document.querySelector(selector);
        if (!el) return;
        el.classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
    };

    // Add Vehicle
    const openAddVehicleBtn = document.getElementById('openAddVehicleBtn');
    if (openAddVehicleBtn) {
        openAddVehicleBtn.addEventListener('click', () => openModal('#addVehicleModal'));
    }

    // Book Service
    const openBookServiceBtn = document.getElementById('openBookServiceBtn');
    if (openBookServiceBtn) {
        openBookServiceBtn.addEventListener('click', () => openModal('#bookServiceModal'));
    }

    // Close buttons
    document.querySelectorAll('.closeModalBtn').forEach(btn => {
        btn.addEventListener('click', () => {
            const target = btn.getAttribute('data-close');
            if (target) closeModal(target);
        });
    });

    // Click outside modal content closes
    ['#addVehicleModal', '#editVehicleModal', '#bookServiceModal'].forEach(selector => {
        const modal = document.querySelector(selector);
        if (!modal) return;

        modal.addEventListener('click', (e) => {
            if (e.target === modal || e.target.classList.contains('backdrop')) {
                closeModal(selector);
            }
        });

        // Also close if clicking on the dark overlay
        const overlay = modal.querySelector('div.absolute.inset-0');
        if (overlay) overlay.addEventListener('click', () => closeModal(selector));
    });

    // ESC closes any open modal
    document.addEventListener('keydown', (e) => {
        if (e.key !== 'Escape') return;
        ['#addVehicleModal', '#editVehicleModal', '#bookServiceModal'].forEach(selector => {
            const modal = document.querySelector(selector);
            if (modal && !modal.classList.contains('hidden')) closeModal(selector);
        });
    });

    // Edit vehicle modal hydration
    const editVehicleModalSelector = '#editVehicleModal';
    const editVehicleForm = document.getElementById('editVehicleForm');

    document.querySelectorAll('.editVehicleBtn').forEach(btn => {
        btn.addEventListener('click', () => {
            const id = btn.dataset.id;

            document.getElementById('editBrand').value = btn.dataset.brand || '';
            document.getElementById('editModel').value = btn.dataset.model || '';
            document.getElementById('editPlate').value = btn.dataset.plate || '';
            document.getElementById('editYear').value = btn.dataset.year || '';

            if (editVehicleForm && id) {
                editVehicleForm.action = `{{ url('/vehicles') }}/${id}`;
            }

            openModal(editVehicleModalSelector);
        });
    });

    // Quick book from vehicle card
    const bookingVehicleSelect = document.getElementById('bookingVehicleSelect');

    document.querySelectorAll('.quickBookBtn').forEach(btn => {
        btn.addEventListener('click', () => {
            const vehicleId = btn.dataset.vehicleId;
            openModal('#bookServiceModal');

            if (bookingVehicleSelect && vehicleId) {
                bookingVehicleSelect.value = vehicleId;
            }
        });
    });

})();
</script>
@endsection
