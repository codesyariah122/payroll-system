<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
            {{ __('Profile Information') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
            {{ __("Update your account's profile information and email address.") }}
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="mt-6 space-y-6">
        @csrf
        @method('patch')

        <div>
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $user->name)" required autofocus autocomplete="name" />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email', $user->email)" required autocomplete="username" />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div>
                    <p class="text-sm mt-2 text-gray-800 dark:text-gray-200">
                        {{ __('Your email address is unverified.') }}

                        <button form="send-verification" class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800">
                            {{ __('Click here to re-send the verification email.') }}
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 font-medium text-sm text-green-600 dark:text-green-400">
                            {{ __('A new verification link has been sent to your email address.') }}
                        </p>
                    @endif
                </div>
            @endif
        </div>

        @if ($user->isAdmin())
            <div class="border-t border-white/10 pt-6">
                <h3 class="text-base font-semibold text-gray-900 dark:text-gray-100">
                    {{ __('Profil Perusahaan') }}
                </h3>

                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                    {{ __('Logo dan data ini dipakai di dashboard perusahaan.') }}
                </p>
            </div>

            <div>
                <x-input-label for="company_logo" :value="__('Logo Perusahaan')" />

                @php($initialLogo = $company?->logo_path ? asset($company->logo_path) : null)

                <div
                    x-data="{
                        isDragging: false,
                        previewUrl: @js($initialLogo),
                        fileName: '',
                        pickFile() {
                            this.$refs.companyLogoInput.click();
                        },
                        setFile(file) {
                            if (!file || !file.type.startsWith('image/')) {
                                return;
                            }

                            this.fileName = file.name;
                            this.previewUrl = URL.createObjectURL(file);
                        },
                        handleInput(event) {
                            this.setFile(event.target.files[0]);
                        },
                        handleDrop(event) {
                            this.isDragging = false;
                            const file = event.dataTransfer.files[0];

                            if (!file || !file.type.startsWith('image/')) {
                                return;
                            }

                            const transfer = new DataTransfer();
                            transfer.items.add(file);
                            this.$refs.companyLogoInput.files = transfer.files;
                            this.setFile(file);
                        }
                    }"
                    class="mt-2">
                    <input
                        x-ref="companyLogoInput"
                        id="company_logo"
                        name="company_logo"
                        type="file"
                        accept="image/png,image/jpeg,image/webp"
                        class="sr-only"
                        @change="handleInput">

                    <button
                        type="button"
                        @click="pickFile"
                        @dragover.prevent="isDragging = true"
                        @dragleave.prevent="isDragging = false"
                        @drop.prevent="handleDrop"
                        class="group flex w-full flex-col items-center justify-center rounded-3xl border border-dashed border-indigo-400/30 bg-slate-950/50 px-6 py-8 text-center transition hover:border-cyan-300/50 hover:bg-cyan-400/[0.04] focus:outline-none focus:ring-2 focus:ring-indigo-500"
                        :class="isDragging ? 'border-cyan-300/70 bg-cyan-400/[0.08]' : ''">

                        <div class="flex h-24 w-24 items-center justify-center overflow-hidden rounded-2xl border border-white/10 bg-slate-900/80 ring-1 ring-white/5">
                            <template x-if="previewUrl">
                                <img :src="previewUrl" alt="Preview logo perusahaan" class="h-full w-full object-contain p-3">
                            </template>

                            <template x-if="!previewUrl">
                                <span class="text-sm font-semibold uppercase text-indigo-300">
                                    {{ strtoupper(substr($company?->name ?? config('app.name'), 0, 2)) }}
                                </span>
                            </template>
                        </div>

                        <div class="mt-4">
                            <p class="text-sm font-semibold text-slate-100">
                                Tarik logo ke sini atau klik untuk pilih file
                            </p>

                            <p class="mt-1 text-xs text-slate-400">
                                PNG, JPG, JPEG, atau WEBP. Maksimal 2 MB.
                            </p>

                            <p x-show="fileName" x-text="fileName" class="mt-3 truncate text-xs font-semibold text-cyan-300"></p>
                        </div>
                    </button>
                </div>

                <x-input-error class="mt-2" :messages="$errors->get('company_logo')" />
            </div>

            <div>
                <x-input-label for="company_name" :value="__('Nama Perusahaan')" />
                <x-text-input id="company_name" name="company_name" type="text" class="mt-1 block w-full" :value="old('company_name', $company?->name)" autocomplete="organization" />
                <x-input-error class="mt-2" :messages="$errors->get('company_name')" />
            </div>

            <div>
                <x-input-label for="company_description" :value="__('Deskripsi')" />
                <textarea id="company_description" name="company_description" rows="3"
                    class="mt-1 block w-full rounded-md border-gray-300 bg-white text-gray-900 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100">{{ old('company_description', $company?->description) }}</textarea>
                <x-input-error class="mt-2" :messages="$errors->get('company_description')" />
            </div>

            <div class="grid gap-4 md:grid-cols-2">
                <div>
                    <x-input-label for="company_email" :value="__('Email Perusahaan')" />
                    <x-text-input id="company_email" name="company_email" type="email" class="mt-1 block w-full" :value="old('company_email', $company?->email)" autocomplete="email" />
                    <x-input-error class="mt-2" :messages="$errors->get('company_email')" />
                </div>

                <div>
                    <x-input-label for="company_phone" :value="__('Telepon')" />
                    <x-text-input id="company_phone" name="company_phone" type="text" class="mt-1 block w-full" :value="old('company_phone', $company?->phone)" autocomplete="tel" />
                    <x-input-error class="mt-2" :messages="$errors->get('company_phone')" />
                </div>
            </div>

            <div>
                <x-input-label for="company_website" :value="__('Website')" />
                <x-text-input id="company_website" name="company_website" type="text" class="mt-1 block w-full" :value="old('company_website', $company?->website)" autocomplete="url" />
                <x-input-error class="mt-2" :messages="$errors->get('company_website')" />
            </div>

            <div>
                <x-input-label for="company_address" :value="__('Alamat Lengkap')" />
                <textarea id="company_address" name="company_address" rows="3"
                    class="mt-1 block w-full rounded-md border-gray-300 bg-white text-gray-900 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100">{{ old('company_address', $company?->address) }}</textarea>
                <x-input-error class="mt-2" :messages="$errors->get('company_address')" />
            </div>

            <div class="grid gap-4 md:grid-cols-2">
                <div>
                    <x-input-label for="company_city" :value="__('Kota')" />
                    <x-text-input id="company_city" name="company_city" type="text" class="mt-1 block w-full" :value="old('company_city', $company?->city)" />
                    <x-input-error class="mt-2" :messages="$errors->get('company_city')" />
                </div>

                <div>
                    <x-input-label for="company_province" :value="__('Provinsi')" />
                    <x-text-input id="company_province" name="company_province" type="text" class="mt-1 block w-full" :value="old('company_province', $company?->province)" />
                    <x-input-error class="mt-2" :messages="$errors->get('company_province')" />
                </div>

                <div>
                    <x-input-label for="company_postal_code" :value="__('Kode Pos')" />
                    <x-text-input id="company_postal_code" name="company_postal_code" type="text" class="mt-1 block w-full" :value="old('company_postal_code', $company?->postal_code)" />
                    <x-input-error class="mt-2" :messages="$errors->get('company_postal_code')" />
                </div>

                <div>
                    <x-input-label for="company_country" :value="__('Negara')" />
                    <x-text-input id="company_country" name="company_country" type="text" class="mt-1 block w-full" :value="old('company_country', $company?->country ?? 'Indonesia')" />
                    <x-input-error class="mt-2" :messages="$errors->get('company_country')" />
                </div>
            </div>

            <div>
                <x-input-label for="company_tax_id" :value="__('NPWP / Tax ID')" />
                <x-text-input id="company_tax_id" name="company_tax_id" type="text" class="mt-1 block w-full" :value="old('company_tax_id', $company?->tax_id)" />
                <x-input-error class="mt-2" :messages="$errors->get('company_tax_id')" />
            </div>
        @endif

        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Save') }}</x-primary-button>

            @if (session('status') === 'profile-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-gray-600 dark:text-gray-400"
                >{{ __('Saved.') }}</p>
            @endif
        </div>
    </form>
</section>
