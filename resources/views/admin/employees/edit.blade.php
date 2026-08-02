<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Edit Karyawan</h2>
    </x-slot>

    <div class="py-6">
        <div class="mx-auto max-w-4xl sm:px-6 lg:px-8">
            <div class="rounded-xl bg-white dark:bg-gray-800 shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                <form action="{{ route('admin.employees.update', $employee) }}" method="POST" x-data="{ createUser: {{ old('create_user') ? 'true' : 'false' }} }">
                    @csrf
                    @method('PUT')

                    <div class="grid gap-6 lg:grid-cols-2">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">NIP</label>
                            <input type="text" name="nip" value="{{ old('nip', $employee->nip) }}"
                                class="mt-2 block w-full rounded-lg border-gray-300 bg-white text-gray-900 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100"
                                required>
                            @error('nip')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Email</label>
                            <input type="email" name="email" value="{{ old('email', $employee->email) }}"
                                class="mt-2 block w-full rounded-lg border-gray-300 bg-white text-gray-900 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100"
                                required>
                            @error('email')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="lg:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Nama
                                Lengkap</label>
                            <input type="text" name="name" value="{{ old('name', $employee->name) }}"
                                class="mt-2 block w-full rounded-lg border-gray-300 bg-white text-gray-900 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100"
                                required>
                            @error('name')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">No HP</label>
                            <input type="text" name="phone" value="{{ old('phone', $employee->phone) }}"
                                class="mt-2 block w-full rounded-lg border-gray-300 bg-white text-gray-900 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100">
                            @error('phone')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Tanggal
                                Bergabung</label>
                            <input type="date" name="join_date"
                                value="{{ old('join_date', $employee->join_date?->format('Y-m-d')) }}"
                                class="mt-2 block w-full rounded-lg border-gray-300 bg-white text-gray-900 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100"
                                required>
                            @error('join_date')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Department</label>
                            <select name="department_id"
                                class="mt-2 block w-full rounded-lg border-gray-300 bg-white text-gray-900 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100"
                                required>
                                <option value="">Pilih Department</option>
                                @foreach ($departments as $department)
                                    <option value="{{ $department->id }}"
                                        {{ old('department_id', $employee->department_id) == $department->id ? 'selected' : '' }}>
                                        {{ $department->name }}</option>
                                @endforeach
                            </select>
                            @error('department_id')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Jabatan</label>
                            <select name="position_id"
                                class="mt-2 block w-full rounded-lg border-gray-300 bg-white text-gray-900 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100"
                                required>
                                <option value="">Pilih Jabatan</option>
                                @foreach ($positions as $position)
                                    <option value="{{ $position->id }}"
                                        {{ old('position_id', $employee->position_id) == $position->id ? 'selected' : '' }}>
                                        {{ $position->name }}</option>
                                @endforeach
                            </select>
                            @error('position_id')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        @if ($employee->user)
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Password Baru
                                    (opsional)</label>
                                <input type="password" name="password"
                                    class="mt-2 block w-full rounded-lg border-gray-300 bg-white text-gray-900 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100"
                                    autocomplete="new-password">
                                <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">Kosongkan jika tidak ingin mengubah password akun login.</p>
                                @error('password')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Konfirmasi Password Baru</label>
                                <input type="password" name="password_confirmation"
                                    class="mt-2 block w-full rounded-lg border-gray-300 bg-white text-gray-900 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100"
                                    autocomplete="new-password">
                                @error('password_confirmation')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        @else
                            <div class="lg:col-span-2 rounded-xl border border-indigo-200 bg-indigo-50 p-4 dark:border-indigo-500/20 dark:bg-indigo-500/10">
                                <label class="flex items-start gap-3">
                                    <input type="checkbox" name="create_user" value="1" x-model="createUser"
                                        class="mt-1 rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900">
                                    <span>
                                        <span class="block text-sm font-semibold text-gray-800 dark:text-gray-100">Buat akun login untuk karyawan ini</span>
                                        <span class="mt-1 block text-sm leading-6 text-gray-600 dark:text-gray-300">
                                            Employee ini hanya master data payroll. Aktifkan jika karyawan perlu akses dashboard.
                                        </span>
                                    </span>
                                </label>
                                @error('create_user')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div x-show="createUser" style="display: none;">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Password Akun</label>
                                <input type="password" name="password"
                                    class="mt-2 block w-full rounded-lg border-gray-300 bg-white text-gray-900 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100"
                                    autocomplete="new-password">
                                <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">Minimal 8 karakter, wajib huruf besar-kecil dan angka.</p>
                                @error('password')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div x-show="createUser" style="display: none;">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Konfirmasi Password</label>
                                <input type="password" name="password_confirmation"
                                    class="mt-2 block w-full rounded-lg border-gray-300 bg-white text-gray-900 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100"
                                    autocomplete="new-password">
                                @error('password_confirmation')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        @endif
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Gaji Pokok</label>
                            <input type="number" step="0.01" name="basic_salary"
                                value="{{ old('basic_salary', $employee->basic_salary) }}"
                                class="mt-2 block w-full rounded-lg border-gray-300 bg-white text-gray-900 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100"
                                required>
                            @error('basic_salary')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Tunjangan</label>
                            <input type="number" step="0.01" name="allowance"
                                value="{{ old('allowance', $employee->allowance) }}"
                                class="mt-2 block w-full rounded-lg border-gray-300 bg-white text-gray-900 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100"
                                required>
                            @error('allowance')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="lg:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Alamat</label>
                            <textarea name="address" rows="4"
                                class="mt-2 block w-full rounded-lg border-gray-300 bg-white text-gray-900 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100">{{ old('address', $employee->address) }}</textarea>
                            @error('address')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="lg:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Status</label>
                            <select name="status"
                                class="mt-2 block w-full rounded-lg border-gray-300 bg-white text-gray-900 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100"
                                required>
                                <option value="active"
                                    {{ old('status', $employee->status) == 'active' ? 'selected' : '' }}>Active
                                </option>
                                <option value="inactive"
                                    {{ old('status', $employee->status) == 'inactive' ? 'selected' : '' }}>Inactive
                                </option>
                            </select>
                            @error('status')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end gap-3">
                        <a href="{{ route('admin.employees.index') }}"
                            class="inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200">Batal</a>
                        <button type="submit"
                            class="inline-flex items-center rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700">Perbarui</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
