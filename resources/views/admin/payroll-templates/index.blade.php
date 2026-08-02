<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Template Slip Payroll</h2>
                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Kelola desain slip gaji PDF. Default tetap memakai template payroll saat ini.</p>
            </div>

            <a href="{{ route('admin.payroll-templates.create') }}"
                class="inline-flex items-center rounded-2xl border border-violet-500/20 bg-violet-500/10 px-5 py-3 text-sm font-semibold text-violet-300 shadow-lg shadow-violet-500/10 transition hover:bg-violet-500/20 hover:text-white">
                Tambah Template
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="mb-6 rounded-3xl border border-emerald-500/20 bg-emerald-500/10 p-6 text-emerald-100">
                <p class="text-sm font-semibold uppercase tracking-[0.18em] text-emerald-300">Template Aktif</p>
                <h3 class="mt-2 text-xl font-bold text-white">
                    {{ $activeTemplate?->name ?? 'Default Payroll Saat Ini' }}
                </h3>
                <p class="mt-2 text-sm text-emerald-100/80">
                    {{ $activeTemplate ? 'PDF payroll akan memakai template HTML aktif ini.' : 'PDF payroll memakai template bawaan yang saat ini sudah digunakan.' }}
                </p>

                @if ($activeTemplate)
                    <form action="{{ route('admin.payroll-templates.deactivate') }}" method="POST" class="mt-4">
                        @csrf
                        <button type="submit"
                            class="inline-flex rounded-xl border border-white/10 bg-white/10 px-4 py-2 text-sm font-semibold text-white transition hover:bg-white/20">
                            Pakai Default Bawaan
                        </button>
                    </form>
                @endif
            </div>

            <div class="overflow-hidden rounded-3xl border border-white/10 bg-slate-900/80 shadow-2xl backdrop-blur-xl">
                <div class="p-6">
                    <div class="mb-5 rounded-2xl border border-amber-500/20 bg-amber-500/10 p-4 text-sm text-amber-100">
                        Upload PDF disimpan sebagai referensi desain. Untuk dipakai otomatis saat generate slip, buat template tipe HTML dengan placeholder.
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full">
                            <thead class="border-b border-white/5 bg-white/[0.03]">
                                <tr>
                                    <th class="px-4 py-4 text-left text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Nama</th>
                                    <th class="px-4 py-4 text-left text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Tipe</th>
                                    <th class="px-4 py-4 text-left text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Status</th>
                                    <th class="px-4 py-4 text-right text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Aksi</th>
                                </tr>
                            </thead>

                            <tbody class="divide-y divide-white/5 bg-slate-950/30">
                                <tr class="transition hover:bg-white/[0.03]">
                                    <td class="px-4 py-4">
                                        <p class="font-semibold text-white">Default Payroll Saat Ini</p>
                                        <p class="text-xs text-slate-400">Template bawaan sistem dari desain slip payroll saat ini.</p>
                                    </td>
                                    <td class="px-4 py-4">
                                        <span class="inline-flex rounded-full border border-emerald-500/20 bg-emerald-500/10 px-3 py-1 text-xs font-semibold text-emerald-300">
                                            Default Bawaan
                                        </span>
                                    </td>
                                    <td class="px-4 py-4">
                                        @if (! $activeTemplate)
                                            <span class="inline-flex rounded-full border border-emerald-500/20 bg-emerald-500/10 px-3 py-1 text-xs font-semibold text-emerald-300">Aktif</span>
                                        @else
                                            <span class="inline-flex rounded-full border border-slate-500/20 bg-slate-500/10 px-3 py-1 text-xs font-semibold text-slate-300">Standby</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-4">
                                        <div class="flex flex-wrap items-center justify-end gap-2">
                                            <a href="{{ route('admin.payroll-templates.default.preview') }}" target="_blank"
                                                class="rounded-xl border border-cyan-500/20 bg-cyan-500/10 px-3 py-2 text-xs font-semibold text-cyan-300 transition hover:bg-cyan-500/20 hover:text-white">
                                                Preview
                                            </a>

                                            @if ($activeTemplate)
                                                <form action="{{ route('admin.payroll-templates.deactivate') }}" method="POST">
                                                    @csrf
                                                    <button type="submit"
                                                        class="rounded-xl border border-emerald-500/20 bg-emerald-500/10 px-3 py-2 text-xs font-semibold text-emerald-300 transition hover:bg-emerald-500/20 hover:text-white">
                                                        Aktifkan Default
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>

                                @forelse ($templates as $template)
                                    <tr class="transition hover:bg-white/[0.03]">
                                        <td class="px-4 py-4">
                                            <p class="font-semibold text-white">{{ $template->name }}</p>
                                            <p class="text-xs text-slate-400">Dibuat {{ $template->created_at->format('d M Y H:i') }}</p>
                                        </td>
                                        <td class="px-4 py-4">
                                            <span class="inline-flex rounded-full border border-cyan-500/20 bg-cyan-500/10 px-3 py-1 text-xs font-semibold text-cyan-300">
                                                {{ $template->isHtml() ? 'HTML Generate' : 'PDF Referensi' }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-4">
                                            @if ($template->isHtml() && $template->is_active)
                                                <span class="inline-flex rounded-full border border-emerald-500/20 bg-emerald-500/10 px-3 py-1 text-xs font-semibold text-emerald-300">Aktif</span>
                                            @else
                                                <span class="inline-flex rounded-full border border-slate-500/20 bg-slate-500/10 px-3 py-1 text-xs font-semibold text-slate-300">Nonaktif</span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-4">
                                            <div class="flex flex-wrap items-center justify-end gap-2">
                                                @if ($template->isHtml() && ! $template->is_active)
                                                    <form action="{{ route('admin.payroll-templates.activate', $template) }}" method="POST">
                                                        @csrf
                                                        <button type="submit"
                                                            class="rounded-xl border border-emerald-500/20 bg-emerald-500/10 px-3 py-2 text-xs font-semibold text-emerald-300 transition hover:bg-emerald-500/20 hover:text-white">
                                                            Aktifkan
                                                        </button>
                                                    </form>
                                                @endif

                                                @if ($template->isPdfReference() && $template->pdf_path)
                                                    <a href="{{ route('admin.payroll-templates.download', $template) }}"
                                                        class="rounded-xl border border-cyan-500/20 bg-cyan-500/10 px-3 py-2 text-xs font-semibold text-cyan-300 transition hover:bg-cyan-500/20 hover:text-white">
                                                        Download
                                                    </a>
                                                @endif

                                                <a href="{{ route('admin.payroll-templates.edit', $template) }}"
                                                    class="rounded-xl border border-indigo-500/20 bg-indigo-500/10 px-3 py-2 text-xs font-semibold text-indigo-300 transition hover:bg-indigo-500/20 hover:text-white">
                                                    Edit
                                                </a>

                                                <form action="{{ route('admin.payroll-templates.destroy', $template) }}" method="POST" data-confirm
                                                    data-confirm-title="Hapus template?"
                                                    data-confirm-text="Template ini akan dihapus permanen."
                                                    data-confirm-button="Ya, hapus">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                        class="rounded-xl border border-red-500/20 bg-red-500/10 px-3 py-2 text-xs font-semibold text-red-300 transition hover:bg-red-500/20 hover:text-white">
                                                        Hapus
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-4 py-10 text-center text-sm text-slate-400">
                                            Belum ada template custom lain.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-5">{{ $templates->links() }}</div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
