<x-app-layout>
    <x-slot name="header">
        {{ $classification->exists ? 'Edit Klasifikasi DDC' : 'Tambah Klasifikasi DDC' }}
    </x-slot>

    <div class="card max-w-2xl">
        <h3 class="card-header">{{ $classification->exists ? 'Edit Klasifikasi DDC' : 'Tambah Klasifikasi DDC Baru' }}</h3>

        <form action="{{ $classification->exists ? route('master.classifications.update', $classification) : route('master.classifications.store') }}" method="POST">
            @csrf
            @if($classification->exists)
                @method('PUT')
            @endif

            <div class="space-y-4">
                <div>
                    <label for="ddc_code" class="form-label">Kode DDC <span class="text-red-500">*</span></label>
                    <input type="text" 
                           name="ddc_code" 
                           id="ddc_code" 
                           class="form-input @error('ddc_code') border-red-500 @enderror" 
                           value="{{ old('ddc_code', $classification->ddc_code) }}"
                           placeholder="Contoh: 000"
                           required>
                    <p class="text-xs text-slate-500 mt-1">Masukkan kode DDC 3 digit (contoh: 000, 100, 200, dst.)</p>
                    @error('ddc_code')
                        <p class="form-error">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="name" class="form-label">Nama Klasifikasi <span class="text-red-500">*</span></label>
                    <input type="text" 
                           name="name" 
                           id="name" 
                           class="form-input @error('name') border-red-500 @enderror" 
                           value="{{ old('name', $classification->name) }}"
                           placeholder="Contoh: Ilmu Komputer, Informasi, dan Karya Umum"
                           required>
                    @error('name')
                        <p class="form-error">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="flex items-center gap-3 mt-6 pt-6 border-t border-slate-200">
                <button type="submit" class="btn btn-primary">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    {{ $classification->exists ? 'Simpan Perubahan' : 'Simpan' }}
                </button>
                <a href="{{ route('master.classifications.index') }}" class="btn btn-secondary">Batal</a>
            </div>
        </form>
    </div>
</x-app-layout>
