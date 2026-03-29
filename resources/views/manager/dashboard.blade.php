@extends('layouts.dashboard')

@section('title', 'Operations')
@section('subtitle', 'Garage activity & workflow')

@section('content')

@php
    use App\Models\Vehicle;

    $hasOperations = Vehicle::exists();
@endphp

@if(!$hasOperations)

    {{-- SKELETON STATE --}}
    <div class="space-y-6 max-w-3xl">

        <div class="flex gap-4">
            <x-skeleton height="h-20" width="w-1/3" />
            <x-skeleton height="h-20" width="w-1/3" />
            <x-skeleton height="h-20" width="w-1/3" />
        </div>

        <x-skeleton height="h-6" width="w-1/4" />

        <div class="space-y-3">
            <x-skeleton height="h-4" />
            <x-skeleton height="h-4" />
            <x-skeleton height="h-4" />
        </div>

    </div>

@else

    {{-- REAL CONTENT --}}
    <p class="text-slate-400">
        Operations exist — analytics will render here.
    </p>

@endif

@endsection
