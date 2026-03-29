@extends('layouts.dashboard')

@section('title', 'Schedule')
@section('subtitle', 'Weekly calendar view of bookings.')

@section('content')
<div class="space-y-6">

    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-semibold text-white">Schedule</h1>
        <a href="{{ route('moderator.appointments.index') }}"
           class="rounded-xl border border-white/10 bg-white/5 px-4 py-2 text-sm text-white hover:bg-white/10 transition">
            Back to Bookings
        </a>
    </div>

    <div class="rounded-2xl border border-white/10 bg-white/5 p-6">
        <div class="text-white/70 text-sm mb-4">
            Week: <span class="text-white font-medium">{{ $start->format('d M Y') }}</span>
            to <span class="text-white font-medium">{{ $end->format('d M Y') }}</span>
        </div>

        @if($appointments->isEmpty())
            <div class="text-white/60">No bookings this week.</div>
        @else
            <div class="space-y-5">
                @foreach($appointments as $date => $items)
                    <div class="rounded-xl border border-white/10 bg-black/20 p-4">
                        <div class="text-white font-semibold mb-3">
                            {{ \Carbon\Carbon::parse($date)->format('l, d M Y') }}
                        </div>

                        <div class="space-y-3">
                            @foreach($items as $a)
                                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 border-b border-white/10 pb-3">
                                    <div class="text-white">
                                        <span class="text-white/60">{{ $a->service_time }}</span>
                                        • {{ $a->service_type }}
                                        • {{ optional($a->vehicle)->brand }} {{ optional($a->vehicle)->model }}
                                    </div>
                                    <div class="text-white/60 text-sm">
                                        {{ optional($a->user)->name }} ({{ $a->status }})
                                    </div>
                                </div>
                            @endforeach
                        </div>

                    </div>
                @endforeach
            </div>
        @endif
    </div>

</div>
@endsection