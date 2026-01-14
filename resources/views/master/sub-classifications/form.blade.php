<x-app-layout>
    <x-slot name="header">
        {{ $subClassification->exists ? 'Edit Sub Klasifikasi' : 'Tambah Sub Klasifikasi' }}
    </x-slot>

    <div class="card max-w-2xl">
        <h3 class="card-header">{{ $subClassification->exists ? 'Edit Sub Klasifikasi' : 'Tambah Sub Klasifikasi Baru' }}</h3>

        <form action="{{ $subClassification->exists ? route('master.sub-classifications.update', $subClassification) : route('master.sub-classifications.store') }}" method="POST">
            @csrf
            @if($subClassification->exists)
                @method('PUT')
            @endif

            <div class="space-y-4">
                <div>
                    <label for="classification_id" class="form-label">Klasifikasi Induk <span class="text-red-500">*</span></label>
                    <select name="classification_id" 
                            id="classification_id" 
                            class="form-select @error('classification_id') border-red-500 @enderror" 
                            required>
                        <option value="">Pilih Klasifikasi</option>
                        @foreach($classifications as $classification)
                            <option value="{{ $classification->id }}" 
                                    {{ old('classification_id', $subClassification->classification_id) == $classification->id ? 'selected' : '' }}>
                                {{ $classification->ddc_code }} - {{ $classification->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('classification_id')
                        <p class="form-error">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="sub_ddc_code" class="form-label">Kode Sub DDC <span class="text-red-500">*</span></label>
                    <input type="text" 
                           name="sub_ddc_code" 
                           id="sub_ddc_code" 
                           class="form-input @error('sub_ddc_code') border-red-500 @enderror" 
                           value="{{ old('sub_ddc_code', $subClassification->sub_ddc_code) }}"
                           placeholder="Contoh: 000 - 009"
                           required>
                    <p class="text-xs text-slate-500 mt-1">Masukkan rentang kode sub DDC (contoh: 000 - 009)</p>
                    @error('sub_ddc_code')
                        <p class="form-error">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="name" class="form-label">Nama Sub Klasifikasi <span class="text-red-500">*</span></label>
                    <input type="text" 
                           name="name" 
                           id="name" 
                           class="form-input @error('name') border-red-500 @enderror" 
                           value="{{ old('name', $subClassification->name) }}"
                           placeholder="Contoh: Ilmu Umum dan Komputer"
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
                    {{ $subClassification->exists ? 'Simpan Perubahan' : 'Simpan' }}
                </button>
                <a href="{{ route('master.sub-classifications.index') }}" class="btn btn-secondary">Batal</a>
            </div>
        </form>
    </div>
</x-app-layout>
