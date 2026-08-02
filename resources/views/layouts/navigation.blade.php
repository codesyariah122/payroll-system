<nav x-data="{ open: false }"
    class="sticky top-0 z-50 border-b border-white/5 bg-slate-950/80 backdrop-blur-2xl shadow-[0_1px_0_rgba(255,255,255,0.03)]">
    @php($company = Auth::user()?->company)

    <div class="px-4 sm:px-6 lg:px-8">
        <div class="flex h-16 items-center justify-between">

            <!-- Left -->
            <div class="flex items-center gap-4">

                <!-- Logo -->
                <a href="{{ route('dashboard') }}" class="flex items-center gap-3">

                    <div class="flex items-center justify-center shrink-0">

                        @if ($company?->logo_path)
                            <img src="{{ asset($company->logo_path) }}" alt="{{ $company->name }}" class="h-9 w-auto max-w-36 object-contain" />
                        @else
                            <x-application-logo class="h-9 w-auto object-contain" />
                        @endif

                    </div>

                    {{-- <div class="hidden sm:block">
                        <p class="text-sm font-semibold text-white">
                            {{ config('app.name', 'Payroll') }}
                        </p>

                        <p class="text-xs text-slate-400">
                            Management System
                        </p>
                    </div> --}}

                </a>
            </div>

            <!-- Right -->
            <div class="hidden sm:flex sm:items-center">

                <x-dropdown align="right" width="72">

                    <!-- Trigger -->
                    <x-slot name="trigger">
                        <button
                            class="group flex items-center gap-3 rounded-2xl border border-white/5 bg-white/[0.03] px-3 py-2 transition-all duration-200 hover:border-indigo-500/20 hover:bg-white/[0.05]">

                            <!-- Avatar -->
                            <div
                                class="flex h-10 w-10 items-center justify-center rounded-2xl bg-indigo-500/10 text-sm font-semibold text-indigo-300 ring-1 ring-indigo-500/20">

                                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                            </div>

                            <!-- Info -->
                            <div class="text-left">
                                <p class="text-sm font-semibold text-white leading-tight">
                                    {{ Auth::user()->name }}
                                </p>

                                <p class="text-xs text-slate-400 leading-tight">
                                    {{ Auth::user()?->isAdmin() ? 'Administrator' : 'Karyawan' }}
                                </p>
                            </div>

                            <!-- Icon -->
                            <svg xmlns="http://www.w3.org/2000/svg"
                                class="h-4 w-4 text-slate-500 transition group-hover:text-indigo-300" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">

                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                    </x-slot>

                    <!-- Content -->
                    <x-slot name="content">

                        <div class="px-4 py-3 border-b border-white/5 bg-slate-900">
                            <p class="text-sm font-semibold text-white">
                                {{ Auth::user()->name }}
                            </p>

                            <p class="truncate text-xs text-slate-400">
                                {{ Auth::user()->email }}
                            </p>
                        </div>

                        <div class="py-2 bg-slate-950">

                            <x-dropdown-link :href="route('profile.edit')"
                                class="flex items-center gap-2 text-slate-300 hover:bg-slate-900 hover:text-white">

                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">

                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5.121 17.804A13.937 13.937 0 0112 15c2.5 0 4.847.605 6.879 1.804M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>

                                {{ __('Profil Saya') }}
                            </x-dropdown-link>

                            <form method="POST" action="{{ route('logout') }}">
                                @csrf

                                <x-dropdown-link :href="route('logout')"
                                    class="flex items-center gap-2 text-red-300 hover:bg-red-500/10 hover:text-red-200"
                                    onclick="event.preventDefault(); this.closest('form').submit();">

                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">

                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1m0-10V7" />
                                    </svg>

                                    {{ __('Logout') }}
                                </x-dropdown-link>
                            </form>

                        </div>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Mobile Hamburger -->
            <div class="flex items-center sm:hidden">
                <button @click="open = ! open"
                    class="inline-flex items-center justify-center rounded-xl border border-white/5 bg-white/[0.03] p-2 text-slate-400 transition hover:text-white hover:bg-white/[0.05]">

                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">

                        <path :class="{ 'hidden': open, 'inline-flex': !open }" class="inline-flex"
                            stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16" />

                        <path :class="{ 'hidden': !open, 'inline-flex': open }" class="hidden" stroke-linecap="round"
                            stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

        </div>
    </div>
</nav>
