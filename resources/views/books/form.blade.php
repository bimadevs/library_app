<x-app-layout>
    <x-slot name="header">
        {{ $book->exists ? 'Edit Buku' : 'Tambah Buku' }}
    </x-slot>

    <div class="card max-w-5xl">
        <h3 class="card-header">{{ $book->exists ? 'Edit Data Buku' : 'Tambah Buku Baru' }}</h3>

        <form action="{{ $book->exists ? route('books.update', $book) : route('books.store') }}" method="POST">
            @csrf
            @if($book->exists)
                @method('PUT')
            @endif

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <!-- Kode Buku -->
                <div>
                    <label for="code" class="form-label">Kode Buku <span class="text-red-500">*</span></label>
                    <input type="text" 
                           name="code" 
                           id="code" 
                           class="form-input @error('code') border-red-500 @enderror" 
                           value="{{ old('code', $book->code) }}"
                           placeholder="Contoh: BK001"
                           required>
                    @error('code')
                        <p class="form-error">{{ $message }}</p>
                    @enderror
                </div>

                <!-- ISBN -->
                <div>
                    <label for="isbn" class="form-label">ISBN</label>
                    <input type="text" 
                           name="isbn" 
                           id="isbn" 
                           class="form-input @error('isbn') border-red-500 @enderror" 
                           value="{{ old('isbn', $book->isbn) }}"
                           placeholder="Nomor ISBN">
                    @error('isbn')
                        <p class="form-error">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Entry Date -->
                <div>
                    <label for="entry_date" class="form-label">Tanggal Masuk <span class="text-red-500">*</span></label>
                    <input type="date" 
                           name="entry_date" 
                           id="entry_date" 
                           class="form-input @error('entry_date') border-red-500 @enderror" 
                           value="{{ old('entry_date', $book->entry_date?->format('Y-m-d') ?? now()->format('Y-m-d')) }}"
                           required>
                    @error('entry_date')
                        <p class="form-error">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Title -->
                <div class="md:col-span-3">
                    <label for="title" class="form-label">Judul Buku <span class="text-red-500">*</span></label>
                    <input type="text" 
                           name="title" 
                           id="title" 
                           class="form-input @error('title') border-red-500 @enderror" 
                           value="{{ old('title', $book->title) }}"
                           placeholder="Judul lengkap buku"
                           required>
                    @error('title')
                        <p class="form-error">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Author -->
                <div class="md:col-span-2">
                    <label for="author" class="form-label">Pengarang <span class="text-red-500">*</span></label>
                    <input type="text" 
                           name="author" 
                           id="author" 
                           class="form-input @error('author') border-red-500 @enderror" 
                           value="{{ old('author', $book->author) }}"
                           placeholder="Nama pengarang"
                           required>
                    @error('author')
                        <p class="form-error">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Publisher -->
                <div>
                    <label for="publisher_id" class="form-label">Penerbit <span class="text-red-500">*</span></label>
                    <select name="publisher_id" 
                            id="publisher_id" 
                            class="form-select @error('publisher_id') border-red-500 @enderror"
                            required>
                        <option value="">Pilih Penerbit</option>
                        @foreach($publishers as $publisher)
                            <option value="{{ $publisher->id }}" {{ old('publisher_id', $book->publisher_id) == $publisher->id ? 'selected' : '' }}>
                                {{ $publisher->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('publisher_id')
                        <p class="form-error">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Publish Place -->
                <div>
                    <label for="publish_place" class="form-label">Tempat Terbit <span class="text-red-500">*</span></label>
                    <input type="text" 
                           name="publish_place" 
                           id="publish_place" 
                           class="form-input @error('publish_place') border-red-500 @enderror" 
                           value="{{ old('publish_place', $book->publish_place) }}"
                           placeholder="Kota terbit"
                           required>
                    @error('publish_place')
                        <p class="form-error">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Publish Year -->
                <div>
                    <label for="publish_year" class="form-label">Tahun Terbit <span class="text-red-500">*</span></label>
                    <input type="number" 
                           name="publish_year" 
                           id="publish_year" 
                           class="form-input @error('publish_year') border-red-500 @enderror" 
                           value="{{ old('publish_year', $book->publish_year) }}"
                           min="1900"
                           max="{{ date('Y') + 1 }}"
                           placeholder="Tahun"
                           required>
                    @error('publish_year')
                        <p class="form-error">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Stock -->
                <div>
                    <label for="stock" class="form-label">Jumlah Stok <span class="text-red-500">*</span></label>
                    <input type="number" 
                           name="stock" 
                           id="stock" 
                           class="form-input @error('stock') border-red-500 @enderror" 
                           value="{{ old('stock', $book->stock ?? 1) }}"
                           min="1"
                           required>
                    @error('stock')
                        <p class="form-error">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Classification -->
                <div>
                    <label for="classification_id" class="form-label">Klasifikasi DDC <span class="text-red-500">*</span></label>
                    <select name="classification_id" 
                            id="classification_id" 
                            class="form-select @error('classification_id') border-red-500 @enderror"
                            required>
                        <option value="">Pilih Klasifikasi</option>
                        @foreach($classifications as $classification)
                            <option value="{{ $classification->id }}" {{ old('classification_id', $book->classification_id) == $classification->id ? 'selected' : '' }}>
                                {{ $classification->ddc_code }} - {{ $classification->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('classification_id')
                        <p class="form-error">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Sub Classification -->
                <div>
                    <label for="sub_classification_id" class="form-label">Sub Klasifikasi</label>
                    <select name="sub_classification_id" 
                            id="sub_classification_id" 
                            class="form-select @error('sub_classification_id') border-red-500 @enderror">
                        <option value="">Pilih Sub Klasifikasi</option>
                        @foreach($subClassifications as $subClassification)
                            <option value="{{ $subClassification->id }}" {{ old('sub_classification_id', $book->sub_classification_id) == $subClassification->id ? 'selected' : '' }}>
                                {{ $subClassification->sub_ddc_code }} - {{ $subClassification->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('sub_classification_id')
                        <p class="form-error">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Category -->
                <div>
                    <label for="category_id" class="form-label">Kategori <span class="text-red-500">*</span></label>
                    <select name="category_id" 
                            id="category_id" 
                            class="form-select @error('category_id') border-red-500 @enderror"
                            required>
                        <option value="">Pilih Kategori</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ old('category_id', $book->category_id) == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('category_id')
                        <p class="form-error">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Page Count -->
                <div>
                    <label for="page_count" class="form-label">Jumlah Halaman <span class="text-red-500">*</span></label>
                    <input type="number" 
                           name="page_count" 
                           id="page_count" 
                           class="form-input @error('page_count') border-red-500 @enderror" 
                           value="{{ old('page_count', $book->page_count) }}"
                           min="1"
                           placeholder="Jumlah halaman"
                           required>
                    @error('page_count')
                        <p class="form-error">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Thickness -->
                <div>
                    <label for="thickness" class="form-label">Ketebalan</label>
                    <input type="text" 
                           name="thickness" 
                           id="thickness" 
                           class="form-input @error('thickness') border-red-500 @enderror" 
                           value="{{ old('thickness', $book->thickness) }}"
                           placeholder="Contoh: 2 cm">
                    @error('thickness')
                        <p class="form-error">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Shelf Location -->
                <div>
                    <label for="shelf_location" class="form-label">Lokasi Rak <span class="text-red-500">*</span></label>
                    <input type="text" 
                           name="shelf_location" 
                           id="shelf_location" 
                           class="form-input @error('shelf_location') border-red-500 @enderror" 
                           value="{{ old('shelf_location', $book->shelf_location) }}"
                           placeholder="Contoh: A-01-02"
                           required>
                    @error('shelf_location')
                        <p class="form-error">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Source -->
                <div>
                    <label for="book_source_id" class="form-label">Sumber Buku <span class="text-red-500">*</span></label>
                    <select name="book_source_id" 
                            id="book_source_id" 
                            class="form-select @error('book_source_id') border-red-500 @enderror"
                            required>
                        <option value="">Pilih Sumber Buku</option>
                        @foreach($bookSources as $bookSource)
                            <option value="{{ $bookSource->id }}" {{ old('book_source_id', $book->book_source_id) == $bookSource->id ? 'selected' : '' }}>
                                {{ $bookSource->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('book_source_id')
                        <p class="form-error">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Price -->
                <div>
                    <label for="price" class="form-label">Harga</label>
                    <input type="number" 
                           name="price" 
                           id="price" 
                           class="form-input @error('price') border-red-500 @enderror" 
                           value="{{ old('price', $book->price) }}"
                           min="0"
                           step="0.01"
                           placeholder="Harga buku">
                    @error('price')
                        <p class="form-error">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Textbook Flag -->
                <div class="md:col-span-3">
                    <label class="flex items-center gap-2">
                        <input type="checkbox" 
                               name="is_textbook" 
                               id="is_textbook"
                               value="1"
                               class="form-checkbox rounded border-slate-300 text-primary-600 focus:ring-primary-500 @error('is_textbook') border-red-500 @enderror"
                               {{ old('is_textbook', $book->is_textbook ?? false) ? 'checked' : '' }}>
                        <span class="form-label mb-0">Buku Paket Pelajaran</span>
                    </label>
                    <p class="text-sm text-slate-500 mt-1">
                        Centang jika buku ini adalah buku paket pelajaran. Peminjaman buku paket tidak dihitung dalam batas peminjaman siswa.
                    </p>
                    @error('is_textbook')
                        <p class="form-error">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Description -->
                <div class="md:col-span-3">
                    <label for="description" class="form-label">Deskripsi</label>
                    <textarea name="description" 
                              id="description" 
                              rows="3"
                              class="form-input @error('description') border-red-500 @enderror" 
                              placeholder="Deskripsi atau sinopsis buku (opsional)">{{ old('description', $book->description) }}</textarea>
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
                    {{ $book->exists ? 'Simpan Perubahan' : 'Simpan' }}
                </button>
                <a href="{{ route('books.index') }}" class="btn btn-secondary">Batal</a>
            </div>
        </form>
    </div>

    @push('scripts')
    <script>
        document.getElementById('classification_id').addEventListener('change', function() {
            const classificationId = this.value;
            const subClassificationSelect = document.getElementById('sub_classification_id');
            
            // Clear current options
            subClassificationSelect.innerHTML = '<option value="">Pilih Sub Klasifikasi</option>';
            
            if (classificationId) {
                fetch(`/books/sub-classifications/${classificationId}`)
                    .then(response => response.json())
                    .then(data => {
                        data.forEach(subClassification => {
                            const option = document.createElement('option');
                            option.value = subClassification.id;
                            option.textContent = `${subClassification.sub_ddc_code} - ${subClassification.name}`;
                            subClassificationSelect.appendChild(option);
                        });
                    });
            }
        });
    </script>
    @endpush
</x-app-layout>
