<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AutoServe</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('images/autoserve-mark.svg') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        :root {
            --bg0: #05060a;
            --bg1: #070914;
            --card: rgba(255, 255, 255, 0.06);
            --card2: rgba(255, 255, 255, 0.08);
            --border: rgba(255, 255, 255, 0.10);
        }

        body {
            background-color: #05060a;
            font-family: 'Manrope', sans-serif;
        }

        .glass {
            background: var(--card);
            border: 1px solid var(--border);
            backdrop-filter: blur(10px);
        }

        .glass-hover:hover {
            background: var(--card2);
        }

        .soft-shadow {
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.35);
        }
    </style>
</head>

<body class="bg-slate-950 text-slate-200 antialiased">
<div
    class="pointer-events-none fixed inset-0 bg-cover bg-center opacity-[0.05]"
    style="background-image: url('https://images.unsplash.com/photo-1503376780353-7e6692767b70?auto=format&fit=crop&w=1800&q=80');"
></div>
<div class="pointer-events-none fixed inset-0 bg-[radial-gradient(circle_at_20%_12%,rgba(174,194,219,0.06),transparent_34%),radial-gradient(circle_at_84%_82%,rgba(77,98,124,0.06),transparent_36%)]"></div>
<div class="pointer-events-none fixed inset-0 bg-gradient-to-br from-black/96 via-black/90 to-[#05060a]"></div>

@php
    $role = auth()->user()->role ?? 'user';
    $dashboardUrl = route('dashboard');

    $baseLink = "flex items-center px-3 py-2 rounded-md transition border";
    $activeLink = "bg-[rgba(143,167,199,.20)] text-white border-white/20";
    $idleLink = "text-slate-300 border-transparent hover:border-white/10 hover:bg-white/5 hover:text-white";

    $isDashboard = request()->is('dashboard') || request()->is('*/dashboard');
    $isAdminDashboard = request()->is('admin/dashboard');
    $isAdminUsers = request()->is('admin/users*');
    $isModeratorDashboard = request()->is('moderator/dashboard');
    $isModeratorAppointments = request()->is('moderator/appointments*');
    $isModeratorCalendar = request()->is('moderator/calendar*');
    $isUserDashboard = request()->is('user/dashboard');
    $isUserInvoices = request()->is('user/invoices*');
@endphp

<div class="relative z-10 flex min-h-screen">
    <aside class="w-64 bg-black/80 border-r border-white/10 backdrop-blur-xl flex flex-col">
        <div class="px-6 py-5 border-b border-white/10">
            <div class="flex items-center">
                <img src="{{ asset('images/autoserve-logo.svg') }}" alt="AUTOSERVE Performance Atelier logo" class="h-10 w-auto">
            </div>
        </div>

        <nav class="flex-1 px-3 py-4 space-y-1 text-sm">
            <a href="{{ $dashboardUrl }}" class="{{ $baseLink }} {{ $isDashboard ? $activeLink : $idleLink }}">Dashboard</a>

            @if($role === 'user')
                <a href="{{ route('user.invoices.index') }}" class="{{ $baseLink }} {{ $isUserInvoices ? $activeLink : $idleLink }}">My Invoices</a>
            @endif

            @if($role === 'moderator')
                <a href="{{ route('moderator.appointments.index') }}" class="{{ $baseLink }} {{ $isModeratorAppointments ? $activeLink : $idleLink }}">Bookings</a>
                <a href="{{ route('moderator.calendar') }}" class="{{ $baseLink }} {{ $isModeratorCalendar ? $activeLink : $idleLink }}">Schedule</a>
            @endif

            @if($role === 'admin')
                <a href="{{ route('admin.dashboard') }}" class="{{ $baseLink }} {{ $isAdminDashboard ? $activeLink : $idleLink }}">Admin</a>
            @endif
        </nav>

        <div class="px-6 py-4 border-t border-white/10 text-sm text-slate-400">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="hover:text-white transition">Logout</button>
            </form>
        </div>
    </aside>

    <main class="flex-1 flex flex-col">
        <header class="h-16 border-b border-white/10 bg-black/75 backdrop-blur-md flex items-center justify-between px-8">
            <div>
                <h2 class="text-lg font-medium text-white">@yield('title')</h2>
                <p class="text-sm text-slate-400">@yield('subtitle')</p>
            </div>

            <div class="text-sm text-slate-400">
                {{ auth()->user()->name }} | {{ ucfirst(auth()->user()->role) }}
            </div>
        </header>

        <section class="flex-1 p-8">
            @yield('content')
        </section>
    </main>
</div>

<script>
    document.querySelectorAll('[data-count]').forEach(el => {
        const target = +el.dataset.count;
        let current = 0;
        const increment = Math.max(1, target / 40);

        const update = () => {
            current += increment;
            if (current >= target) {
                el.textContent = target;
            } else {
                el.textContent = Math.floor(current);
                requestAnimationFrame(update);
            }
        };

        update();
    });
</script>

@yield('scripts')

</body>
</html>
