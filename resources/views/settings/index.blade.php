<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-800 leading-tight">
            {{ __('Pengaturan Identitas') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-slate-900">

                    <form action="{{ route('settings.update') }}" method="POST" enctype="multipart/form-data" class="space-y-6 max-w-2xl">
                        @csrf
                        @method('PUT')

                        <!-- School Name -->
                        <div>
                            <x-input-label for="school_name" :value="__('Nama Sekolah / Perpustakaan')" />
                            <x-text-input id="school_name" name="school_name" type="text" class="mt-1 block w-full" :value="old('school_name', $setting->school_name)" required autofocus />
                            <x-input-error class="mt-2" :messages="$errors->get('school_name')" />
                        </div>

                        <!-- Address -->
                        <div>
                            <x-input-label for="school_address" :value="__('Alamat')" />
                            <textarea id="school_address" name="school_address" class="mt-1 block w-full border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" rows="3">{{ old('school_address', $setting->school_address) }}</textarea>
                            <x-input-error class="mt-2" :messages="$errors->get('school_address')" />
                        </div>

                        <!-- Logo -->
                        <div>
                            <x-input-label for="school_logo" :value="__('Logo Sekolah')" />

                            @if($setting->logo_url)
                                <div class="mt-2 mb-4">
                                    <p class="text-sm text-slate-500 mb-2">Logo Saat Ini:</p>
                                    <img src="{{ $setting->logo_url }}" alt="Logo" class="h-20 w-auto object-contain border p-1 rounded">
                                </div>
                            @endif

                            <input id="school_logo" name="school_logo" type="file" class="mt-1 block w-full text-sm text-slate-500
                                file:mr-4 file:py-2 file:px-4
                                file:rounded-full file:border-0
                                file:text-sm file:font-semibold
                                file:bg-indigo-50 file:text-indigo-700
                                hover:file:bg-indigo-100
                            " accept="image/*" />
                            <p class="mt-1 text-sm text-slate-500">Format: JPG, PNG, GIF. Max: 2MB.</p>
                            <x-input-error class="mt-2" :messages="$errors->get('school_logo')" />
                        </div>

                        <div class="flex items-center gap-4">
                            <x-primary-button>{{ __('Simpan Perubahan') }}</x-primary-button>

                            @if (session('success'))
                                <p
                                    x-data="{ show: true }"
                                    x-show="show"
                                    x-transition
                                    x-init="setTimeout(() => show = false, 2000)"
                                    class="text-sm text-emerald-600"
                                >{{ session('success') }}</p>
                            @endif
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
