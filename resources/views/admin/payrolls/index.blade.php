<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">

            <div>
                <h2 class="text-2xl font-bold tracking-tight text-white">
                    Manajemen Payroll
                </h2>

                <p class="mt-2 max-w-2xl text-sm leading-relaxed text-slate-400">
                    Kelola payroll karyawan, import data Excel, generate slip gaji PDF,
                    dan kirim email massal secara modern dan efisien.
                </p>
            </div>

            <div class="flex shrink-0 flex-wrap items-center gap-2">

                <a href="{{ route('admin.payrolls.export') }}"
                    class="inline-flex items-center gap-2 rounded-2xl border border-emerald-500/20 bg-emerald-500/10 px-5 py-3 text-sm font-semibold text-emerald-300 shadow-lg shadow-emerald-500/10 transition-all duration-200 hover:-translate-y-0.5 hover:bg-emerald-500/20 hover:text-white">

                    Export Excel

                </a>

                <a href="{{ route('admin.payrolls.create') }}"
                    class="inline-flex items-center gap-2 rounded-2xl border border-violet-500/20 bg-violet-500/10 px-5 py-3 text-sm font-semibold text-violet-300 shadow-lg shadow-violet-500/10 transition-all duration-200 hover:-translate-y-0.5 hover:bg-violet-500/20 hover:text-white">

                    Tambah Payroll

                </a>

                <a href="{{ route('admin.payrolls.import') }}"
                    class="inline-flex items-center gap-2 rounded-2xl border border-cyan-500/20 bg-cyan-500/10 px-5 py-3 text-sm font-semibold text-cyan-300 shadow-lg shadow-cyan-500/10 transition-all duration-200 hover:-translate-y-0.5 hover:bg-cyan-500/20 hover:text-white">

                    Import Excel

                </a>

            </div>

        </div>
    </x-slot>

    <div class="py-6">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">

            <div
                class="max-w-full overflow-hidden rounded-3xl border border-white/10 bg-slate-900/80 shadow-2xl backdrop-blur-xl">

                <div class="p-6">

                    {{-- TOP ACTIONS --}}
                    <div class="mb-6 flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">

                        <div>
                            <h3 class="text-lg font-semibold text-white">
                                Data Payroll
                            </h3>

                            <p class="mt-1 text-sm text-slate-400">
                                Kelola slip gaji, email massal, dan payroll karyawan.
                            </p>
                        </div>

                        <div class="flex flex-col gap-3 lg:flex-row lg:items-center">

                            {{-- SEARCH FORM --}}
                            <form method="GET" action="{{ route('admin.payrolls.index') }}"
                                class="flex items-center gap-2">

                                <div class="relative">

                                    <input type="text" name="search" value="{{ request('search') }}"
                                        placeholder="Cari nama, NIP, periode, status email..."
                                        class="w-full lg:w-80 rounded-2xl border border-white/10 bg-slate-950/50 px-4 py-3 pl-11 text-sm text-white placeholder:text-slate-500 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20">

                                    <svg xmlns="http://www.w3.org/2000/svg"
                                        class="absolute left-4 top-1/2 h-5 w-5 -translate-y-1/2 text-slate-500"
                                        fill="none" viewBox="0 0 24 24" stroke="currentColor">

                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M21 21l-4.35-4.35m1.85-5.15a7 7 0 11-14 0 7 7 0 0114 0z" />

                                    </svg>

                                </div>

                                <button type="submit"
                                    class="rounded-2xl border border-indigo-500/20 bg-indigo-500/10 px-4 py-3 text-sm font-semibold text-indigo-300 transition hover:bg-indigo-500/20 hover:text-white">

                                    Search

                                </button>

                            </form>

                        </div>

                    </div>

                    <div class="mb-6 flex flex-wrap items-center gap-3">
                        <form action="{{ route('admin.payrolls.destroy-all') }}" method="POST" data-confirm
                            data-confirm-title="Hapus semua payroll?"
                            data-confirm-text="Semua data payroll, PDF slip gaji, dan antrean email payroll akan dihapus permanen."
                            data-confirm-button="Ya, hapus semua" data-confirm-cancel="Batal">

                            @csrf
                            @method('DELETE')

                            <button type="submit"
                                class="inline-flex items-center gap-2 rounded-2xl border border-rose-500/20 bg-rose-500/10 px-4 py-3 text-sm font-semibold text-rose-300 transition hover:bg-rose-500/20 hover:text-white">

                                Hapus Semua Payroll

                            </button>

                        </form>
                    </div>

                    {{-- FORM BULK EMAIL --}}
                    <form action="{{ route('admin.payrolls.bulk-email') }}" method="POST">

                        @csrf

                        <div class="mb-6 flex flex-wrap items-center gap-3">

                            <button type="submit" id="send-selected"
                                class="inline-flex items-center gap-2 rounded-2xl border border-emerald-500/20 bg-emerald-500/10 px-4 py-3 text-sm font-semibold text-emerald-300 transition hover:bg-emerald-500/20 hover:text-white">

                                Kirim Terpilih

                            </button>

                            <button type="submit" name="send_all" value="1" id="send-all"
                                class="inline-flex items-center gap-2 rounded-2xl border border-cyan-500/20 bg-cyan-500/10 px-4 py-3 text-sm font-semibold text-cyan-300 transition hover:bg-cyan-500/20 hover:text-white">

                                Kirim Semua

                            </button>

                        </div>

                        {{-- QUEUE ALERT --}}
                        <div id="queue-alert"
                            class="mb-6 hidden rounded-2xl border border-amber-500/20 bg-amber-500/10 p-4 text-amber-200">

                            <div class="flex items-center gap-3 min-w-0">

                                <svg class="h-5 w-5 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none"
                                    viewBox="0 0 24 24">

                                    <circle class="opacity-25" cx="12" cy="12" r="10"
                                        stroke="currentColor" stroke-width="4">
                                    </circle>

                                    <path class="opacity-75" fill="currentColor"
                                        d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z">
                                    </path>

                                </svg>

                                <div>

                                    <p class="font-semibold">
                                        Payroll sedang diproses...
                                    </p>

                                    <p class="text-sm text-amber-300/80">
                                        Generating PDF & queue email sedang berjalan.
                                    </p>

                                </div>

                            </div>

                        </div>

                        {{-- TABLE --}}
                        <div class="w-full overflow-x-auto">

                            <table class="min-w-[1100px]">

                                <thead class="border-b border-white/5 bg-white/[0.03]">

                                    <tr>

                                        <th
                                            class="w-[70px] px-4 py-4 text-left text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">

                                            <input type="checkbox" id="select-all"
                                                class="h-5 w-5 rounded-lg border-white/10 bg-slate-800 text-indigo-500 focus:ring-indigo-500" />

                                        </th>

                                        <th
                                            class="min-w-[280px] px-4 py-4 text-left text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">

                                            Karyawan

                                        </th>

                                        <th
                                            class="min-w-[180px] px-4 py-4 text-left text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">

                                            Periode

                                        </th>

                                        <th
                                            class="min-w-[180px] px-4 py-4 text-left text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">

                                            Total Gaji

                                        </th>

                                        <th
                                            class="min-w-[180px] px-4 py-4 text-left text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">

                                            Status Email

                                        </th>

                                        <th
                                            class="min-w-[320px] px-4 py-4 text-right text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">

                                            Actions

                                        </th>

                                    </tr>

                                </thead>

                                <tbody class="divide-y divide-white/5 bg-slate-950/30">

                                    @foreach ($payrolls as $payroll)
                                        <tr class="group transition duration-200 hover:bg-white/[0.03]">

                                            {{-- CHECKBOX --}}
                                            <td class="px-4 py-4">

                                                <input type="checkbox" name="payroll_ids[]" value="{{ $payroll->id }}"
                                                    class="payroll-checkbox h-5 w-5 rounded-lg border-white/10 bg-slate-800 text-indigo-500 focus:ring-indigo-500" />

                                            </td>

                                            {{-- EMPLOYEE --}}
                                            <td class="min-w-[280px] px-4 py-4">

                                                <div class="flex items-center gap-3 min-w-0">

                                                    <div
                                                        class="flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl bg-indigo-500/10 text-indigo-300 ring-1 ring-indigo-500/20">

                                                        <span class="text-sm font-semibold">
                                                            {{ strtoupper(substr($payroll->employee->name, 0, 1)) }}
                                                        </span>

                                                    </div>

                                                    <div class="min-w-0">

                                                        <p class="truncate font-semibold text-white">
                                                            {{ $payroll->employee->name }}
                                                        </p>

                                                        <small class="block truncate text-xs text-slate-400">
                                                            {{ $payroll->employee->email ?? '-' }}
                                                        </small>

                                                    </div>

                                                </div>

                                            </td>

                                            {{-- PERIOD --}}
                                            <td class="px-4 py-4">

                                                <span
                                                    class="inline-flex whitespace-nowrap rounded-full border border-violet-500/20 bg-violet-500/10 px-3 py-1 text-xs font-semibold text-violet-300">

                                                    {{ $payroll->period }}

                                                </span>

                                            </td>

                                            {{-- SALARY --}}
                                            <td class="px-4 py-4">

                                                <p class="whitespace-nowrap font-semibold text-emerald-300">
                                                    Rp {{ number_format($payroll->total_salary, 0, ',', '.') }}
                                                </p>

                                            </td>

                                            {{-- STATUS --}}
                                            <td class="px-4 py-4">

                                                @if ($payroll->email_status === 'sent')
                                                    <span
                                                        class="inline-flex items-center whitespace-nowrap rounded-full border border-emerald-500/20 bg-emerald-500/10 px-3 py-1 text-xs font-semibold text-emerald-300">

                                                        Terkirim

                                                    </span>
                                                @elseif ($payroll->email_status === 'queued')
                                                    <span
                                                        class="inline-flex items-center whitespace-nowrap rounded-full border border-amber-500/20 bg-amber-500/10 px-3 py-1 text-xs font-semibold text-amber-300">

                                                        Queue

                                                    </span>
                                                @elseif ($payroll->email_status === 'sending')
                                                    <span
                                                        class="inline-flex items-center whitespace-nowrap rounded-full border border-cyan-500/20 bg-cyan-500/10 px-3 py-1 text-xs font-semibold text-cyan-300">

                                                        Mengirim

                                                    </span>
                                                @elseif ($payroll->email_status === 'failed')
                                                    <span
                                                        class="inline-flex items-center whitespace-nowrap rounded-full border border-rose-500/20 bg-rose-500/10 px-3 py-1 text-xs font-semibold text-rose-300">

                                                        Gagal

                                                    </span>
                                                @elseif ($payroll->email_status === 'skipped')
                                                    <span
                                                        class="inline-flex items-center whitespace-nowrap rounded-full border border-slate-500/20 bg-slate-500/10 px-3 py-1 text-xs font-semibold text-slate-300">

                                                        Skip Rp 0

                                                    </span>
                                                @else
                                                    <span
                                                        class="inline-flex items-center whitespace-nowrap rounded-full border border-slate-500/20 bg-slate-500/10 px-3 py-1 text-xs font-semibold text-slate-300">

                                                        Pending

                                                    </span>
                                                @endif

                                            </td>

                                            {{-- ACTIONS --}}
                                            <td class="px-4 py-4 whitespace-nowrap">

                                                <div class="flex flex-wrap items-center justify-end gap-2">

                                                    {{-- DETAIL --}}
                                                    <a href="{{ route('admin.payrolls.show', $payroll) }}"
                                                        class="inline-flex items-center gap-2 rounded-xl border border-cyan-500/20 bg-cyan-500/10 px-3 py-2 text-xs font-semibold text-cyan-300 transition hover:bg-cyan-500/20 hover:text-white">

                                                        Detail

                                                    </a>

                                                    {{-- EDIT --}}
                                                    <a href="{{ route('admin.payrolls.edit', $payroll) }}"
                                                        class="inline-flex items-center gap-2 rounded-xl border border-indigo-500/20 bg-indigo-500/10 px-3 py-2 text-xs font-semibold text-indigo-300 transition hover:bg-indigo-500/20 hover:text-white">

                                                        Edit

                                                    </a>

                                                    {{-- PREVIEW PDF --}}
                                                    <a href="{{ route('admin.payrolls.preview', $payroll) }}"
                                                        target="_blank"
                                                        class="inline-flex items-center gap-2 rounded-xl border border-amber-500/20 bg-amber-500/10 px-3 py-2 text-xs font-semibold text-amber-300 transition hover:bg-amber-500/20 hover:text-white">

                                                        PDF

                                                    </a>

                                                    {{-- DOWNLOAD PDF --}}
                                                    <a href="{{ route('admin.payrolls.download', $payroll) }}"
                                                        class="inline-flex items-center gap-2 rounded-xl border border-emerald-500/20 bg-emerald-500/10 px-3 py-2 text-xs font-semibold text-emerald-300 transition hover:bg-emerald-500/20 hover:text-white">

                                                        Download

                                                    </a>

                                                </div>

                                            </td>

                                        </tr>
                                    @endforeach

                                </tbody>

                            </table>

                        </div>

                    </form>

                    {{-- PAGINATION --}}
                    <div class="mt-6 flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">

                        <div class="text-sm text-slate-500">
                            Total Payroll :
                            <span class="font-semibold text-slate-300">
                                {{ $payrolls->total() }}
                            </span>
                        </div>

                        <div>
                            {{ $payrolls->links() }}
                        </div>

                    </div>

                </div>

            </div>

        </div>
    </div>

    <script>
        const selectAll =
            document.getElementById('select-all');

        const checkboxes =
            document.querySelectorAll('.payroll-checkbox');

        selectAll.addEventListener('change', function() {

            checkboxes.forEach((checkbox) => {

                checkbox.checked = this.checked;
            });
        });

        checkboxes.forEach((checkbox) => {

            checkbox.addEventListener('change', function() {

                const total =
                    checkboxes.length;

                const checked =
                    document.querySelectorAll('.payroll-checkbox:checked').length;

                selectAll.checked =
                    total === checked;
            });
        });
    </script>

    <script>
        let wasProcessing = false;

        async function checkQueueStatus() {

            try {

                const response = await fetch(
                    '{{ route('admin.payrolls.queue-status') }}'
                );

                const data = await response.json();

                const alertBox =
                    document.getElementById('queue-alert');

                if (data.processing) {

                    wasProcessing = true;

                    alertBox.classList.remove('hidden');

                } else {

                    alertBox.classList.add('hidden');

                    // queue selesai → reload
                    if (wasProcessing) {

                        location.reload();
                    }
                }

            } catch (error) {

                console.error(error);
            }
        }

        checkQueueStatus();

        setInterval(checkQueueStatus, 3000);
    </script>
</x-app-layout>
