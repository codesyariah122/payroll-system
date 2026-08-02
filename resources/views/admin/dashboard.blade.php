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

            @if ($company)
                <section
                    class="rounded-3xl border border-white/10 bg-slate-900/80 p-6 shadow-2xl backdrop-blur-xl">
                    <div class="flex flex-col gap-6 lg:flex-row lg:items-start lg:justify-between">
                        <div class="flex min-w-0 items-start gap-4">
                            <div
                                class="flex h-16 w-16 shrink-0 items-center justify-center overflow-hidden rounded-2xl border border-white/10 bg-slate-950/70">
                                @if ($company->logo_path)
                                    <img src="{{ asset($company->logo_path) }}" alt="{{ $company->name }}"
                                        class="h-full w-full object-contain p-2">
                                @else
                                    <span class="text-sm font-bold uppercase text-indigo-300">
                                        {{ strtoupper(substr($company->name, 0, 2)) }}
                                    </span>
                                @endif
                            </div>

                            <div class="min-w-0">
                                <p class="text-xs font-semibold uppercase tracking-[0.35em] text-emerald-300">
                                    Profil Perusahaan
                                </p>

                                <h2 class="mt-2 truncate text-2xl font-bold text-white">
                                    {{ $company->name }}
                                </h2>

                                @if ($company->description)
                                    <p class="mt-2 max-w-3xl text-sm leading-relaxed text-slate-300">
                                        {{ $company->description }}
                                    </p>
                                @endif
                            </div>
                        </div>

                        <div class="grid gap-3 text-sm text-slate-300 sm:grid-cols-2 lg:w-[480px]">
                            @if ($company->email)
                                <div class="rounded-2xl border border-white/5 bg-white/[0.03] px-4 py-3">
                                    <p class="text-xs uppercase tracking-wider text-slate-500">Email</p>
                                    <p class="mt-1 truncate font-medium text-white">{{ $company->email }}</p>
                                </div>
                            @endif

                            @if ($company->phone)
                                <div class="rounded-2xl border border-white/5 bg-white/[0.03] px-4 py-3">
                                    <p class="text-xs uppercase tracking-wider text-slate-500">Telepon</p>
                                    <p class="mt-1 truncate font-medium text-white">{{ $company->phone }}</p>
                                </div>
                            @endif

                            @if ($company->city || $company->province)
                                <div class="rounded-2xl border border-white/5 bg-white/[0.03] px-4 py-3">
                                    <p class="text-xs uppercase tracking-wider text-slate-500">Domisili</p>
                                    <p class="mt-1 truncate font-medium text-white">
                                        {{ collect([$company->city, $company->province])->filter()->join(', ') }}
                                    </p>
                                </div>
                            @endif

                            @if ($company->website)
                                <div class="rounded-2xl border border-white/5 bg-white/[0.03] px-4 py-3">
                                    <p class="text-xs uppercase tracking-wider text-slate-500">Website</p>
                                    <p class="mt-1 truncate font-medium text-white">{{ $company->website }}</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    @if ($company->address)
                        <div class="mt-5 rounded-2xl border border-white/5 bg-white/[0.03] px-4 py-3">
                            <p class="text-xs uppercase tracking-wider text-slate-500">Alamat</p>
                            <p class="mt-1 text-sm leading-relaxed text-slate-200">{{ $company->address }}</p>
                        </div>
                    @endif
                </section>
            @endif

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

            @php
                $analysisTotal = max($payrollAnalysis['total_payrolls'], 1);
                $salaryPieStops = [];
                $salaryStart = 0;

                foreach ($payrollAnalysis['salary_bands'] as $band) {
                    $slice = ($band['count'] / $analysisTotal) * 100;
                    $salaryEnd = $salaryStart + $slice;
                    $salaryPieStops[] = "{$band['color']} {$salaryStart}% {$salaryEnd}%";
                    $salaryStart = $salaryEnd;
                }

                $salaryPie = $payrollAnalysis['total_payrolls'] > 0
                    ? implode(', ', $salaryPieStops)
                    : '#1e293b 0% 100%';

                $statusColors = [
                    'sent' => '#34d399',
                    'failed' => '#fb7185',
                    'skipped' => '#94a3b8',
                    'queued' => '#facc15',
                    'pending' => '#818cf8',
                ];
                $statusPieStops = [];
                $statusStart = 0;

                foreach ($payrollAnalysis['email_statuses'] as $status) {
                    $slice = ($status['count'] / $analysisTotal) * 100;
                    $statusEnd = $statusStart + $slice;
                    $color = $statusColors[$status['status']] ?? '#818cf8';
                    $statusPieStops[] = "{$color} {$statusStart}% {$statusEnd}%";
                    $statusStart = $statusEnd;
                }

                $statusPie = $payrollAnalysis['total_payrolls'] > 0
                    ? implode(', ', $statusPieStops)
                    : '#1e293b 0% 100%';

                $complianceTotal = max(array_sum($payrollAnalysis['workday_compliance']), 1);
                $departmentMax = max($payrollAnalysis['department_payroll']->max('total') ?: 0, 1);
                $monthlyMax = max($monthlyPayroll->max('total') ?: 0, 1);
            @endphp

            <section class="grid gap-6 xl:grid-cols-3">
                <div class="rounded-3xl border border-white/10 bg-slate-900/80 p-6 shadow-2xl backdrop-blur-xl">
                    <p class="text-sm text-slate-400">Rata-rata Gaji Bersih</p>
                    <h3 class="mt-4 text-3xl font-bold text-white">
                        Rp {{ number_format($payrollAnalysis['average_salary'], 0, ',', '.') }}
                    </h3>
                    <p class="mt-3 text-xs text-emerald-300">
                        Berdasarkan {{ $payrollAnalysis['total_payrolls'] }} payroll {{ $latestPeriod ? "periode {$latestPeriod}" : 'terbaru' }}.
                    </p>
                </div>

                <div class="rounded-3xl border border-white/10 bg-slate-900/80 p-6 shadow-2xl backdrop-blur-xl">
                    <p class="text-sm text-slate-400">Realisasi Hari Kerja</p>
                    <div class="mt-4 flex items-end justify-between gap-4">
                        <h3 class="text-3xl font-bold text-white">{{ $payrollAnalysis['workday_rate'] }}%</h3>
                        <p class="text-right text-xs text-slate-400">
                            {{ number_format($payrollAnalysis['total_work_days'], 0, ',', '.') }} /
                            {{ number_format($payrollAnalysis['total_target_work_days'], 0, ',', '.') }} hari
                        </p>
                    </div>
                    <div class="mt-5 h-3 overflow-hidden rounded-full bg-slate-800">
                        <div class="h-full rounded-full bg-gradient-to-r from-cyan-400 to-emerald-400"
                            style="width: {{ $payrollAnalysis['workday_rate'] }}%"></div>
                    </div>
                </div>

                <div class="rounded-3xl border border-white/10 bg-slate-900/80 p-6 shadow-2xl backdrop-blur-xl">
                    <p class="text-sm text-slate-400">Total Jam Lembur</p>
                    <h3 class="mt-4 text-3xl font-bold text-white">
                        {{ number_format($payrollAnalysis['total_overtime_hours'], 1, ',', '.') }} jam
                    </h3>
                    <p class="mt-3 text-xs text-purple-300">
                        Rata-rata {{ number_format($payrollAnalysis['average_overtime_hours'], 1, ',', '.') }} jam per payroll.
                    </p>
                </div>
            </section>

            <section class="grid gap-6 xl:grid-cols-5">
                <div class="rounded-3xl border border-white/10 bg-slate-900/80 p-6 shadow-2xl backdrop-blur-xl xl:col-span-2">
                    <div class="mb-6">
                        <h3 class="text-xl font-semibold text-white">Distribusi Gaji</h3>
                        <p class="mt-1 text-sm text-slate-400">Sebaran take home pay karyawan pada periode terbaru.</p>
                    </div>

                    <div class="flex flex-col items-center gap-6 sm:flex-row">
                        <div class="relative h-44 w-44 shrink-0 rounded-full"
                            style="background: conic-gradient({{ $salaryPie }});">
                            <div class="absolute inset-8 rounded-full border border-white/10 bg-slate-950/95"></div>
                            <div class="absolute inset-0 flex items-center justify-center text-center">
                                <div>
                                    <p class="text-2xl font-bold text-white">{{ $payrollAnalysis['total_payrolls'] }}</p>
                                    <p class="text-xs text-slate-400">Payroll</p>
                                </div>
                            </div>
                        </div>

                        <div class="w-full space-y-3">
                            @foreach ($payrollAnalysis['salary_bands'] as $band)
                                @php($percentage = $payrollAnalysis['total_payrolls'] > 0 ? round(($band['count'] / $analysisTotal) * 100) : 0)
                                <div>
                                    <div class="mb-1 flex items-center justify-between gap-3 text-sm">
                                        <span class="flex items-center gap-2 text-slate-300">
                                            <span class="h-2.5 w-2.5 rounded-full" style="background-color: {{ $band['color'] }}"></span>
                                            {{ $band['label'] }}
                                        </span>
                                        <span class="font-semibold text-white">{{ $band['count'] }} <span class="text-slate-500">({{ $percentage }}%)</span></span>
                                    </div>
                                    <div class="h-2 overflow-hidden rounded-full bg-slate-800">
                                        <div class="h-full rounded-full" style="width: {{ $percentage }}%; background-color: {{ $band['color'] }}"></div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="rounded-3xl border border-white/10 bg-slate-900/80 p-6 shadow-2xl backdrop-blur-xl xl:col-span-3">
                    <div class="mb-6 flex items-center justify-between gap-4">
                        <div>
                            <h3 class="text-xl font-semibold text-white">Payroll per Departemen</h3>
                            <p class="mt-1 text-sm text-slate-400">Area biaya payroll terbesar untuk bahan evaluasi HC.</p>
                        </div>
                        <span class="rounded-2xl bg-cyan-500/10 px-4 py-2 text-xs font-medium text-cyan-300">
                            Top 5
                        </span>
                    </div>

                    <div class="space-y-4">
                        @forelse ($payrollAnalysis['department_payroll'] as $department)
                            @php($width = round(($department['total'] / $departmentMax) * 100, 1))
                            <div class="rounded-2xl border border-white/5 bg-white/[0.03] p-4">
                                <div class="mb-3 flex items-center justify-between gap-4">
                                    <div class="min-w-0">
                                        <p class="truncate font-semibold text-white">{{ $department['name'] }}</p>
                                        <p class="mt-1 text-xs text-slate-500">{{ $department['count'] }} payroll</p>
                                    </div>
                                    <p class="shrink-0 text-sm font-bold text-emerald-300">
                                        Rp {{ number_format($department['total'], 0, ',', '.') }}
                                    </p>
                                </div>
                                <div class="h-3 overflow-hidden rounded-full bg-slate-800">
                                    <div class="h-full rounded-full bg-gradient-to-r from-cyan-400 to-indigo-400"
                                        style="width: {{ $width }}%"></div>
                                </div>
                            </div>
                        @empty
                            <div class="rounded-2xl border border-dashed border-white/10 py-12 text-center">
                                <p class="text-sm text-slate-400">Belum ada data departemen payroll.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </section>

            <section class="grid gap-6 xl:grid-cols-3">
                <div class="rounded-3xl border border-white/10 bg-slate-900/80 p-6 shadow-2xl backdrop-blur-xl">
                    <h3 class="text-xl font-semibold text-white">Kepatuhan Hari Kerja</h3>
                    <p class="mt-1 text-sm text-slate-400">Perbandingan hadir terhadap target hari kerja.</p>

                    <div class="mt-6 h-4 overflow-hidden rounded-full bg-slate-800">
                        @php($underWidth = round(($payrollAnalysis['workday_compliance']['under'] / $complianceTotal) * 100, 1))
                        @php($targetWidth = round(($payrollAnalysis['workday_compliance']['on_target'] / $complianceTotal) * 100, 1))
                        @php($overWidth = round(($payrollAnalysis['workday_compliance']['over'] / $complianceTotal) * 100, 1))
                        <div class="flex h-full">
                            <div class="bg-rose-400" style="width: {{ $underWidth }}%"></div>
                            <div class="bg-emerald-400" style="width: {{ $targetWidth }}%"></div>
                            <div class="bg-cyan-400" style="width: {{ $overWidth }}%"></div>
                        </div>
                    </div>

                    <div class="mt-5 grid gap-3 text-sm">
                        <div class="flex items-center justify-between">
                            <span class="flex items-center gap-2 text-slate-300"><span class="h-2.5 w-2.5 rounded-full bg-rose-400"></span>Di bawah target</span>
                            <span class="font-semibold text-white">{{ $payrollAnalysis['workday_compliance']['under'] }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="flex items-center gap-2 text-slate-300"><span class="h-2.5 w-2.5 rounded-full bg-emerald-400"></span>Sesuai target</span>
                            <span class="font-semibold text-white">{{ $payrollAnalysis['workday_compliance']['on_target'] }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="flex items-center gap-2 text-slate-300"><span class="h-2.5 w-2.5 rounded-full bg-cyan-400"></span>Di atas target</span>
                            <span class="font-semibold text-white">{{ $payrollAnalysis['workday_compliance']['over'] }}</span>
                        </div>
                    </div>
                </div>

                <div class="rounded-3xl border border-white/10 bg-slate-900/80 p-6 shadow-2xl backdrop-blur-xl">
                    <h3 class="text-xl font-semibold text-white">Status Email Slip</h3>
                    <p class="mt-1 text-sm text-slate-400">Kondisi distribusi slip gaji ke karyawan.</p>

                    <div class="mt-6 flex items-center gap-5">
                        <div class="relative h-32 w-32 shrink-0 rounded-full"
                            style="background: conic-gradient({{ $statusPie }});">
                            <div class="absolute inset-6 rounded-full border border-white/10 bg-slate-950/95"></div>
                        </div>

                        <div class="w-full space-y-3">
                            @forelse ($payrollAnalysis['email_statuses'] as $status)
                                @php($statusColor = $statusColors[$status['status']] ?? '#818cf8')
                                <div class="flex items-center justify-between gap-3 text-sm">
                                    <span class="flex items-center gap-2 text-slate-300">
                                        <span class="h-2.5 w-2.5 rounded-full" style="background-color: {{ $statusColor }}"></span>
                                        {{ $status['label'] }}
                                    </span>
                                    <span class="font-semibold text-white">{{ $status['count'] }}</span>
                                </div>
                            @empty
                                <p class="text-sm text-slate-400">Belum ada status email.</p>
                            @endforelse
                        </div>
                    </div>
                </div>

                <div class="rounded-3xl border border-white/10 bg-slate-900/80 p-6 shadow-2xl backdrop-blur-xl">
                    <h3 class="text-xl font-semibold text-white">Top Lembur</h3>
                    <p class="mt-1 text-sm text-slate-400">Karyawan dengan jam lembur tertinggi.</p>

                    <div class="mt-6 space-y-3">
                        @forelse ($payrollAnalysis['top_overtime_employees'] as $employee)
                            <div class="flex items-center justify-between gap-4 rounded-2xl border border-white/5 bg-white/[0.03] px-4 py-3">
                                <div class="min-w-0">
                                    <p class="truncate text-sm font-semibold text-white">{{ $employee['name'] }}</p>
                                    <p class="mt-1 text-xs text-slate-500">{{ $employee['period'] }}</p>
                                </div>
                                <span class="shrink-0 rounded-full bg-purple-500/10 px-3 py-1 text-xs font-bold text-purple-200">
                                    {{ number_format($employee['hours'], 1, ',', '.') }} jam
                                </span>
                            </div>
                        @empty
                            <div class="rounded-2xl border border-dashed border-white/10 py-10 text-center">
                                <p class="text-sm text-slate-400">Belum ada data lembur.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </section>

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
