@extends('layouts.guest')

@section('content')
<div class="relative min-h-screen overflow-hidden bg-[#060709]">
    <div
        class="absolute inset-0 bg-cover bg-center opacity-35"
        style="background-image: url('https://images.unsplash.com/photo-1503376780353-7e6692767b70?auto=format&fit=crop&w=1800&q=80');"
    ></div>
    <div class="absolute inset-0 bg-[radial-gradient(circle_at_18%_25%,rgba(143,167,199,0.20),transparent_42%),radial-gradient(circle_at_82%_80%,rgba(82,102,126,0.20),transparent_40%)]"></div>
    <div class="absolute inset-0 bg-gradient-to-r from-black/92 via-black/72 to-[#0c0d12]/95"></div>

    <div class="relative z-10 min-h-screen px-4 py-8 sm:px-8 lg:px-14">
        <div class="mx-auto grid min-h-[calc(100vh-4rem)] w-full max-w-7xl items-center gap-10 lg:grid-cols-2 lg:gap-16">
            <section class="max-w-xl">
                <div class="mb-7">
                    <img src="{{ asset('images/autoserve-logo.svg') }}" alt="AUTOSERVE Performance Atelier logo" class="h-12 w-auto">
                </div>

                <p class="mb-4 text-xs uppercase tracking-[0.34em] text-white/60">Luxury Service Platform</p>
                <h1 class="text-4xl font-semibold leading-tight tracking-tight text-white sm:text-5xl">
                    Your premium garage experience starts here.
                </h1>
                <p class="mt-5 max-w-lg text-base leading-relaxed text-white/75 sm:text-lg">
                    Manage appointments, track services, and access a cleaner automotive workflow built for modern performance owners.
                </p>
            </section>

            <section class="w-full lg:flex lg:justify-end">
                <div class="w-full max-w-md rounded-3xl border border-white/10 bg-black/45 p-8 shadow-[0_20px_80px_rgba(0,0,0,0.65)] backdrop-blur-xl sm:p-10">
                    <h2 class="text-3xl font-bold text-white mb-2">Welcome back</h2>
                    <p class="text-neutral-300 mb-8">Sign in to continue to your dashboard</p>

                    @if (session('success'))
                        <div class="mb-5 rounded-2xl border border-emerald-400/30 bg-emerald-400/10 px-4 py-3 text-sm text-emerald-100">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="mb-5 rounded-2xl border border-rose-400/30 bg-rose-400/10 px-4 py-3 text-sm text-rose-100">
                            {{ $errors->first() }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('login') }}" class="space-y-5">
                        @csrf

                        <div>
                            <label class="block text-sm text-neutral-300 mb-1.5">Email</label>
                            <input
                                type="email"
                                name="email"
                                value="{{ old('email') }}"
                                required
                                autofocus
                                class="w-full px-4 py-3 rounded-xl bg-white/[0.05] text-white
                                       border border-white/10 placeholder:text-neutral-500
                                       focus:outline-none focus:ring-2 focus:ring-[#9fb1c9] focus:border-transparent transition"
                            >
                        </div>

                        <div>
                            <label class="block text-sm text-neutral-300 mb-1.5">Password</label>
                            <input
                                type="password"
                                name="password"
                                required
                                class="w-full px-4 py-3 rounded-xl bg-white/[0.05] text-white
                                       border border-white/10 placeholder:text-neutral-500
                                       focus:outline-none focus:ring-2 focus:ring-[#9fb1c9] focus:border-transparent transition"
                            >
                        </div>

                        <button
                            type="submit"
                            class="w-full py-3 rounded-xl bg-gradient-to-r from-[#3f536f] via-[#8fa7c7] to-[#3f536f]
                                   hover:brightness-110 text-black font-semibold transition duration-300"
                        >
                            Sign in
                        </button>
                    </form>

                    <div class="mt-6 text-center text-sm text-neutral-300">
                        Need a new account?
                        <a href="{{ route('register') }}" class="text-white hover:text-[#c9d8eb] transition">
                            Sign up
                        </a>
                    </div>
                </div>
            </section>
        </div>
    </div>
</div>
@endsection
