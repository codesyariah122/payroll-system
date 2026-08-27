@php
    $company = auth()->user()?->company;
    $label = $company?->name ?? config('app.name', 'Payroll');
    $initials = collect(explode(' ', $label))->filter()->take(2)->map(fn($word) => mb_substr($word, 0, 1))->implode('');
@endphp

<div {{ $attributes->merge(['class' => 'flex items-center']) }}>
    <div
        class="flex h-10 min-w-10 items-center justify-center rounded-2xl border border-indigo-400/20 bg-indigo-500/10 px-3 text-sm font-bold uppercase tracking-widest text-indigo-200">
        {{ $initials ?: 'PS' }}
    </div>
</div>
