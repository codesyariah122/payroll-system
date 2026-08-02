<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Tambah Template Slip</h2>
                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Buat template HTML untuk generate PDF atau upload PDF sebagai referensi.</p>
            </div>
            <a href="{{ route('admin.payroll-templates.index') }}"
                class="inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200">Kembali</a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="mx-auto max-w-6xl sm:px-6 lg:px-8">
            <div class="rounded-3xl border border-white/10 bg-slate-900/80 p-6 shadow-2xl backdrop-blur-xl">
                @include('admin.payroll-templates.form', [
                    'action' => route('admin.payroll-templates.store'),
                    'method' => 'POST',
                    'template' => null,
                    'sampleHtml' => $sampleHtml,
                ])
            </div>
        </div>
    </div>
</x-app-layout>
