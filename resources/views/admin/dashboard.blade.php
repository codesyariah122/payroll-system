<x-app-layout>

    <x-slot name="header">
        <div
            class="relative overflow-hidden rounded-3xl border border-white/10 bg-gradient-to-br from-slate-900 via-slate-950 to-indigo-950 px-8 py-8 shadow-2xl">

            <!-- Glow -->
            <div class="absolute -top-24 right-0 h-72 w-72 rounded-full bg-indigo-500/20 blur-3xl">
            </div>

            <div class="absolute bottom-0 left-0 h-56 w-56 rounded-full bg-cyan-500/10 blur-3xl">
            </div>

            <div class="relative z-10 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">

                <div>
                    <div
                        class="mb-4 inline-flex items-center gap-2 rounded-full border border-indigo-500/20 bg-indigo-500/10 px-4 py-1 text-xs font-medium text-indigo-300">

                        Payroll Analytics
                    </div>

                    <h1 class="text-3xl font-bold tracking-tight text-white">
                        Dashboard Admin
                    </h1>

                    <p class="mt-3 max-w-2xl text-sm leading-relaxed text-slate-300">
                        Monitoring payroll, performa karyawan, statistik penggajian,
                        dan aktivitas perusahaan secara realtime.
                    </p>
                </div>

                <div class="grid grid-cols-2 gap-4 lg:w-[340px]">

                    <div class="rounded-2xl border border-white/10 bg-white/[0.03] p-4 backdrop-blur-xl">
                        <p class="text-xs uppercase tracking-wider text-slate-400">
                            Status
                        </p>

                        <div class="mt-3 flex items-center gap-2">
                            <div class="h-2 w-2 rounded-full bg-emerald-400"></div>

                            <span class="text-sm font-medium text-white">
                                System Active
                            </span>
                        </div>
                    </div>

                    <div class="rounded-2xl border border-white/10 bg-white/[0.03] p-4 backdrop-blur-xl">
                        <p class="text-xs uppercase tracking-wider text-slate-400">
                            Payroll
                        </p>

                        <p class="mt-3 text-lg font-bold text-white">
                            {{ $totalPayrolls }}
                        </p>
                    </div>

                </div>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-7xl space-y-8 sm:px-6 lg:px-8">

            <!-- KPI -->
            <div class="grid gap-6 lg:grid-cols-3">

                <!-- Card -->
                <div
                    class="group relative overflow-hidden rounded-3xl border border-white/10 bg-gradient-to-br from-slate-900 to-slate-800 p-6 shadow-xl transition duration-300 hover:-translate-y-1 hover:shadow-indigo-500/10">

                    <div class="absolute right-0 top-0 h-32 w-32 rounded-full bg-indigo-500/10 blur-3xl">
                    </div>

                    <div class="relative z-10 flex items-start justify-between">

                        <div>
                            <p class="text-sm text-slate-400">
                                Total Karyawan
                            </p>

                            <h3 class="mt-4 text-4xl font-bold text-white">
                                {{ $totalEmployees }}
                            </h3>

                            <p class="mt-3 text-xs text-emerald-400">
                                +12% dari bulan lalu
                            </p>
                        </div>

                        <div
                            class="flex h-14 w-14 items-center justify-center rounded-2xl bg-indigo-500/10 text-indigo-300 ring-1 ring-indigo-500/20">

                            <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">

                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 20h5V10a1 1 0 00-1-1h-4v11zM2 20h5V4H2v16zM9 20h6V4H9v16z" />
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Card -->
                <div
                    class="group relative overflow-hidden rounded-3xl border border-white/10 bg-gradient-to-br from-slate-900 to-slate-800 p-6 shadow-xl transition duration-300 hover:-translate-y-1 hover:shadow-cyan-500/10">

                    <div class="absolute right-0 top-0 h-32 w-32 rounded-full bg-cyan-500/10 blur-3xl">
                    </div>

                    <div class="relative z-10 flex items-start justify-between">

                        <div>
                            <p class="text-sm text-slate-400">
                                Total Payroll
                            </p>

                            <h3 class="mt-4 text-4xl font-bold text-white">
                                {{ $totalPayrolls }}
                            </h3>

                            <p class="mt-3 text-xs text-cyan-400">
                                Payroll diproses realtime
                            </p>
                        </div>

                        <div
                            class="flex h-14 w-14 items-center justify-center rounded-2xl bg-cyan-500/10 text-cyan-300 ring-1 ring-cyan-500/20">

                            <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">

                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8c-1.657 0-3 1.343-3 3v6h6v-6c0-1.657-1.343-3-3-3z" />
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Card -->
                <div
                    class="group relative overflow-hidden rounded-3xl border border-white/10 bg-gradient-to-br from-slate-900 to-slate-800 p-6 shadow-xl transition duration-300 hover:-translate-y-1 hover:shadow-purple-500/10">

                    <div class="absolute right-0 top-0 h-32 w-32 rounded-full bg-purple-500/10 blur-3xl">
                    </div>

                    <div class="relative z-10 flex items-start justify-between">

                        <div>
                            <p class="text-sm text-slate-400">
                                Payroll Terakhir
                            </p>

                            <h3 class="mt-4 text-2xl font-bold text-white">
                                {{ $monthlyPayroll->first()?->period ?? '-' }}
                            </h3>

                            <p class="mt-3 text-xs text-purple-400">
                                Update payroll terbaru
                            </p>
                        </div>

                        <div
                            class="flex h-14 w-14 items-center justify-center rounded-2xl bg-purple-500/10 text-purple-300 ring-1 ring-purple-500/20">

                            <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">

                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 7V3m8 4V3m-9 8h10m-11 9h12a2 2 0 002-2V7a2 2 0 00-2-2H6a2 2 0 00-2 2v11a2 2 0 002 2z" />
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Content -->
            <div class="grid gap-6 xl:grid-cols-3">

                <!-- Statistik -->
                <div
                    class="xl:col-span-2 rounded-3xl border border-white/10 bg-slate-900/80 p-6 shadow-2xl backdrop-blur-xl">

                    <div class="mb-6 flex items-center justify-between">
                        <div>
                            <h3 class="text-xl font-semibold text-white">
                                Statistik Payroll
                            </h3>

                            <p class="mt-1 text-sm text-slate-400">
                                Riwayat payroll bulanan perusahaan.
                            </p>
                        </div>

                        <div class="rounded-2xl bg-indigo-500/10 px-4 py-2 text-xs font-medium text-indigo-300">
                            Analytics
                        </div>
                    </div>

                    <div class="space-y-4">

                        @foreach ($monthlyPayroll as $month)
                            <div
                                class="group flex items-center justify-between rounded-2xl border border-white/5 bg-white/[0.03] px-5 py-4 transition hover:border-indigo-500/20 hover:bg-indigo-500/[0.03]">

                                <div>
                                    <p class="font-semibold text-white">
                                        {{ $month->period }}
                                    </p>

                                    <p class="mt-1 text-xs text-slate-400">
                                        Total payroll perusahaan
                                    </p>
                                </div>

                                <div class="text-right">
                                    <p class="text-lg font-bold text-emerald-400">
                                        Rp {{ number_format($month->total, 0, ',', '.') }}
                                    </p>

                                    <p class="text-xs text-slate-500">
                                        Payroll
                                    </p>
                                </div>
                            </div>
                        @endforeach

                    </div>
                </div>

                <!-- Activity -->
                <div class="rounded-3xl border border-white/10 bg-slate-900/80 p-6 shadow-2xl backdrop-blur-xl">

                    <div class="mb-6">
                        <h3 class="text-xl font-semibold text-white">
                            Payroll Terbaru
                        </h3>

                        <p class="mt-1 text-sm text-slate-400">
                            Aktivitas payroll terbaru.
                        </p>
                    </div>

                    <div class="space-y-4">

                        @forelse ($lastPayrolls as $payroll)
                            <div
                                class="rounded-2xl border border-white/5 bg-white/[0.03] p-4 transition hover:border-cyan-500/20 hover:bg-cyan-500/[0.03]">

                                <div class="flex items-start gap-3">

                                    <div
                                        class="flex h-11 w-11 items-center justify-center rounded-2xl bg-cyan-500/10 text-sm font-semibold text-cyan-300">

                                        {{ strtoupper(substr($payroll->employee->name, 0, 1)) }}
                                    </div>

                                    <div class="min-w-0 flex-1">

                                        <p class="truncate font-semibold text-white">
                                            {{ $payroll->employee->name }}
                                        </p>

                                        <p class="mt-1 text-xs text-slate-400">
                                            {{ $payroll->period }}
                                        </p>

                                        <p class="mt-3 text-sm font-semibold text-emerald-400">
                                            Rp {{ number_format($payroll->total_salary, 0, ',', '.') }}
                                        </p>
                                    </div>
                                </div>
                            </div>

                        @empty

                            <div class="rounded-2xl border border-dashed border-white/10 py-12 text-center">

                                <p class="text-sm text-slate-400">
                                    Belum ada payroll terbaru.
                                </p>
                            </div>
                        @endforelse

                    </div>
                </div>
            </div>

        </div>
    </div>

</x-app-layout>
