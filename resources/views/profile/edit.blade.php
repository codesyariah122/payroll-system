<x-app-layout>

    <x-slot name="header">

        <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">

            <div>

                <p class="text-sm font-semibold uppercase tracking-[0.22em] text-indigo-400">
                    Account Settings
                </p>

                <h2 class="mt-2 text-3xl font-bold tracking-tight text-white">
                    Profile Settings
                </h2>

                <p class="mt-2 max-w-2xl text-sm leading-relaxed text-slate-400">
                    Kelola informasi akun, keamanan password,
                    dan pengaturan profil Anda secara aman.
                </p>

            </div>

            <!-- Profile Badge -->
            <div
                class="inline-flex items-center gap-3 rounded-3xl border border-white/10 bg-slate-900/60 px-4 py-3 backdrop-blur-xl">

                <div
                    class="flex h-12 w-12 items-center justify-center rounded-2xl bg-indigo-500/10 text-indigo-300 ring-1 ring-indigo-500/20">

                    <span class="text-sm font-semibold">
                        {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                    </span>

                </div>

                <div>

                    <p class="text-sm font-semibold text-white">
                        {{ Auth::user()->name }}
                    </p>

                    <p class="text-xs text-slate-400">
                        {{ Auth::user()->email }}
                    </p>

                </div>

            </div>

        </div>

    </x-slot>

    <div class="py-8">

        <div class="mx-auto max-w-7xl space-y-8 sm:px-6 lg:px-8">

            <!-- Hero Card -->
            <div
                class="relative overflow-hidden rounded-[2rem] border border-white/10 bg-gradient-to-br from-slate-900 via-slate-950 to-indigo-950 p-8 shadow-2xl">

                <!-- Glow -->
                <div class="absolute -top-24 right-0 h-72 w-72 rounded-full bg-indigo-500/10 blur-3xl">
                </div>

                <div class="absolute bottom-0 left-0 h-56 w-56 rounded-full bg-cyan-500/10 blur-3xl">
                </div>

                <div class="relative z-10 flex flex-col gap-8 lg:flex-row lg:items-center lg:justify-between">

                    <div class="max-w-2xl">

                        <p class="text-sm font-semibold uppercase tracking-[0.24em] text-indigo-400">
                            Personal Account
                        </p>

                        <h1 class="mt-4 text-4xl font-bold tracking-tight text-white">
                            Kelola Profil Anda
                        </h1>

                        <p class="mt-4 text-base leading-relaxed text-slate-300">
                            Update informasi akun, ubah password,
                            dan kontrol keamanan akun payroll Anda
                            dengan tampilan dashboard modern.
                        </p>

                    </div>

                    <div
                        class="flex h-32 w-32 items-center justify-center rounded-[2.5rem] border border-white/10 bg-white/5 backdrop-blur-xl">

                        <span class="text-5xl font-bold text-indigo-300">
                            {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                        </span>

                    </div>

                </div>

            </div>

            <!-- Content Grid -->
            <div class="grid gap-8 xl:grid-cols-3">

                <!-- Profile Info -->
                <div
                    class="overflow-hidden rounded-[2rem] border border-white/10 bg-slate-900/80 shadow-2xl backdrop-blur-xl xl:col-span-2">

                    <div class="border-b border-white/5 px-8 py-6">

                        <h3 class="text-xl font-semibold text-white">
                            Informasi Profil
                        </h3>

                        <p class="mt-2 text-sm text-slate-400">
                            Update nama, email, dan informasi akun Anda.
                        </p>

                    </div>

                    <div class="p-8">

                        <div class="max-w-2xl">
                            @include('profile.partials.update-profile-information-form')
                        </div>

                    </div>

                </div>

                <!-- Security -->
                <div class="space-y-8">

                    <!-- Password -->
                    <div
                        class="overflow-hidden rounded-[2rem] border border-white/10 bg-slate-900/80 shadow-2xl backdrop-blur-xl">

                        <div class="border-b border-white/5 px-6 py-5">

                            <h3 class="text-lg font-semibold text-white">
                                Security
                            </h3>

                            <p class="mt-1 text-sm text-slate-400">
                                Update password akun Anda.
                            </p>

                        </div>

                        <div class="p-6">

                            @include('profile.partials.update-password-form')

                        </div>

                    </div>

                    <!-- Delete -->
                    <div
                        class="overflow-hidden rounded-[2rem] border border-red-500/10 bg-red-500/[0.03] shadow-2xl backdrop-blur-xl">

                        <div class="border-b border-red-500/10 px-6 py-5">

                            <h3 class="text-lg font-semibold text-red-300">
                                Danger Zone
                            </h3>

                            <p class="mt-1 text-sm text-red-200/70">
                                Hapus akun secara permanen.
                            </p>

                        </div>

                        <div class="p-6">

                            @include('profile.partials.delete-user-form')

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>

</x-app-layout>
