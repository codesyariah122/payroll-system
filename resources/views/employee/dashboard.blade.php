<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Dashboard Karyawan</h2>
                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Ringkasan slip gaji dan riwayat payroll Anda.
                </p>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="grid gap-6 md:grid-cols-2">
                <div
                    class="rounded-xl bg-white dark:bg-gray-800 p-6 shadow-sm border border-gray-200 dark:border-gray-700">
                    <p class="text-sm text-gray-500">Nama</p>
                    <p class="mt-3 text-2xl font-semibold text-gray-900 dark:text-white">{{ $employee->name }}</p>
                    <p class="text-sm text-gray-500 mt-1">NIP: {{ $employee->nip }}</p>
                    <p class="text-sm text-gray-500">Jabatan: {{ $employee->position?->name ?? '-' }}</p>
                </div>
                <div
                    class="rounded-xl bg-white dark:bg-gray-800 p-6 shadow-sm border border-gray-200 dark:border-gray-700">
                    <p class="text-sm text-gray-500">Total Slip Gaji</p>
                    <p class="mt-3 text-4xl font-semibold text-indigo-700 dark:text-indigo-300">{{ $nextPayrollCount }}
                    </p>
                    <p class="text-sm text-gray-500 mt-2">Slip gaji terakhir periode {{ $lastPaid?->period ?? '-' }}</p>
                </div>
            </div>

            <div class="rounded-xl bg-white dark:bg-gray-800 p-6 shadow-sm border border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Riwayat Slip Gaji</h3>
                    <a href="{{ route('employee.payrolls.index') }}"
                        class="text-indigo-600 hover:text-indigo-800 dark:text-indigo-400">Lihat semua</a>
                </div>

                @if ($payrolls->isEmpty())
                    <p class="mt-4 text-sm text-gray-500 dark:text-gray-400">Belum ada slip gaji tersedia.</p>
                @else
                    <div class="mt-4 space-y-3">
                        @foreach ($payrolls as $payroll)
                            <div class="rounded-xl bg-gray-50 dark:bg-gray-900 p-4">
                                <div class="flex items-center justify-between gap-4">
                                    <div>
                                        <p class="font-semibold text-gray-900 dark:text-gray-100">{{ $payroll->period }}
                                        </p>
                                        <p class="text-sm text-gray-500 dark:text-gray-400">Rp
                                            {{ number_format($payroll->total_salary, 0, ',', '.') }}</p>
                                    </div>
                                    <a href="{{ route('employee.payrolls.download', $payroll) }}"
                                        class="inline-flex items-center rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700">Download</a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
