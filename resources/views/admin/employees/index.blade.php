<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Manajemen Karyawan</h2>
                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Data karyawan, jabatan, dan status aktif.</p>
            </div>
            <div class="flex flex-wrap items-center gap-3">

                <!-- Import -->
                <a href="{{ route('admin.employees.import') }}"
                    class="inline-flex items-center gap-2 rounded-2xl border border-cyan-500/20 bg-cyan-500/10 px-5 py-3 text-sm font-semibold text-cyan-300 shadow-lg shadow-cyan-500/10 transition-all duration-200 hover:-translate-y-0.5 hover:bg-cyan-500/20 hover:text-white">

                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">

                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                    </svg>

                    Import Excel
                </a>

                <!-- Export -->
                <a href="{{ route('admin.employees.export') }}"
                    class="inline-flex items-center gap-2 rounded-2xl border border-emerald-500/20 bg-emerald-500/10 px-5 py-3 text-sm font-semibold text-emerald-300 shadow-lg shadow-emerald-500/10 transition-all duration-200 hover:-translate-y-0.5 hover:bg-emerald-500/20 hover:text-white">

                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">

                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 10v6m0 0l-3-3m3 3l3-3M4 20h16" />
                    </svg>

                    Export Excel
                </a>

                <!-- Tambah -->
                <a href="{{ route('admin.employees.create') }}"
                    class="inline-flex items-center gap-2 rounded-2xl border border-violet-500/20 bg-violet-500/10 px-5 py-3 text-sm font-semibold text-violet-300 shadow-lg shadow-violet-500/10 transition-all duration-200 hover:-translate-y-0.5 hover:bg-violet-500/20 hover:text-white">

                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">

                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>

                    Tambah Karyawan
                </a>

                <form action="{{ route('admin.employees.destroy-all') }}" method="POST" data-confirm
                    data-confirm-title="Hapus semua karyawan?"
                    data-confirm-text="Semua karyawan, akun login karyawan, payroll terkait, PDF slip gaji, dan antrean payroll akan dihapus permanen."
                    data-confirm-button="Ya, hapus semua" data-confirm-cancel="Batal">
                    @csrf
                    @method('DELETE')

                    <button type="submit"
                        class="inline-flex items-center gap-2 rounded-2xl border border-red-500/20 bg-red-500/10 px-5 py-3 text-sm font-semibold text-red-300 shadow-lg shadow-red-500/10 transition-all duration-200 hover:-translate-y-0.5 hover:bg-red-500/20 hover:text-white">

                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">

                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M9 7h6m-7 0h8l-1-2H10L9 7z" />
                        </svg>

                        Hapus Semua
                    </button>
                </form>

            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="overflow-hidden rounded-3xl border border-white/10 bg-slate-900/80 shadow-2xl backdrop-blur-xl">
                <div class="p-6">
                    <!-- Search -->
                    <form method="GET" action="{{ route('admin.employees.index') }}" class="mb-6">
                        <div class="flex flex-col gap-3 sm:flex-row">

                            <div class="relative flex-1">
                                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-slate-400"
                                        fill="none" viewBox="0 0 24 24" stroke="currentColor">

                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M21 21l-4.35-4.35m1.85-5.15a7 7 0 11-14 0 7 7 0 0114 0z" />
                                    </svg>
                                </div>

                                <input type="text" name="search" value="{{ request('search') }}"
                                    placeholder="Cari nama, NIP, email, departemen, jabatan..."
                                    class="w-full rounded-2xl border border-white/10 bg-slate-800/80 py-3 pl-12 pr-4 text-sm text-white placeholder:text-slate-400 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/30">
                            </div>

                            <div class="flex gap-2">
                                <button type="submit"
                                    class="inline-flex items-center justify-center rounded-2xl border border-indigo-500/20 bg-indigo-500/10 px-5 py-3 text-sm font-semibold text-indigo-300 transition hover:bg-indigo-500/20 hover:text-white">

                                    Cari
                                </button>

                                @if (request('search'))
                                    <a href="{{ route('admin.employees.index') }}"
                                        class="inline-flex items-center justify-center rounded-2xl border border-slate-600/20 bg-slate-700/40 px-5 py-3 text-sm font-semibold text-slate-300 transition hover:bg-slate-700 hover:text-white">

                                        Reset
                                    </a>
                                @endif
                            </div>
                        </div>
                    </form>

                    <form id="bulk-delete-form" action="{{ route('admin.employees.bulk-delete') }}" method="POST"
                        class="mb-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                        @csrf
                        @method('DELETE')

                        <p class="text-sm text-slate-400">
                            <span id="selected-count">0</span> karyawan dipilih
                        </p>

                        <button type="submit" id="bulk-delete-button" disabled
                            class="inline-flex items-center justify-center rounded-xl border border-red-500/20 bg-red-500/10 px-4 py-2 text-sm font-semibold text-red-300 opacity-50 transition hover:bg-red-500/20 hover:text-white disabled:cursor-not-allowed">
                            Hapus Terpilih
                        </button>
                    </form>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="border-b border-white/5 bg-white/[0.03]">
                                <tr>
                                    <th class="px-4 py-3">
                                        <input type="checkbox" id="select-all-employees"
                                            class="h-5 w-5 rounded-lg border-white/10 bg-slate-800 text-indigo-500 focus:ring-indigo-500">
                                    </th>
                                    {{-- <th
                                        class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">
                                        NIP</th> --}}
                                    <th
                                        class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">
                                        Nama</th>
                                    <th
                                        class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">
                                        Dept / Jabatan</th>
                                    <th
                                        class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">
                                        Status</th>
                                    <th
                                        class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wide text-gray-500">
                                        Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-white/5 bg-slate-950/30">

                                @if ($employees->count() === 0)
                                    <tr>
                                        <td colspan="5" class="px-4 py-10 text-center text-sm text-slate-400">
                                            Data karyawan tidak ditemukan.
                                        </td>
                                    </tr>
                                @endif

                                @foreach ($employees as $employee)
                                    <tr class="group transition duration-200 hover:bg-white/[0.03]">

                                        <td class="px-4 py-4">
                                            <input type="checkbox" name="employee_ids[]" value="{{ $employee->id }}"
                                                form="bulk-delete-form"
                                                class="employee-checkbox h-5 w-5 rounded-lg border-white/10 bg-slate-800 text-indigo-500 focus:ring-indigo-500">
                                        </td>

                                        <!-- NIP -->
                                        {{-- <td class="px-4 py-4">
                                            <div class="text-sm font-medium text-slate-300">
                                                {{ $employee->nip }}
                                            </div>
                                        </td> --}}

                                        <!-- Nama -->
                                        <td class="px-4 py-4">
                                            <div class="flex items-center gap-3">

                                                <!-- Avatar -->
                                                <div
                                                    class="flex h-11 w-11 items-center justify-center rounded-2xl bg-indigo-500/10 text-sm font-semibold text-indigo-300 ring-1 ring-indigo-500/20">

                                                    {{ strtoupper(substr($employee->name, 0, 1)) }}
                                                </div>

                                                <div>
                                                    <p class="font-semibold text-white">
                                                        {{ $employee->name }}
                                                    </p>

                                                    <p class="text-xs text-slate-400">
                                                        Employee
                                                    </p>
                                                </div>
                                            </div>
                                        </td>

                                        <!-- Dept -->
                                        <td class="px-4 py-4">
                                            <div>
                                                <p class="font-medium text-slate-200">
                                                    {{ $employee->department?->name }}
                                                </p>

                                                <p class="text-xs text-slate-400">
                                                    {{ $employee->position?->name }}
                                                </p>
                                            </div>
                                        </td>

                                        <!-- Status -->
                                        <td class="px-4 py-4">

                                            @if ($employee->status === 'active')
                                                <span
                                                    class="inline-flex items-center gap-2 rounded-full border border-emerald-500/20 bg-emerald-500/10 px-3 py-1 text-xs font-semibold text-emerald-300">

                                                    <span class="h-2 w-2 rounded-full bg-emerald-400"></span>

                                                    Active
                                                </span>
                                            @else
                                                <span
                                                    class="inline-flex items-center gap-2 rounded-full border border-red-500/20 bg-red-500/10 px-3 py-1 text-xs font-semibold text-red-300">

                                                    <span class="h-2 w-2 rounded-full bg-red-400"></span>

                                                    Inactive
                                                </span>
                                            @endif

                                        </td>

                                        <!-- Action -->
                                        <td class="px-4 py-4">

                                            <div class="flex items-center justify-end gap-2">

                                                <!-- Detail -->
                                                <a href="{{ route('admin.employees.show', $employee) }}"
                                                    class="inline-flex items-center gap-2 rounded-xl border border-cyan-500/20 bg-cyan-500/10 px-3 py-2 text-xs font-semibold text-cyan-300 transition hover:bg-cyan-500/20 hover:text-white">

                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4"
                                                        fill="none" viewBox="0 0 24 24" stroke="currentColor">

                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />

                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                    </svg>

                                                    Detail
                                                </a>

                                                <!-- Edit -->
                                                <a href="{{ route('admin.employees.edit', $employee) }}"
                                                    class="inline-flex items-center gap-2 rounded-xl border border-indigo-500/20 bg-indigo-500/10 px-3 py-2 text-xs font-semibold text-indigo-300 transition hover:bg-indigo-500/20 hover:text-white">

                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4"
                                                        fill="none" viewBox="0 0 24 24" stroke="currentColor">

                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M11 5h2m-1-1v2m-6 9l8-8 4 4-8 8H6v-4z" />
                                                    </svg>

                                                    Edit
                                                </a>

                                                <!-- Delete -->
                                                <form action="{{ route('admin.employees.destroy', $employee) }}"
                                                    method="POST" class="inline-block" data-confirm
                                                    data-confirm-title="Hapus karyawan?"
                                                    data-confirm-text="Data karyawan dan akun login terkait akan dihapus."
                                                    data-confirm-button="Ya, hapus">

                                                    @csrf
                                                    @method('DELETE')

                                                    <button type="submit"
                                                        class="inline-flex items-center gap-2 rounded-xl border border-red-500/20 bg-red-500/10 px-3 py-2 text-xs font-semibold text-red-300 transition hover:bg-red-500/20 hover:text-white">

                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4"
                                                            fill="none" viewBox="0 0 24 24" stroke="currentColor">

                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M9 7h6m-7 0h8l-1-2H10L9 7z" />
                                                        </svg>

                                                        Hapus
                                                    </button>
                                                </form>

                                            </div>

                                        </td>
                                    </tr>
                                @endforeach

                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">{{ $employees->links() }}</div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const selectAll = document.getElementById('select-all-employees');
            const checkboxes = [...document.querySelectorAll('.employee-checkbox')];
            const selectedCount = document.getElementById('selected-count');
            const bulkDeleteButton = document.getElementById('bulk-delete-button');
            const bulkDeleteForm = document.getElementById('bulk-delete-form');

            const refreshBulkState = () => {
                const checkedCount = checkboxes.filter((checkbox) => checkbox.checked).length;

                selectedCount.textContent = checkedCount;
                bulkDeleteButton.disabled = checkedCount === 0;
                bulkDeleteButton.classList.toggle('opacity-50', checkedCount === 0);

                selectAll.checked = checkedCount > 0 && checkedCount === checkboxes.length;
                selectAll.indeterminate = checkedCount > 0 && checkedCount < checkboxes.length;
            };

            selectAll.addEventListener('change', () => {
                checkboxes.forEach((checkbox) => {
                    checkbox.checked = selectAll.checked;
                });

                refreshBulkState();
            });

            checkboxes.forEach((checkbox) => checkbox.addEventListener('change', refreshBulkState));

            bulkDeleteForm.addEventListener('submit', async (event) => {
                const checkedCount = checkboxes.filter((checkbox) => checkbox.checked).length;

                if (checkedCount === 0) {
                    event.preventDefault();
                    window.AppAlerts.warning('Silakan pilih minimal satu karyawan terlebih dahulu.');
                    return;
                }

                event.preventDefault();

                const result = await window.AppAlerts.confirm({
                    title: 'Hapus karyawan terpilih?',
                    text: `${checkedCount} karyawan dan akun login terkait akan dihapus.`,
                    confirmButtonText: 'Ya, hapus',
                });

                if (result.isConfirmed) {
                    bulkDeleteForm.submit();
                }
            });

            refreshBulkState();
        });
    </script>
</x-app-layout>
