<x-app-layout>
    <x-slot name="header">
        {{ $bookSource->exists ? 'Edit Sumber Buku' : 'Tambah Sumber Buku' }}
    </x-slot>

    <div class="card max-w-2xl">
        <h3 class="card-header">{{ $bookSource->exists ? 'Edit Sumber Buku' : 'Tambah Sumber Buku Baru' }}</h3>

        <form action="{{ $bookSource->exists ? route('master.book-sources.update', $bookSource) : route('master.book-sources.store') }}" method="POST">
            @csrf
            @if($bookSource->exists)
                @method('PUT')
            @endif

            <div class="space-y-4">
                <div>
                    <label for="name" class="form-label">Nama Sumber <span class="text-red-500">*</span></label>
                    <input type="text" 
                           name="name" 
                           id="name" 
                           class="form-input @error('name') border-red-500 @enderror" 
                           value="{{ old('name', $bookSource->name) }}"
                           placeholder="Contoh: Pembelian, Hibah, Sumbangan"
                           required>
                    @error('name')
                        <p class="form-error">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="description" class="form-label">Deskripsi</label>
                    <textarea name="description" 
                              id="description" 
                              rows="3"
                              class="form-input @error('description') border-red-500 @enderror" 
                              placeholder="Deskripsi sumber buku (opsional)">{{ old('description', $bookSource->description) }}</textarea>
                    @error('description')
                        <p class="form-error">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="flex items-center gap-3 mt-6 pt-6 border-t border-slate-200">
                <button type="submit" class="btn btn-primary">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    {{ $bookSource->exists ? 'Simpan Perubahan' : 'Simpan' }}
                </button>
                <a href="{{ route('master.book-sources.index') }}" class="btn btn-secondary">Batal</a>
            </div>
        </form>
    </div>
</x-app-layout>
