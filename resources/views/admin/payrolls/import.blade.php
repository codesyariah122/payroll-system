<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Import Payroll Excel</h2>
    </x-slot>

    <div class="py-6">
        <div class="mx-auto max-w-4xl sm:px-6 lg:px-8">
            <div
                class="rounded-[2rem] border border-slate-200 bg-white/95 p-8 shadow-lg shadow-slate-200/20 dark:border-slate-800 dark:bg-slate-900/95 dark:shadow-none">
                <div class="grid gap-8 lg:grid-cols-[1fr_320px]">
                    <div class="space-y-4">
                        <div>
                            <p class="text-sm font-semibold uppercase tracking-[0.18em] text-indigo-600">Import Payroll
                            </p>
                            <h1 class="mt-3 text-2xl font-semibold text-slate-900 dark:text-white">Unggah file Excel
                                payroll Anda</h1>
                            <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">Gunakan format file Excel yang
                                sesuai agar data payroll dapat diproses dengan cepat dan aman.</p>
                        </div>

                        <div
                            class="rounded-3xl border border-slate-200 bg-slate-50 p-6 dark:border-slate-800 dark:bg-slate-950">
                            <h2 class="text-lg font-semibold text-slate-900 dark:text-white">Format file</h2>
                            <p class="mt-3 text-sm text-slate-500 dark:text-slate-400">Gunakan header sesuai template
                                slip gaji: No, Bulan, Nama, Email, Jabatan, Departemen, Target HK, HK, Jam Lembur, Jam
                                Lembur Khusus, sampai THP/Gaji Bersih.</p>
                            <p class="mt-4 text-sm text-slate-500 dark:text-slate-400">Sistem mencocokkan karyawan dari
                                kolom Email. Jika memakai format lama, kolom NIP masih didukung sebagai cadangan.</p>
                        </div>
                    </div>

                    <div
                        class="space-y-6 rounded-3xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                        <form id="importPayrollForm" action="{{ route('admin.payrolls.import.store') }}" method="POST"
                            enctype="multipart/form-data">
                            @csrf

                            <div class="space-y-6">
                                <div x-data="{ fileName: '' }">

                                    <label for="file-upload"
                                        class="group relative flex cursor-pointer flex-col items-center justify-center overflow-hidden rounded-[2rem] border border-dashed border-slate-300 bg-gradient-to-br from-white to-slate-50 px-6 py-10 text-center transition-all duration-300 hover:border-indigo-400 hover:shadow-2xl hover:shadow-indigo-500/10 dark:border-slate-700 dark:from-slate-900 dark:to-slate-950 dark:hover:border-indigo-500">

                                        <!-- Glow -->
                                        <div
                                            class="absolute inset-0 bg-gradient-to-br from-indigo-500/0 via-indigo-500/0 to-cyan-500/0 opacity-0 transition duration-500 group-hover:opacity-100">
                                        </div>

                                        <!-- Icon -->
                                        <div
                                            class="relative flex h-20 w-20 items-center justify-center rounded-[2rem] bg-indigo-500/10 text-indigo-400 ring-1 ring-indigo-500/20 transition duration-300 group-hover:scale-110 group-hover:bg-indigo-500/20">

                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor">

                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                                    d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                            </svg>

                                        </div>

                                        <!-- Text -->
                                        <div class="relative mt-6">

                                            <h3 class="text-lg font-semibold text-slate-900 dark:text-white">
                                                Drag & Drop File Excel
                                            </h3>

                                            <p class="mt-2 text-sm leading-relaxed text-slate-500 dark:text-slate-400">
                                                Upload file payroll format
                                                <span class="font-semibold text-indigo-400">.xlsx</span>
                                                atau
                                                <span class="font-semibold text-cyan-400">.xls</span>
                                            </p>

                                            <p class="mt-4 text-xs uppercase tracking-[0.24em] text-slate-400">
                                                atau klik untuk memilih file
                                            </p>

                                        </div>

                                        <!-- Filename -->
                                        <div x-show="fileName" x-transition
                                            class="relative mt-6 inline-flex items-center gap-2 rounded-2xl border border-emerald-500/20 bg-emerald-500/10 px-4 py-2 text-sm font-medium text-emerald-300">

                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor">

                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                            </svg>

                                            <span x-text="fileName"></span>

                                        </div>

                                        <!-- Hidden Input -->
                                        <input id="file-upload" type="file" name="file" accept=".xlsx,.xls"
                                            class="hidden" required @change="fileName = $event.target.files[0]?.name">

                                    </label>

                                    @error('file')
                                        <p class="mt-3 text-sm font-medium text-red-500">
                                            {{ $message }}
                                        </p>
                                    @enderror

                                </div>

                                <div class="grid gap-3">
                                    <button id="submitPayrollBtn" type="submit"
                                        class="group inline-flex items-center justify-center gap-2 rounded-[1.4rem] bg-gradient-to-r from-indigo-600 to-violet-600 px-5 py-3 text-sm font-semibold text-white shadow-2xl shadow-indigo-500/20 transition-all duration-300 hover:-translate-y-0.5 hover:from-indigo-500 hover:to-violet-500">Unggah
                                        dan Proses</button>
                                    <a href="{{ route('admin.payrolls.index') }}"
                                        class="inline-flex justify-center rounded-3xl border border-slate-300 bg-white px-5 py-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-100 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-200 dark:hover:bg-slate-900">Kembali
                                        ke Payroll</a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- LOADING OVERLAY -->
    <div id="loadingOverlay"
        class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm">

        <div class="w-full max-w-sm rounded-3xl bg-white p-8 text-center shadow-2xl dark:bg-slate-900">

            <div class="flex justify-center">
                <svg class="h-14 w-14 animate-spin text-indigo-600" xmlns="http://www.w3.org/2000/svg" fill="none"
                    viewBox="0 0 24 24">

                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                        stroke-width="4">
                    </circle>

                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z">
                    </path>
                </svg>
            </div>

            <h2 class="mt-6 text-xl font-bold text-slate-900 dark:text-white">
                Sedang Import Payroll
            </h2>

            <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">
                Mohon tunggu, sistem sedang memproses data payroll...
            </p>

        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {

            const form = document.getElementById('importPayrollForm');

            const submitBtn = document.getElementById('submitPayrollBtn');

            const loadingOverlay = document.getElementById('loadingOverlay');

            form.addEventListener('submit', function() {

                submitBtn.disabled = true;

                loadingOverlay.classList.remove('hidden');
            });
        });
    </script>
</x-app-layout>
