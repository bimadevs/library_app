<x-app-layout>
    <x-slot name="header">
        {{ $academicYear->exists ? 'Edit Tahun Ajaran' : 'Tambah Tahun Ajaran' }}
    </x-slot>

    <div class="card max-w-2xl">
        <h3 class="card-header">{{ $academicYear->exists ? 'Edit Tahun Ajaran' : 'Tambah Tahun Ajaran Baru' }}</h3>

        <form action="{{ $academicYear->exists ? route('master.academic-years.update', $academicYear) : route('master.academic-years.store') }}" method="POST">
            @csrf
            @if($academicYear->exists)
                @method('PUT')
            @endif

            <div class="space-y-4">
                <div>
                    <label for="name" class="form-label">Nama Tahun Ajaran <span class="text-red-500">*</span></label>
                    <input type="text" 
                           name="name" 
                           id="name" 
                           class="form-input @error('name') border-red-500 @enderror" 
                           value="{{ old('name', $academicYear->name) }}"
                           placeholder="Contoh: 2024/2025"
                           required>
                    @error('name')
                        <p class="form-error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center gap-2">
                    <input type="checkbox" 
                           name="is_active" 
                           id="is_active" 
                           value="1"
                           class="rounded border-slate-300 text-emerald-600 focus:ring-emerald-500"
                           {{ old('is_active', $academicYear->is_active) ? 'checked' : '' }}>
                    <label for="is_active" class="text-sm text-slate-700">Tahun ajaran aktif</label>
                </div>
            </div>

            <div class="flex items-center gap-3 mt-6 pt-6 border-t border-slate-200">
                <button type="submit" class="btn btn-primary">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    {{ $academicYear->exists ? 'Simpan Perubahan' : 'Simpan' }}
                </button>
                <a href="{{ route('master.academic-years.index') }}" class="btn btn-secondary">Batal</a>
            </div>
        </form>
    </div>
</x-app-layout>
