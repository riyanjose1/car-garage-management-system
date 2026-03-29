@extends('layouts.dashboard')

@section('content')
<div class="space-y-8">

    {{-- Header --}}
    <div>
        <h1 class="text-2xl font-semibold tracking-tight text-white">
            Users Management
        </h1>
        <p class="text-sm text-white/60 mt-1">
            Manage system users and roles.
        </p>
    </div>

    @if(session('success'))
        <div class="rounded-xl border border-emerald-400/20 bg-emerald-500/10 px-4 py-3 text-sm text-emerald-200">
            {{ session('success') }}
        </div>
    @endif

    {{-- Search --}}
    <form method="GET" action="{{ route('admin.users') }}" class="flex gap-3">
        <input
            name="q"
            value="{{ $q ?? '' }}"
            placeholder="Search by name, email, or role..."
            class="flex-1 rounded-xl border border-white/10 bg-white/5 px-4 py-2 text-sm text-white placeholder:text-white/30 focus:outline-none focus:ring-2 focus:ring-white/10"
        >

        <button type="submit"
                class="rounded-xl bg-white px-4 py-2 text-sm font-medium text-black hover:bg-white/90 transition">
            Search
        </button>
    </form>

    {{-- Users List --}}
    <div class="rounded-2xl border border-white/10 bg-white/5 p-6">

        <div class="space-y-4">
            @foreach($users as $user)
                <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 border-b border-white/5 pb-4">

                    <div>
                        <div class="text-white font-medium">
                            {{ $user->name }}
                        </div>
                        <div class="text-sm text-white/50">
                            {{ $user->email }}
                        </div>
                    </div>

                    <form method="POST"
                          action="{{ route('admin.users.role', $user->id) }}"
                          class="flex items-center gap-2">
                        @csrf

                        <select name="role"
                                class="rounded-xl border border-white/10 bg-white/5 px-3 py-2 text-sm text-white focus:outline-none focus:ring-2 focus:ring-white/10">
                            <option value="user" @selected($user->role === 'user')>User</option>
                            <option value="moderator" @selected($user->role === 'moderator')>Moderator</option>
                            <option value="admin" @selected($user->role === 'admin')>Admin</option>
                        </select>

                        <button type="submit"
                                class="rounded-xl bg-white px-4 py-2 text-sm font-medium text-black hover:bg-white/90 transition">
                            Update
                        </button>
                    </form>

                </div>
            @endforeach
        </div>

        {{-- Pagination --}}
        @if($users->hasPages())
            <div class="pt-6">
                {{ $users->links('vendor.pagination.autoserve') }}
            </div>
        @endif

    </div>

</div>
@endsection
