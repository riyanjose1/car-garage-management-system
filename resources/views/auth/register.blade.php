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

                <p class="mb-4 text-xs uppercase tracking-[0.34em] text-white/60">Bespoke Automotive Care</p>
                <h1 class="text-4xl font-semibold leading-tight tracking-tight text-white sm:text-5xl">
                    Crafted for drivers who expect more than ordinary service.
                </h1>
                <p class="mt-5 max-w-lg text-base leading-relaxed text-white/75 sm:text-lg">
                    AutoServe brings premium detailing, precision maintenance, and curated upgrades together in one seamless platform. Your car deserves concierge-level attention from booking to delivery.
                </p>

                <div class="mt-8 grid gap-3 text-sm text-white/80 sm:grid-cols-2">
                    <div class="rounded-2xl border border-white/10 bg-white/[0.03] px-4 py-3">Priority scheduling for premium members</div>
                    <div class="rounded-2xl border border-white/10 bg-white/[0.03] px-4 py-3">Certified specialists and genuine parts</div>
                    <div class="rounded-2xl border border-white/10 bg-white/[0.03] px-4 py-3">Live service updates and transparent pricing</div>
                    <div class="rounded-2xl border border-white/10 bg-white/[0.03] px-4 py-3">Luxury wash and detailing standards</div>
                </div>
            </section>

            <section class="w-full lg:flex lg:justify-end">
                <div class="w-full max-w-md rounded-3xl border border-white/10 bg-black/45 p-8 shadow-[0_20px_80px_rgba(0,0,0,0.65)] backdrop-blur-xl sm:p-10">
                    <h2 class="text-3xl font-bold tracking-tight text-white mb-2">
                        Create account
                    </h2>

                    <p class="text-neutral-300 mb-8">
                        Register to start using AutoServe
                    </p>

                    <form method="POST" action="{{ route('register') }}" class="space-y-5">
                        @csrf

                        <div>
                            <label class="block text-sm text-neutral-300 mb-1.5">Name</label>
                            <input
                                type="text"
                                name="name"
                                required
                                autofocus
                                class="w-full px-4 py-3 rounded-xl bg-white/[0.05] text-white
                                       border border-white/10 placeholder:text-neutral-500
                                       focus:outline-none focus:ring-2 focus:ring-[#9fb1c9] focus:border-transparent transition"
                            >
                        </div>

                        <div>
                            <label class="block text-sm text-neutral-300 mb-1.5">Email</label>
                            <input
                                type="email"
                                name="email"
                                required
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

                        <div>
                            <label class="block text-sm text-neutral-300 mb-1.5">Confirm Password</label>
                            <input
                                type="password"
                                name="password_confirmation"
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
                            Register
                        </button>
                    </form>

                    <div class="mt-6 text-center text-sm text-neutral-300">
                        Already have an account?
                        <a href="{{ route('login') }}" class="text-white hover:text-[#c9d8eb] transition">
                            Sign in
                        </a>
                    </div>
                </div>
            </section>
        </div>
    </div>
</div>
@endsection

