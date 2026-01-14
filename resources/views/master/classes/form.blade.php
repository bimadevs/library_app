<x-app-layout>
    <x-slot name="header">
        {{ $class->exists ? 'Edit Kelas' : 'Tambah Kelas' }}
    </x-slot>

    <div class="card max-w-2xl">
        <h3 class="card-header">{{ $class->exists ? 'Edit Kelas' : 'Tambah Kelas Baru' }}</h3>

        <form action="{{ $class->exists ? route('master.classes.update', $class) : route('master.classes.store') }}" method="POST">
            @csrf
            @if($class->exists)
                @method('PUT')
            @endif

            <div class="space-y-4">
                <div>
                    <label for="name" class="form-label">Nama Kelas <span class="text-red-500">*</span></label>
                    <input type="text" 
                           name="name" 
                           id="name" 
                           class="form-input @error('name') border-red-500 @enderror" 
                           value="{{ old('name', $class->name) }}"
                           placeholder="Contoh: X TJKT 1"
                           required>
                    @error('name')
                        <p class="form-error">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="level" class="form-label">Tingkat <span class="text-red-500">*</span></label>
                    <select name="level" id="level" class="form-select @error('level') border-red-500 @enderror" required>
                        <option value="">Pilih Tingkat</option>
                        @for($i = 10; $i <= 12; $i++)
                            <option value="{{ $i }}" {{ old('level', $class->level) == $i ? 'selected' : '' }}>
                                Kelas {{ $i }} ({{ ['X', 'XI', 'XII'][$i - 10] }})
                            </option>
                        @endfor
                    </select>
                    @error('level')
                        <p class="form-error">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="flex items-center gap-3 mt-6 pt-6 border-t border-slate-200">
                <button type="submit" class="btn btn-primary">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    {{ $class->exists ? 'Simpan Perubahan' : 'Simpan' }}
                </button>
                <a href="{{ route('master.classes.index') }}" class="btn btn-secondary">Batal</a>
            </div>
        </form>
    </div>
</x-app-layout>
