<x-app-layout>
    <x-slot name="header">
        {{ $category->exists ? 'Edit Kategori' : 'Tambah Kategori' }}
    </x-slot>

    <div class="card max-w-2xl">
        <h3 class="card-header">{{ $category->exists ? 'Edit Kategori' : 'Tambah Kategori Baru' }}</h3>

        <form action="{{ $category->exists ? route('master.categories.update', $category) : route('master.categories.store') }}" method="POST">
            @csrf
            @if($category->exists)
                @method('PUT')
            @endif

            <div class="space-y-4">
                <div>
                    <label for="name" class="form-label">Nama Kategori <span class="text-red-500">*</span></label>
                    <input type="text" 
                           name="name" 
                           id="name" 
                           class="form-input @error('name') border-red-500 @enderror" 
                           value="{{ old('name', $category->name) }}"
                           placeholder="Contoh: Fiksi, Non-Fiksi, Referensi"
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
                    {{ $category->exists ? 'Simpan Perubahan' : 'Simpan' }}
                </button>
                <a href="{{ route('master.categories.index') }}" class="btn btn-secondary">Batal</a>
            </div>
        </form>
    </div>
</x-app-layout>
