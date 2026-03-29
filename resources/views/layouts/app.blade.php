<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>AutoServe</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="relative min-h-screen overflow-x-hidden bg-[#05060a] text-white antialiased [font-family:'Manrope',sans-serif]">
<div
    class="pointer-events-none fixed inset-0 bg-cover bg-center opacity-[0.05]"
    style="background-image: url('https://images.unsplash.com/photo-1503376780353-7e6692767b70?auto=format&fit=crop&w=1800&q=80');"
></div>
<div class="pointer-events-none fixed inset-0 bg-[radial-gradient(circle_at_20%_12%,rgba(174,194,219,0.06),transparent_34%),radial-gradient(circle_at_84%_82%,rgba(77,98,124,0.06),transparent_36%)]"></div>
<div class="pointer-events-none fixed inset-0 bg-gradient-to-br from-black/96 via-black/90 to-[#05060a]"></div>

<header class="relative z-10 border-b border-white/10 bg-black/80 backdrop-blur-xl">
    <div class="mx-auto flex w-full max-w-7xl items-center justify-between px-6 py-4">
        <div class="flex items-center">
            <img src="{{ asset('images/autoserve-logo.svg') }}" alt="AUTOSERVE Performance Atelier logo" class="h-10 w-auto">
        </div>

        @auth
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button class="rounded-lg border border-white/15 px-4 py-2 text-sm text-white/85 transition hover:border-white/30 hover:bg-white/5 hover:text-white">
                Logout
            </button>
        </form>
        @endauth
    </div>
</header>

<main class="relative z-10 mx-auto w-full max-w-7xl px-6 py-8">
    @yield('content')
</main>

</body>
</html>
