@php
    $isEdit = filled($template);
    $type = old('type', $template?->type ?? 'html');
    $htmlContent = old('html_content', $template?->html_content ?? $sampleHtml);
@endphp

<form action="{{ $action }}" method="POST" enctype="multipart/form-data" class="space-y-6">
    @csrf
    @if ($method !== 'POST')
        @method($method)
    @endif

    <div class="grid gap-6 lg:grid-cols-[1fr_360px]">
        <div class="space-y-5">
            <div>
                <label class="mb-2 block text-sm font-semibold text-slate-200">Nama Template</label>
                <input type="text" name="name" value="{{ old('name', $template?->name) }}"
                    class="w-full rounded-2xl border border-white/10 bg-slate-950/70 px-4 py-3 text-sm text-white placeholder:text-slate-500 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20"
                    placeholder="Contoh: Slip Gaji CRK Baru">
                @error('name')
                    <p class="mt-2 text-sm text-red-300">{{ $message }}</p>
                @enderror
            </div>

            @if (! $isEdit)
                <div>
                    <label class="mb-2 block text-sm font-semibold text-slate-200">Tipe Template</label>
                    <select name="type" id="template-type"
                        class="w-full rounded-2xl border border-white/10 bg-slate-950/70 px-4 py-3 text-sm text-white focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20">
                        <option value="html" @selected($type === 'html')>HTML - dipakai generate PDF</option>
                        <option value="pdf" @selected($type === 'pdf')>Upload PDF - referensi desain</option>
                    </select>
                </div>
            @else
                <input type="hidden" name="type" id="template-type" value="{{ $type }}">
            @endif

            <div id="html-template-panel">
                <label class="mb-2 block text-sm font-semibold text-slate-200">HTML Template</label>
                <textarea name="html_content" rows="24"
                    class="font-mono w-full rounded-2xl border border-white/10 bg-slate-950/70 px-4 py-3 text-xs leading-relaxed text-slate-100 placeholder:text-slate-500 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20">{{ $htmlContent }}</textarea>
                @error('html_content')
                    <p class="mt-2 text-sm text-red-300">{{ $message }}</p>
                @enderror
            </div>

            <div id="pdf-template-panel">
                <label class="mb-2 block text-sm font-semibold text-slate-200">Upload PDF Referensi</label>
                <input type="file" name="pdf_file" accept="application/pdf"
                    class="w-full rounded-2xl border border-white/10 bg-slate-950/70 px-4 py-3 text-sm text-slate-200 file:mr-4 file:rounded-xl file:border-0 file:bg-indigo-500/20 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-indigo-200">
                @if ($template?->pdf_path)
                    <p class="mt-2 text-sm text-slate-400">PDF referensi saat ini sudah tersimpan.</p>
                @endif
                @error('pdf_file')
                    <p class="mt-2 text-sm text-red-300">{{ $message }}</p>
                @enderror
            </div>

            <label id="active-panel" class="flex items-center gap-3 rounded-2xl border border-white/10 bg-slate-950/50 p-4 text-sm text-slate-200">
                <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $template?->is_active))
                    class="h-5 w-5 rounded border-white/10 bg-slate-800 text-indigo-500 focus:ring-indigo-500">
                Jadikan template aktif untuk generate slip payroll
            </label>
        </div>

        <aside class="space-y-4 rounded-3xl border border-white/10 bg-slate-950/60 p-5">
            <div>
                <p class="text-sm font-semibold uppercase tracking-[0.18em] text-indigo-300">Placeholder</p>
                <p class="mt-2 text-sm leading-relaxed text-slate-400">Gunakan placeholder ini di HTML template. Saat PDF dibuat, sistem akan menggantinya dengan data payroll.</p>
            </div>

            <div class="grid gap-2 text-xs text-slate-300">
                @foreach ([
                    '[[period]]',
                    '[[employee_name]]',
                    '[[employee_email]]',
                    '[[employee_nip]]',
                    '[[position_name]]',
                    '[[department_name]]',
                    '[[target_work_days]]',
                    '[[work_days]]',
                    '[[overtime_hours]]',
                    '[[basic_salary]]',
                    '[[position_allowance]]',
                    '[[attendance_allowance]]',
                    '[[overtime_pay]]',
                    '[[total_income]]',
                    '[[total_deduction]]',
                    '[[take_home_pay]]',
                ] as $placeholder)
                    <code class="rounded-lg bg-white/5 px-3 py-2">{{ $placeholder }}</code>
                @endforeach
            </div>

            <div class="rounded-2xl border border-amber-500/20 bg-amber-500/10 p-4 text-xs leading-relaxed text-amber-100">
                Template default bawaan tetap aman. Kalau tidak ada template HTML aktif, PDF payroll otomatis memakai desain lama.
            </div>
        </aside>
    </div>

    <div class="flex justify-end gap-3">
        <a href="{{ route('admin.payroll-templates.index') }}"
            class="rounded-2xl border border-slate-600/20 bg-slate-700/40 px-5 py-3 text-sm font-semibold text-slate-300 transition hover:bg-slate-700 hover:text-white">Batal</a>
        <button type="submit"
            class="rounded-2xl border border-indigo-500/20 bg-indigo-500/20 px-5 py-3 text-sm font-semibold text-indigo-100 transition hover:bg-indigo-500/30 hover:text-white">
            Simpan Template
        </button>
    </div>
</form>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const typeSelect = document.getElementById('template-type');
        const htmlPanel = document.getElementById('html-template-panel');
        const pdfPanel = document.getElementById('pdf-template-panel');
        const activePanel = document.getElementById('active-panel');

        const refreshPanels = () => {
            const type = typeSelect.value;

            htmlPanel.classList.toggle('hidden', type !== 'html');
            pdfPanel.classList.toggle('hidden', type !== 'pdf');
            activePanel.classList.toggle('hidden', type !== 'html');
        };

        typeSelect.addEventListener('change', refreshPanels);
        refreshPanels();
    });
</script>
