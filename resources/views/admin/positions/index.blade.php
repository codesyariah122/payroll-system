<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Master Jabatan</h2>
                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Kelola posisi dan role internal perusahaan.</p>
            </div>
            <a href="{{ route('admin.positions.create') }}"
                class="inline-flex items-center rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700">Tambah
                Jabatan</a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="overflow-hidden rounded-3xl border border-white/10 bg-slate-900/80 shadow-2xl backdrop-blur-xl">

                <div class="p-6">

                    <div class="overflow-x-auto">

                        <table class="min-w-full">

                            <thead class="border-b border-white/5 bg-white/[0.03]">

                                <tr>

                                    <th
                                        class="px-4 py-4 text-left text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">
                                        Jabatan
                                    </th>

                                    <th
                                        class="px-4 py-4 text-right text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">
                                        Actions
                                    </th>

                                </tr>

                            </thead>

                            <tbody class="divide-y divide-white/5 bg-slate-950/30">

                                @foreach ($positions as $position)
                                    <tr class="group transition duration-200 hover:bg-white/[0.03]">

                                        <!-- Position -->
                                        <td class="px-4 py-4">

                                            <div class="flex items-center gap-3">

                                                <!-- Icon -->
                                                <div
                                                    class="flex h-11 w-11 items-center justify-center rounded-2xl bg-violet-500/10 text-violet-300 ring-1 ring-violet-500/20">

                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5"
                                                        fill="none" viewBox="0 0 24 24" stroke="currentColor">

                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M12 8c1.657 0 3-1.343 3-3S13.657 2 12 2 9 3.343 9 5s1.343 3 3 3zM6 20v-2a4 4 0 014-4h4a4 4 0 014 4v2" />
                                                    </svg>

                                                </div>

                                                <!-- Info -->
                                                <div>

                                                    <p class="font-semibold text-white">
                                                        {{ $position->name }}
                                                    </p>

                                                    <p class="text-xs text-slate-400">
                                                        Position / Internal Role
                                                    </p>

                                                </div>

                                            </div>

                                        </td>

                                        <!-- Actions -->
                                        <td class="px-4 py-4">

                                            <div class="flex items-center justify-end gap-2">

                                                <!-- Detail -->
                                                <a href="{{ route('admin.positions.show', $position) }}"
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
                                                <a href="{{ route('admin.positions.edit', $position) }}"
                                                    class="inline-flex items-center gap-2 rounded-xl border border-violet-500/20 bg-violet-500/10 px-3 py-2 text-xs font-semibold text-violet-300 transition hover:bg-violet-500/20 hover:text-white">

                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4"
                                                        fill="none" viewBox="0 0 24 24" stroke="currentColor">

                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M11 5h2m-1-1v2m-6 9l8-8 4 4-8 8H6v-4z" />
                                                    </svg>

                                                    Edit
                                                </a>

                                                <!-- Delete -->
                                                <form action="{{ route('admin.positions.destroy', $position) }}"
                                                    method="POST" class="inline-block" data-confirm
                                                    data-confirm-title="Hapus jabatan?"
                                                    data-confirm-text="Jabatan ini akan dihapus dari data master."
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

                    <div class="mt-6">
                        {{ $positions->links() }}
                    </div>

                </div>

            </div>
        </div>
    </div>
</x-app-layout>
