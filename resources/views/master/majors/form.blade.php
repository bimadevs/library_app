<x-app-layout>
    <x-slot name="header">
        {{ $major->exists ? 'Edit Jurusan' : 'Tambah Jurusan' }}
    </x-slot>

    <div class="card max-w-2xl">
        <h3 class="card-header">{{ $major->exists ? 'Edit Jurusan' : 'Tambah Jurusan Baru' }}</h3>

        <form action="{{ $major->exists ? route('master.majors.update', $major) : route('master.majors.store') }}" method="POST">
            @csrf
            @if($major->exists)
                @method('PUT')
            @endif

            <div class="space-y-4">
                <div>
                    <label for="code" class="form-label">Kode Jurusan <span class="text-red-500">*</span></label>
                    <input type="text" 
                           name="code" 
                           id="code" 
                           class="form-input @error('code') border-red-500 @enderror" 
                           value="{{ old('code', $major->code) }}"
                           placeholder="Contoh: TJKT"
                           required>
                    @error('code')
                        <p class="form-error">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="name" class="form-label">Nama Jurusan <span class="text-red-500">*</span></label>
                    <input type="text" 
                           name="name" 
                           id="name" 
                           class="form-input @error('name') border-red-500 @enderror" 
                           value="{{ old('name', $major->name) }}"
                           placeholder="Contoh: Teknik Jaringan Komputer dan Telekomunikasi"
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
                    {{ $major->exists ? 'Simpan Perubahan' : 'Simpan' }}
                </button>
                <a href="{{ route('master.majors.index') }}" class="btn btn-secondary">Batal</a>
            </div>
        </form>
    </div>
</x-app-layout>
