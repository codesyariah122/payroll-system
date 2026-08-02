<x-guest-layout>
    <div class="min-h-screen bg-[#050816] text-white">
        <div class="mx-auto grid min-h-screen w-full max-w-7xl gap-10 px-5 py-6 sm:px-8 lg:grid-cols-[1.05fr_0.95fr] lg:items-center lg:px-10">
            <section class="hidden lg:block">
                <a href="{{ url('/') }}" class="inline-flex items-center gap-3 text-white">
                    <span class="flex h-12 w-12 items-center justify-center rounded-2xl border border-white/10 bg-white/[0.06] text-lg font-black text-indigo-200 shadow-lg">
                        PS
                    </span>
                    <span>
                        <span class="block text-lg font-bold">Payroll System</span>
                        <span class="block text-sm text-slate-400">Platform HC Operation</span>
                    </span>
                </a>

                <div class="mt-16 max-w-xl">
                    <span class="inline-flex rounded-full border border-indigo-400/30 bg-indigo-500/10 px-4 py-2 text-xs font-bold uppercase tracking-[0.24em] text-indigo-200">
                        Workspace Perusahaan
                    </span>

                    <h1 class="mt-6 text-5xl font-black leading-tight tracking-tight">
                        Mulai kelola payroll dengan data perusahaan yang terpisah.
                    </h1>

                    <p class="mt-5 text-lg leading-8 text-slate-300">
                        Buat akun admin untuk company Anda. Setiap data karyawan, departemen, posisi, template slip, dan payroll akan berada di workspace perusahaan masing-masing.
                    </p>
                </div>

                <div class="mt-10 grid max-w-2xl grid-cols-2 gap-4">
                    <div class="rounded-3xl border border-white/10 bg-white/[0.055] p-5 shadow-2xl shadow-black/20">
                        <div class="text-sm font-bold uppercase tracking-[0.2em] text-cyan-200">Data Terpisah</div>
                        <p class="mt-3 text-sm leading-6 text-slate-300">Cocok untuk multi-company. Data admin baru tidak bercampur dengan company lain.</p>
                    </div>

                    <div class="rounded-3xl border border-white/10 bg-white/[0.055] p-5 shadow-2xl shadow-black/20">
                        <div class="text-sm font-bold uppercase tracking-[0.2em] text-emerald-200">Siap Import</div>
                        <p class="mt-3 text-sm leading-6 text-slate-300">Import karyawan dan payroll Excel dengan alur validasi untuk tim HC Operation.</p>
                    </div>

                    <div class="rounded-3xl border border-white/10 bg-white/[0.055] p-5 shadow-2xl shadow-black/20">
                        <div class="text-sm font-bold uppercase tracking-[0.2em] text-violet-200">Slip PDF</div>
                        <p class="mt-3 text-sm leading-6 text-slate-300">Generate slip gaji PDF, preview, lalu kirim email payroll melalui antrean.</p>
                    </div>

                    <div class="rounded-3xl border border-white/10 bg-white/[0.055] p-5 shadow-2xl shadow-black/20">
                        <div class="text-sm font-bold uppercase tracking-[0.2em] text-amber-200">Profil Company</div>
                        <p class="mt-3 text-sm leading-6 text-slate-300">Logo, deskripsi, kontak, dan alamat perusahaan bisa dilengkapi setelah login.</p>
                    </div>
                </div>
            </section>

            <section class="flex min-h-screen items-center py-8 lg:min-h-0">
                <div class="w-full">
                    <div class="mb-8 flex items-center justify-between lg:hidden">
                        <a href="{{ url('/') }}" class="inline-flex items-center gap-3 text-white">
                            <span class="flex h-11 w-11 items-center justify-center rounded-2xl border border-white/10 bg-white/[0.06] text-base font-black text-indigo-200">
                                PS
                            </span>
                            <span>
                                <span class="block text-base font-bold">Payroll System</span>
                                <span class="block text-xs text-slate-400">HC Operation</span>
                            </span>
                        </a>

                        <a href="{{ route('login') }}" class="rounded-2xl border border-white/10 px-4 py-2 text-sm font-semibold text-slate-200 transition hover:border-indigo-300/50 hover:text-white">
                            Login
                        </a>
                    </div>

                    <div class="rounded-[2rem] border border-white/10 bg-slate-900/80 p-6 shadow-2xl shadow-black/40 backdrop-blur-xl sm:p-8 lg:p-10">
                        <div class="flex items-start justify-between gap-6">
                            <div>
                                <span class="text-xs font-bold uppercase tracking-[0.24em] text-indigo-300">Daftar Admin</span>
                                <h2 class="mt-3 text-3xl font-black tracking-tight sm:text-4xl">Buat Workspace Payroll</h2>
                                <p class="mt-3 text-sm leading-6 text-slate-400">
                                    Masukkan data awal untuk membuat akun admin dan profil perusahaan.
                                </p>
                            </div>

                            <div class="hidden h-16 w-16 shrink-0 items-center justify-center rounded-3xl border border-indigo-300/20 bg-indigo-500/15 text-2xl font-black text-indigo-200 sm:flex">
                                HC
                            </div>
                        </div>

                        <div class="mt-6 rounded-2xl border border-cyan-300/20 bg-cyan-500/10 p-4 text-sm leading-6 text-cyan-50">
                            Setelah registrasi, Anda bisa melengkapi logo dan data perusahaan di halaman profil, lalu mulai import karyawan dan payroll.
                        </div>

                        <form method="POST" action="{{ route('register') }}" class="mt-8 space-y-5">
                            @csrf

                            <div>
                                <x-input-label for="name" :value="__('Nama Admin')" class="text-slate-200" />
                                <x-text-input id="name" class="mt-2 block h-14 w-full rounded-2xl border-white/10 bg-slate-950/70 px-4 text-white placeholder:text-slate-600 focus:border-indigo-400 focus:ring-indigo-400" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" placeholder="Contoh: Puji Ermanto" />
                                <x-input-error :messages="$errors->get('name')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="company_name" :value="__('Nama Perusahaan')" class="text-slate-200" />
                                <x-text-input id="company_name" class="mt-2 block h-14 w-full rounded-2xl border-white/10 bg-slate-950/70 px-4 text-white placeholder:text-slate-600 focus:border-indigo-400 focus:ring-indigo-400" type="text" name="company_name" :value="old('company_name')" required autocomplete="organization" placeholder="Contoh: PT. Citarasa Kuliner Indonesia" />
                                <x-input-error :messages="$errors->get('company_name')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="email" :value="__('Email Admin')" class="text-slate-200" />
                                <x-text-input id="email" class="mt-2 block h-14 w-full rounded-2xl border-white/10 bg-slate-950/70 px-4 text-white placeholder:text-slate-600 focus:border-indigo-400 focus:ring-indigo-400" type="email" name="email" :value="old('email')" required autocomplete="username" placeholder="admin@perusahaan.com" />
                                <x-input-error :messages="$errors->get('email')" class="mt-2" />
                            </div>

                            <div class="grid gap-5 sm:grid-cols-2">
                                <div x-data="{ show: false }">
                                    <x-input-label for="password" :value="__('Password')" class="text-slate-200" />
                                    <div class="relative mt-2">
                                        <x-text-input id="password" class="block h-14 w-full rounded-2xl border-white/10 bg-slate-950/70 px-4 pr-14 text-white placeholder:text-slate-600 focus:border-indigo-400 focus:ring-indigo-400" x-bind:type="show ? 'text' : 'password'" type="password" name="password" required autocomplete="new-password" placeholder="Minimal 8 karakter" />
                                        <button type="button" class="absolute inset-y-0 right-0 flex w-14 items-center justify-center text-slate-400 transition hover:text-white focus:outline-none focus:ring-2 focus:ring-inset focus:ring-indigo-300" x-on:click="show = !show" x-bind:aria-label="show ? 'Sembunyikan password' : 'Lihat password'">
                                            <svg x-show="!show" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12s3.75-6.75 9.75-6.75S21.75 12 21.75 12 18 18.75 12 18.75 2.25 12 2.25 12Z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                            </svg>
                                            <svg x-show="show" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="m3 3 18 18" />
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M10.6 5.46A8.9 8.9 0 0 1 12 5.25c6 0 9.75 6.75 9.75 6.75a17.6 17.6 0 0 1-3.1 3.82M6.2 6.88C3.76 8.58 2.25 12 2.25 12s3.75 6.75 9.75 6.75a8.8 8.8 0 0 0 4.03-.98" />
                                            </svg>
                                        </button>
                                    </div>
                                    <p class="mt-2 text-xs leading-5 text-slate-500">Minimal 8 karakter, wajib huruf besar-kecil dan angka.</p>
                                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                                </div>

                                <div x-data="{ show: false }">
                                    <x-input-label for="password_confirmation" :value="__('Konfirmasi Password')" class="text-slate-200" />
                                    <div class="relative mt-2">
                                        <x-text-input id="password_confirmation" class="block h-14 w-full rounded-2xl border-white/10 bg-slate-950/70 px-4 pr-14 text-white placeholder:text-slate-600 focus:border-indigo-400 focus:ring-indigo-400" x-bind:type="show ? 'text' : 'password'" type="password" name="password_confirmation" required autocomplete="new-password" placeholder="Ulangi password" />
                                        <button type="button" class="absolute inset-y-0 right-0 flex w-14 items-center justify-center text-slate-400 transition hover:text-white focus:outline-none focus:ring-2 focus:ring-inset focus:ring-indigo-300" x-on:click="show = !show" x-bind:aria-label="show ? 'Sembunyikan konfirmasi password' : 'Lihat konfirmasi password'">
                                            <svg x-show="!show" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12s3.75-6.75 9.75-6.75S21.75 12 21.75 12 18 18.75 12 18.75 2.25 12 2.25 12Z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                            </svg>
                                            <svg x-show="show" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="m3 3 18 18" />
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M10.6 5.46A8.9 8.9 0 0 1 12 5.25c6 0 9.75 6.75 9.75 6.75a17.6 17.6 0 0 1-3.1 3.82M6.2 6.88C3.76 8.58 2.25 12 2.25 12s3.75 6.75 9.75 6.75a8.8 8.8 0 0 0 4.03-.98" />
                                            </svg>
                                        </button>
                                    </div>
                                    <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                                </div>
                            </div>

                            <button type="submit" class="flex h-14 w-full items-center justify-center rounded-2xl bg-indigo-500 px-6 text-sm font-black uppercase tracking-[0.18em] text-white shadow-xl shadow-indigo-950/40 transition hover:bg-indigo-400 focus:outline-none focus:ring-2 focus:ring-indigo-300 focus:ring-offset-2 focus:ring-offset-slate-950">
                                Buat Workspace
                            </button>
                        </form>

                        <p class="mt-6 text-center text-sm text-slate-400">
                            Sudah punya akun?
                            <a class="font-bold text-indigo-200 underline-offset-4 transition hover:text-white hover:underline" href="{{ route('login') }}">
                                Login ke dashboard
                            </a>
                        </p>
                    </div>
                </div>
            </section>
        </div>
    </div>
</x-guest-layout>
