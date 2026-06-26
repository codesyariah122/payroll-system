<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Riwayat Slip Gaji</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div
                class="overflow-hidden rounded-xl bg-white dark:bg-gray-800 shadow-sm border border-gray-200 dark:border-gray-700">
                <div class="p-6">
                    @if ($payrolls->isEmpty())
                        <p class="text-sm text-gray-500 dark:text-gray-400">Belum ada slip gaji tersedia.</p>
                    @else
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-900">
                                    <tr>
                                        <th
                                            class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">
                                            Periode</th>
                                        <th
                                            class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">
                                            Total Gaji</th>
                                        <th
                                            class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wide text-gray-500">
                                            Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200 dark:divide-gray-700 bg-white dark:bg-gray-800">
                                    @foreach ($payrolls as $payroll)
                                        <tr>
                                            <td class="px-4 py-4 text-sm text-gray-700 dark:text-gray-200">
                                                {{ $payroll->period }}</td>
                                            <td class="px-4 py-4 text-sm font-semibold text-gray-900 dark:text-white">Rp
                                                {{ number_format($payroll->total_salary, 0, ',', '.') }}</td>
                                            <td class="px-4 py-4 text-right text-sm font-medium">
                                                <a href="{{ route('employee.payrolls.show', $payroll) }}"
                                                    class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400">Lihat</a>
                                                <a href="{{ route('employee.payrolls.download', $payroll) }}"
                                                    class="ml-4 text-emerald-600 hover:text-emerald-900 dark:text-emerald-400">Download</a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-4">{{ $payrolls->links() }}</div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
