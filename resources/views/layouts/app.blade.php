<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <script>
        window.AppFlash = @json(session('status')
                ? ['type' => 'success', 'message' => session('status')]
                : (session('error')
                    ? ['type' => 'warning', 'message' => session('error')]
                    : null));
    </script>
</head>

<body class="min-h-screen bg-slate-950 font-sans antialiased">
    @php($company = Auth::user()?->company)

    <div x-data="{ sidebarCollapsed: false }" :class="sidebarCollapsed ? 'sidebar-collapsed' : ''"
        class="min-h-[100dvh] bg-slate-950">
        @include('layouts.navigation')

        <div class="flex flex-col lg:flex-row">
            <aside :class="sidebarCollapsed ? 'w-20' : 'w-full lg:w-80 xl:w-96'"
                class="shrink-0 border-t border-slate-200 bg-slate-950 text-slate-100 shadow-lg dark:border-slate-800 transition-all duration-200">
                <div
                    class="absolute top-0 right-0 h-full w-px bg-gradient-to-b from-transparent via-indigo-500/20 to-transparent">
                </div>

                <div class="px-4 py-6 lg:h-screen lg:sticky lg:top-0 flex flex-col">
                    <div class="mb-8">
                        <div class="flex items-center" :class="sidebarCollapsed ? 'justify-center' : 'justify-between'">
                            <div class="flex items-center gap-3">



                                <div x-show="!sidebarCollapsed" class="transition-all">
                                    @if ($company?->logo_path)
                                        <img src="{{ asset($company->logo_path) }}" alt="{{ $company->name }}"
                                            class="mb-4 h-12 w-auto max-w-40 object-contain">
                                    @endif

                                    <h2 class="text-lg font-semibold text-white">
                                        {{ $company?->name ?? config('app.name', 'Payroll') }}
                                    </h2>

                                    <p class="mt-1 text-sm text-slate-400">
                                        {{ $company?->description ?: 'Akses cepat ke fitur payroll dan karyawan.' }}
                                    </p>
                                </div>

                            </div>
                            <div>
                                <button @click="sidebarCollapsed = !sidebarCollapsed"
                                    class="flex items-center justify-center w-10 h-10 rounded-xl bg-slate-800/80 hover:bg-slate-700 text-slate-300 hover:text-white transition-all duration-200 focus:outline-none ring-1 ring-white/10 shrink-0">

                                    <!-- Collapse -->
                                    <svg x-show="!sidebarCollapsed" x-transition xmlns="http://www.w3.org/2000/svg"
                                        class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 19l-7-7 7-7" />
                                    </svg>

                                    <!-- Expand -->
                                    <svg x-show="sidebarCollapsed" x-transition xmlns="http://www.w3.org/2000/svg"
                                        class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 5l7 7-7 7" />
                                    </svg>

                                </button>
                            </div>
                        </div>
                    </div>

                    <nav class="space-y-2 flex-1">
                        <a href="{{ route('dashboard') }}"
                            class="flex items-center rounded-3xl px-4 py-3 text-sm font-semibold transition {{ request()->routeIs('dashboard') ? 'bg-indigo-600 text-white shadow-lg' : 'text-slate-200 hover:bg-slate-800/80 hover:text-white' }}"
                            :class="sidebarCollapsed ? 'justify-center px-2' : ''">
                            <span
                                class="inline-flex items-center justify-center shrink-0
    w-11 h-11 rounded-2xl
    text-indigo-400 bg-indigo-500/10
    ring-1 ring-indigo-500/20
    transition-all duration-200"
                                :class="sidebarCollapsed ? 'mx-auto' : 'mr-3'">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 13h8V3H3v10zM3 21h8v-6H3v6zM13 21h8V11h-8v10zM13 3v6h8V3h-8z" />
                                </svg>
                            </span>
                            <span x-show="!sidebarCollapsed">{{ __('Dashboard') }}</span>
                        </a>

                        @if (Auth::user()?->isAdmin())
                            <a href="{{ route('admin.employees.index') }}"
                                class="flex items-center rounded-3xl px-4 py-3 text-sm font-semibold transition {{ request()->routeIs('admin.employees.*') ? 'bg-indigo-600 text-white shadow-lg' : 'text-slate-200 hover:bg-slate-800/80 hover:text-white' }}"
                                :class="sidebarCollapsed ? 'justify-center px-2' : ''">
                                <span
                                    class="inline-flex items-center justify-center shrink-0
    w-11 h-11 rounded-2xl
    text-indigo-400 bg-indigo-500/10
    ring-1 ring-indigo-500/20
    transition-all duration-200"
                                    :class="sidebarCollapsed ? 'mx-auto' : 'mr-3'">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24"
                                        fill="none" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M17 20h5V10a1 1 0 00-1-1h-4v11zM2 20h5V4H2v16zM9 20h6V4H9v16z" />
                                    </svg>
                                </span>
                                <span x-show="!sidebarCollapsed">{{ __('Karyawan') }}</span>
                            </a>
                            <a href="{{ route('admin.departments.index') }}"
                                class="flex items-center rounded-3xl px-4 py-3 text-sm font-semibold transition {{ request()->routeIs('admin.departments.*') ? 'bg-indigo-600 text-white shadow-lg' : 'text-slate-200 hover:bg-slate-800/80 hover:text-white' }}"
                                :class="sidebarCollapsed ? 'justify-center px-2' : ''">
                                <span
                                    class="inline-flex items-center justify-center shrink-0
    w-11 h-11 rounded-2xl
    text-indigo-400 bg-indigo-500/10
    ring-1 ring-indigo-500/20
    transition-all duration-200"
                                    :class="sidebarCollapsed ? 'mx-auto' : 'mr-3'">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24"
                                        fill="none" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M3 7h18M3 12h18M7 17h10" />
                                    </svg>
                                </span>
                                <span x-show="!sidebarCollapsed">{{ __('Departemen') }}</span>
                            </a>
                            <a href="{{ route('admin.positions.index') }}"
                                class="flex items-center rounded-3xl px-4 py-3 text-sm font-semibold transition {{ request()->routeIs('admin.positions.*') ? 'bg-indigo-600 text-white shadow-lg' : 'text-slate-200 hover:bg-slate-800/80 hover:text-white' }}"
                                :class="sidebarCollapsed ? 'justify-center px-2' : ''">
                                <span
                                    class="inline-flex items-center justify-center shrink-0
    w-11 h-11 rounded-2xl
    text-indigo-400 bg-indigo-500/10
    ring-1 ring-indigo-500/20
    transition-all duration-200"
                                    :class="sidebarCollapsed ? 'mx-auto' : 'mr-3'">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24"
                                        fill="none" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 8c1.657 0 3-1.343 3-3S13.657 2 12 2 9 3.343 9 5s1.343 3 3 3zM6 20v-2a4 4 0 014-4h4a4 4 0 014 4v2" />
                                    </svg>
                                </span>
                                <span x-show="!sidebarCollapsed">{{ __('Posisi') }}</span>
                            </a>
                            <a href="{{ route('admin.payrolls.index') }}"
                                class="flex items-center rounded-3xl px-4 py-3 text-sm font-semibold transition {{ request()->routeIs('admin.payrolls.*') ? 'bg-indigo-600 text-white shadow-lg' : 'text-slate-200 hover:bg-slate-800/80 hover:text-white' }}"
                                :class="sidebarCollapsed ? 'justify-center px-2' : ''">
                                <span
                                    class="inline-flex items-center justify-center shrink-0
    w-11 h-11 rounded-2xl
    text-indigo-400 bg-indigo-500/10
    ring-1 ring-indigo-500/20
    transition-all duration-200"
                                    :class="sidebarCollapsed ? 'mx-auto' : 'mr-3'">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24"
                                        fill="none" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 1v2m0 18v2M17.657 6.343A8 8 0 1012 20v-2a6 6 0 110-12V4a8 8 0 005.657 2.343z" />
                                    </svg>
                                </span>
                                <span x-show="!sidebarCollapsed">{{ __('Payroll') }}</span>
                            </a>
                            <a href="{{ route('admin.payroll-templates.index') }}"
                                class="flex items-center rounded-3xl px-4 py-3 text-sm font-semibold transition {{ request()->routeIs('admin.payroll-templates.*') ? 'bg-indigo-600 text-white shadow-lg' : 'text-slate-200 hover:bg-slate-800/80 hover:text-white' }}"
                                :class="sidebarCollapsed ? 'justify-center px-2' : ''">
                                <span
                                    class="inline-flex items-center justify-center shrink-0
    w-11 h-11 rounded-2xl
    text-indigo-400 bg-indigo-500/10
    ring-1 ring-indigo-500/20
    transition-all duration-200"
                                    :class="sidebarCollapsed ? 'mx-auto' : 'mr-3'">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24"
                                        fill="none" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M7 3h7l5 5v13H7V3zM14 3v5h5M5 7H3v14h10v-2" />
                                    </svg>
                                </span>
                                <span x-show="!sidebarCollapsed">{{ __('Template Slip') }}</span>
                            </a>
                            {{-- <a href="{{ route('admin.payrolls.import') }}"
                                class="flex items-center rounded-3xl px-4 py-3 text-sm font-semibold transition {{ request()->routeIs('admin.payrolls.import') ? 'bg-indigo-600 text-white shadow-lg' : 'text-slate-200 hover:bg-slate-800/80 hover:text-white' }}"
                                :class="sidebarCollapsed ? 'justify-center px-2' : ''">
                                <span
                                    class="inline-flex items-center justify-center shrink-0
    w-11 h-11 rounded-2xl
    text-indigo-400 bg-indigo-500/10
    ring-1 ring-indigo-500/20
    transition-all duration-200"
                                    :class="sidebarCollapsed ? 'mx-auto' : 'mr-3'">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24"
                                        fill="none" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 2v12m0 0l3-3m-3 3l-3-3M21 12v7a2 2 0 01-2 2H5a2 2 0 01-2-2v-7" />
                                    </svg>
                                </span>
                                <span x-show="!sidebarCollapsed">{{ __('Import Payroll') }}</span>
                            </a> --}}
                        @elseif (Auth::user()?->isEmployee())
                            <a href="{{ route('employee.payrolls.index') }}"
                                class="flex items-center rounded-3xl px-4 py-3 text-sm font-semibold transition {{ request()->routeIs('employee.payrolls.*') ? 'bg-indigo-600 text-white shadow-lg' : 'text-slate-200 hover:bg-slate-800/80 hover:text-white' }}"
                                :class="sidebarCollapsed ? 'justify-center px-2' : ''">
                                <span
                                    class="inline-flex items-center justify-center shrink-0
    w-11 h-11 rounded-2xl
    text-indigo-400 bg-indigo-500/10
    ring-1 ring-indigo-500/20
    transition-all duration-200"
                                    :class="sidebarCollapsed ? 'mx-auto' : 'mr-3'">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24"
                                        fill="none" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 17v-6a2 2 0 012-2h2a2 2 0 012 2v6M12 7v.01" />
                                    </svg>
                                </span>
                                <span x-show="!sidebarCollapsed">{{ __('Slip Gaji Saya') }}</span>
                            </a>
                        @endif

                        <a href="{{ route('profile.edit') }}"
                            class="flex items-center rounded-3xl px-4 py-3 text-sm font-semibold transition {{ request()->routeIs('profile.*') ? 'bg-indigo-600 text-white shadow-lg' : 'text-slate-200 hover:bg-slate-800/80 hover:text-white' }}"
                            :class="sidebarCollapsed ? 'justify-center px-2' : ''">
                            <span
                                class="inline-flex items-center justify-center shrink-0
    w-11 h-11 rounded-2xl
    text-indigo-400 bg-indigo-500/10
    ring-1 ring-indigo-500/20
    transition-all duration-200"
                                :class="sidebarCollapsed ? 'mx-auto' : 'mr-3'">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5.121 17.804A13.937 13.937 0 0112 15c2.5 0 4.847.605 6.879 1.804M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                            </span>
                            <span x-show="!sidebarCollapsed">{{ __('Profil Saya') }}</span>
                        </a>
                    </nav>

                    <div class="mt-auto pt-6">
                        <div
                            class="flex items-center gap-3 rounded-2xl border border-white/5 bg-slate-900/40 px-3 py-3 backdrop-blur-md">

                            <!-- Avatar -->
                            <div
                                class="flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl bg-indigo-500/10 text-indigo-300 ring-1 ring-indigo-500/20">

                                <span class="text-sm font-semibold">
                                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                                </span>
                            </div>

                            <!-- Info -->
                            <div x-show="!sidebarCollapsed" class="min-w-0 flex-1">
                                <p class="truncate text-sm font-semibold text-white">
                                    {{ Auth::user()->name }}
                                </p>

                                <p class="truncate text-xs text-slate-400">
                                    {{ Auth::user()->email }}
                                </p>

                                <span
                                    class="mt-2 inline-flex rounded-full bg-indigo-500/10 px-2 py-1 text-[10px] font-medium text-indigo-300">
                                    {{ Auth::user()?->isAdmin() ? 'Admin' : 'Karyawan' }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </aside>

            <div class="min-w-0 flex-1
    bg-[#020617]
    border-l border-white/5
    shadow-inner shadow-black/20">
                @isset($header)
                    <header
                        class="bg-white/90 dark:bg-slate-900/90 backdrop-blur-sm shadow-sm border-b border-slate-200 dark:border-slate-800">
                        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                            {{ $header }}
                        </div>
                    </header>
                @endisset

                <main class="min-w-0 py-6 overflow-hidden">
                    <div class="max-w-7xl mx-auto overflow-hidden sm:px-6 lg:px-8">
                        <div class="space-y-6">
                            {{ $slot }}
                        </div>
                    </div>
                </main>
            </div>
        </div>
    </div>
</body>

</html>
