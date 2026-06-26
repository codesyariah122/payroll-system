<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Detail Payroll</h2>
                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Slip gaji {{ $payroll->employee->name }} untuk
                    periode {{ $payroll->period }}.</p>
            </div>
            <a href="{{ route('admin.payrolls.index') }}"
                class="inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200">Kembali</a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="mx-auto max-w-4xl sm:px-6 lg:px-8">
            <div
                class="rounded-xl bg-white dark:bg-gray-800 shadow-sm border border-gray-200 dark:border-gray-700 p-6 space-y-6">
                <div class="grid gap-6 md:grid-cols-2">
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Nama</p>
                        <p class="mt-2 text-lg font-semibold text-gray-900 dark:text-white">
                            {{ $payroll->employee->name }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">NIP</p>
                        <p class="mt-2 text-lg font-semibold text-gray-900 dark:text-white">
                            {{ $payroll->employee->nip }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Periode</p>
                        <p class="mt-2 text-lg font-semibold text-gray-900 dark:text-white">{{ $payroll->period }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Total Gaji</p>
                        <p class="mt-2 text-lg font-semibold text-green-600 dark:text-green-400">Rp
                            {{ number_format($payroll->total_salary, 0, ',', '.') }}</p>
                    </div>
                </div>

                <div class="rounded-xl bg-gray-50 dark:bg-gray-900 p-4">
                    @include('payrolls.partials.slip-summary')
                </div>

                <div class="flex justify-end gap-3">
                    <a href="{{ route('admin.payrolls.index') }}"
                        class="inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200">Tutup</a>
                    <a href="{{ route('admin.payrolls.download', $payroll) }}"
                        class="inline-flex items-center rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700">Download
                        PDF</a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
