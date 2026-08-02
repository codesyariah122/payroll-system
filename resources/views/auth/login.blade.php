<x-guest-layout>

    <div class="relative min-h-screen overflow-hidden bg-[#050816]">

        <!-- Background -->
        <div
            class="absolute inset-0 bg-[radial-gradient(circle_at_top_left,rgba(99,102,241,0.15),transparent_30%),radial-gradient(circle_at_bottom_right,rgba(6,182,212,0.15),transparent_30%)]">
        </div>

        <!-- Grid -->
        <div
            class="absolute inset-0 bg-[linear-gradient(rgba(255,255,255,0.02)_1px,transparent_1px),linear-gradient(90deg,rgba(255,255,255,0.02)_1px,transparent_1px)] bg-[size:80px_80px]">
        </div>

        <div class="relative z-10 mx-auto flex min-h-screen w-full max-w-7xl items-center px-6 py-10">

            <div class="grid w-full items-center gap-20 lg:grid-cols-[1.1fr_0.9fr]">

                <!-- LEFT CONTENT -->
                <div class="hidden flex-col justify-center lg:flex">

                    <div
                        class="inline-flex w-fit items-center gap-2 rounded-full border border-indigo-500/20 bg-indigo-500/10 px-4 py-2 text-sm font-medium text-indigo-300 backdrop-blur-xl">

                        Smart Payroll Platform

                    </div>

                    <h1 class="mt-8 max-w-xl text-7xl font-black leading-tight tracking-tight text-white">

                        Payroll Login
                        untuk Tim HC Operation.

                    </h1>

                    <p class="mt-6 max-w-lg text-lg leading-relaxed text-slate-400">

                        Akses dashboard payroll untuk mengelola data karyawan,
                        import Excel, preview slip PDF, dan antrean email payroll
                        dalam workspace perusahaan yang terpisah.

                    </p>

                    <!-- Stats -->
                    <div class="mt-10 grid max-w-xl grid-cols-3 gap-4">

                        <div class="rounded-3xl border border-white/10 bg-white/5 p-5 backdrop-blur-xl">

                            <p class="text-3xl font-black text-white">
                                Aman
                            </p>

                            <p class="mt-2 text-sm text-slate-400">
                                Data per Company
                            </p>

                        </div>

                        <div class="rounded-3xl border border-white/10 bg-white/5 p-5 backdrop-blur-xl">

                            <p class="text-3xl font-black text-white">
                                Excel
                            </p>

                            <p class="mt-2 text-sm text-slate-400">
                                Import Payroll
                            </p>

                        </div>

                        <div class="rounded-3xl border border-white/10 bg-white/5 p-5 backdrop-blur-xl">

                            <p class="text-3xl font-black text-white">
                                PDF
                            </p>

                            <p class="mt-2 text-sm text-slate-400">
                                Slip & Email Queue
                            </p>

                        </div>

                    </div>

                </div>

                <!-- LOGIN CARD -->
                <div class="flex items-center justify-center">

                    <div
                        class="relative w-full max-w-xl overflow-hidden rounded-[2.5rem] border border-white/10 bg-white/5 p-8 shadow-2xl backdrop-blur-2xl">

                        <!-- Glow -->
                        <div class="absolute -top-24 right-0 h-40 w-40 rounded-full bg-indigo-500/20 blur-3xl">
                        </div>

                        <!-- Header -->
                        <div class="relative z-10 text-center">

                            <div
                                class="mx-auto flex h-20 w-20 items-center justify-center rounded-2xl bg-white/5 ring-1 ring-white/10 backdrop-blur-xl overflow-hidden">

                                <span class="text-2xl font-black tracking-tight text-indigo-100">
                                    PS
                                </span>

                            </div>

                            <h2 class="mt-5 text-4xl font-black tracking-tight text-white">
                                Masuk Dashboard
                            </h2>

                            <p class="mt-3 text-sm leading-relaxed text-slate-400 max-w-sm mx-auto">
                                Gunakan akun admin perusahaan untuk melanjutkan pengelolaan payroll, karyawan, dan slip gaji.
                            </p>

                        </div>

                        <!-- Form -->
                        <form method="POST" action="{{ route('login') }}" class="relative z-10 mt-10 space-y-5">

                            @csrf

                            <!-- Email -->
                            <div>

                                <label class="mb-2 block text-sm font-medium text-slate-300">

                                    Email Admin

                                </label>

                                <input type="email" name="email" required autofocus autocomplete="username"
                                    placeholder="name@company.com"
                                    class="w-full rounded-2xl border border-white/10 bg-white/5 px-5 py-4 text-white placeholder:text-slate-500 focus:border-indigo-500 focus:ring-indigo-500" />

                            </div>

                            <!-- Password -->
                            <div x-data="{ show: false }">

                                <label class="mb-2 block text-sm font-medium text-slate-300">

                                    Password

                                </label>

                                <div class="relative">
                                    <input x-bind:type="show ? 'text' : 'password'" type="password" name="password" required autocomplete="current-password"
                                        placeholder="••••••••"
                                        class="w-full rounded-2xl border border-white/10 bg-white/5 px-5 py-4 pr-14 text-white placeholder:text-slate-500 focus:border-indigo-500 focus:ring-indigo-500" />

                                    <button type="button"
                                        class="absolute inset-y-0 right-0 flex w-14 items-center justify-center text-slate-400 transition hover:text-white focus:outline-none focus:ring-2 focus:ring-inset focus:ring-indigo-300"
                                        x-on:click="show = !show"
                                        x-bind:aria-label="show ? 'Sembunyikan password' : 'Lihat password'">
                                        <svg x-show="!show" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M2.25 12s3.75-6.75 9.75-6.75S21.75 12 21.75 12 18 18.75 12 18.75 2.25 12 2.25 12Z" />
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                        </svg>
                                        <svg x-show="show" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="m3 3 18 18" />
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M10.6 5.46A8.9 8.9 0 0 1 12 5.25c6 0 9.75 6.75 9.75 6.75a17.6 17.6 0 0 1-3.1 3.82M6.2 6.88C3.76 8.58 2.25 12 2.25 12s3.75 6.75 9.75 6.75a8.8 8.8 0 0 0 4.03-.98" />
                                        </svg>
                                    </button>
                                </div>

                            </div>

                            <!-- Actions -->
                            <div class="flex items-center justify-between">

                                <label class="flex items-center gap-3">

                                    <input type="checkbox" name="remember"
                                        class="rounded border-white/10 bg-white/5 text-indigo-500 focus:ring-indigo-500">

                                    <span class="text-sm text-slate-400">

                                        Ingat saya

                                    </span>

                                </label>

                                @if (Route::has('password.request'))
                                    <a href="{{ route('password.request') }}"
                                        class="text-sm font-medium text-indigo-300 hover:text-white">

                                        Lupa password?

                                    </a>
                                @endif

                            </div>

                            <!-- Button -->
                            <button type="submit"
                                class="group inline-flex w-full items-center justify-center gap-3 rounded-2xl bg-gradient-to-r from-indigo-500 via-violet-500 to-cyan-500 px-6 py-4 text-sm font-bold text-white shadow-2xl shadow-indigo-500/30 transition-all duration-300 hover:-translate-y-1 hover:shadow-indigo-500/50">

                                Login ke Dashboard

                                <svg xmlns="http://www.w3.org/2000/svg"
                                    class="h-5 w-5 transition-transform duration-300 group-hover:translate-x-1"
                                    fill="none" viewBox="0 0 24 24" stroke="currentColor">

                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13 7l5 5m0 0l-5 5m5-5H6" />

                                </svg>

                            </button>

                        </form>

                    </div>

                </div>

            </div>

        </div>

    </div>

</x-guest-layout>
