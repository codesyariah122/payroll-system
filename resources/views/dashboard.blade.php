<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="dashboard-panel">
                <div class="flex flex-col gap-6">
                    <div>
                        <h1 class="text-2xl font-semibold text-slate-900 dark:text-white">Selamat Datang di Dashboard
                        </h1>
                        <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">Tinjau ringkasan payroll dan akses
                            fitur utama melalui sidebar.</p>
                    </div>

                    <div class="dashboard-highlight">
                        <p class="text-slate-700 dark:text-slate-200">{{ __("You're logged in!") }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
