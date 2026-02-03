<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2 text-sm text-slate-500">
            <a href="{{ route('books.index') }}" class="hover:text-indigo-600 transition-colors">Katalog</a>
            <span>/</span>
            <span class="text-slate-800 font-medium">{{ $book->exists ? 'Edit Buku' : 'Tambah Buku Baru' }}</span>
        </div>
    </x-slot>

    <div class="max-w-7xl mx-auto pb-12">
        <form action="{{ $book->exists ? route('books.update', $book) : route('books.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @if($book->exists)
                @method('PUT')
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
                
                <!-- Left Column: Cover Image (Sticky) -->
                <div class="lg:col-span-4 xl:col-span-3 lg:sticky lg:top-8 space-y-6">
                    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
                        <label class="block text-sm font-semibold text-slate-900 mb-4">Cover Buku</label>
                        
                        <div x-data="{ photoName: null, photoPreview: null }" class="space-y-4">
                            <!-- Current Profile Photo -->
                            <div class="relative aspect-[2/3] w-full rounded-xl overflow-hidden bg-slate-100 border-2 border-dashed border-slate-300 hover:border-indigo-400 transition-colors group">
                                <!-- Image Preview -->
                                <div class="w-full h-full" x-show="!photoPreview">
                                    @if($book->exists && $book->cover_image)
                                        <img src="{{ Storage::url($book->cover_image) }}" alt="Cover" class="w-full h-full object-cover">
                                    @else
                                        <div class="w-full h-full flex flex-col items-center justify-center text-slate-400">
                                            <svg class="w-12 h-12 mb-2 group-hover:text-indigo-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                            </svg>
                                            <span class="text-xs font-medium">Upload Image</span>
                                        </div>
                                    @endif
                                </div>

                                <!-- New Photo Preview -->
                                <div class="w-full h-full bg-cover bg-no-repeat bg-center" x-show="photoPreview" style="display: none;"
                                     :style="'background-image: url(\'' + photoPreview + '\');'">
                                </div>

                                <input type="file" name="cover_image" id="cover_image" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer"
                                       accept="image/*"
                                       @change="
                                           const file = $event.target.files[0];
                                           if (file) {
                                               photoName = file.name;
                                               const reader = new FileReader();
                                               reader.onload = (e) => { photoPreview = e.target.result; };
                                               reader.readAsDataURL(file);
                                           }
                                       ">
                            </div>
                            
                            <p class="text-xs text-slate-500 text-center">
                                Format: JPG, PNG. Max: 2MB.<br>
                                Klik area gambar untuk mengganti.
                            </p>
                            @error('cover_image')
                                <p class="text-xs text-rose-600 text-center">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex flex-col gap-3">
                        <button type="submit" class="btn btn-primary justify-center py-3 shadow-lg shadow-indigo-200">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            {{ $book->exists ? 'Simpan Perubahan' : 'Simpan Buku Baru' }}
                        </button>
                        <a href="{{ route('books.index') }}" class="btn btn-secondary justify-center">Batal & Kembali</a>
                    </div>
                </div>

                <!-- Right Column: Form Fields -->
                <div class="lg:col-span-8 xl:col-span-9 space-y-8">
                    
                    <!-- Section 1: Informasi Utama -->
                    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-8">
                        <h3 class="text-lg font-semibold text-slate-900 mb-6 flex items-center gap-2">
                            <span class="w-8 h-8 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                            </span>
                            Informasi Utama
                        </h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="md:col-span-2">
                                <label for="title" class="form-label">Judul Buku <span class="text-rose-500">*</span></label>
                                <input type="text" name="title" id="title" class="form-input text-lg font-medium" 
                                       value="{{ old('title', $book->title) }}" placeholder="Judul lengkap buku" required>
                                @error('title') <p class="form-error">{{ $message }}</p> @enderror
                            </div>

                            <div class="md:col-span-2">
                                <label for="author" class="form-label">Pengarang <span class="text-rose-500">*</span></label>
                                <input type="text" name="author" id="author" class="form-input" 
                                       value="{{ old('author', $book->author) }}" placeholder="Nama pengarang" required>
                                @error('author') <p class="form-error">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label for="code" class="form-label">Kode Buku <span class="text-rose-500">*</span></label>
                                <input type="text" name="code" id="code" class="form-input font-mono" 
                                       value="{{ old('code', $book->code) }}" placeholder="Ex: BK001" required>
                                @error('code') <p class="form-error">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label for="isbn" class="form-label">ISBN</label>
                                <input type="text" name="isbn" id="isbn" class="form-input font-mono" 
                                       value="{{ old('isbn', $book->isbn) }}" placeholder="Nomor ISBN">
                                @error('isbn') <p class="form-error">{{ $message }}</p> @enderror
                            </div>

                            <div class="md:col-span-2">
                                <label class="form-label">Deskripsi</label>
                                <textarea name="description" rows="3" class="form-input" 
                                          placeholder="Sinopsis singkat...">{{ old('description', $book->description) }}</textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Section 2: Klasifikasi & Lokasi -->
                    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-8">
                        <h3 class="text-lg font-semibold text-slate-900 mb-6 flex items-center gap-2">
                            <span class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                            </span>
                            Klasifikasi & Lokasi
                        </h3>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="category_id" class="form-label">Kategori <span class="text-rose-500">*</span></label>
                                <select name="category_id" id="category_id" class="form-select" required>
                                    <option value="">Pilih Kategori</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" {{ old('category_id', $book->category_id) == $category->id ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('category_id') <p class="form-error">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label for="shelf_location" class="form-label">Lokasi Rak <span class="text-rose-500">*</span></label>
                                <input type="text" name="shelf_location" id="shelf_location" class="form-input" 
                                       value="{{ old('shelf_location', $book->shelf_location) }}" placeholder="Ex: RAK-A-01" required>
                                @error('shelf_location') <p class="form-error">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label for="classification_id" class="form-label">Klasifikasi DDC <span class="text-rose-500">*</span></label>
                                <select name="classification_id" id="classification_id" class="form-select" required>
                                    <option value="">Pilih DDC</option>
                                    @foreach($classifications as $classification)
                                        <option value="{{ $classification->id }}" {{ old('classification_id', $book->classification_id) == $classification->id ? 'selected' : '' }}>
                                            {{ $classification->ddc_code }} - {{ $classification->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('classification_id') <p class="form-error">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label for="sub_classification_id" class="form-label">Sub Klasifikasi</label>
                                <select name="sub_classification_id" id="sub_classification_id" class="form-select">
                                    <option value="">Pilih Sub DDC</option>
                                    @foreach($subClassifications as $sub)
                                        <option value="{{ $sub->id }}" {{ old('sub_classification_id', $book->sub_classification_id) == $sub->id ? 'selected' : '' }}>
                                            {{ $sub->sub_ddc_code }} - {{ $sub->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Section 3: Penerbitan & Fisik -->
                    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-8">
                        <h3 class="text-lg font-semibold text-slate-900 mb-6 flex items-center gap-2">
                            <span class="w-8 h-8 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                            </span>
                            Detail Penerbitan
                        </h3>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div class="md:col-span-3">
                                <label for="publisher_id" class="form-label">Penerbit <span class="text-rose-500">*</span></label>
                                <select name="publisher_id" id="publisher_id" class="form-select" required>
                                    <option value="">Pilih Penerbit</option>
                                    @foreach($publishers as $publisher)
                                        <option value="{{ $publisher->id }}" {{ old('publisher_id', $book->publisher_id) == $publisher->id ? 'selected' : '' }}>
                                            {{ $publisher->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label for="publish_year" class="form-label">Tahun Terbit <span class="text-rose-500">*</span></label>
                                <input type="number" name="publish_year" id="publish_year" class="form-input" 
                                       value="{{ old('publish_year', $book->publish_year) }}" min="1900" max="{{ date('Y') + 1 }}" required>
                            </div>

                            <div>
                                <label for="publish_place" class="form-label">Tempat Terbit <span class="text-rose-500">*</span></label>
                                <input type="text" name="publish_place" id="publish_place" class="form-input" 
                                       value="{{ old('publish_place', $book->publish_place) }}" required>
                            </div>

                            <div>
                                <label for="page_count" class="form-label">Jml Halaman <span class="text-rose-500">*</span></label>
                                <input type="number" name="page_count" id="page_count" class="form-input" 
                                       value="{{ old('page_count', $book->page_count) }}" min="1" required>
                            </div>
                        </div>
                    </div>

                    <!-- Section 4: Inventaris & Stok -->
                    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-8">
                        <h3 class="text-lg font-semibold text-slate-900 mb-6 flex items-center gap-2">
                            <span class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
                            </span>
                            Inventaris
                        </h3>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="stock" class="form-label">Total Stok <span class="text-rose-500">*</span></label>
                                <input type="number" name="stock" id="stock" class="form-input" 
                                       value="{{ old('stock', $book->stock ?? 1) }}" min="1" required>
                            </div>

                            <div>
                                <label for="price" class="form-label">Harga (Rp)</label>
                                <input type="number" name="price" id="price" class="form-input" 
                                       value="{{ old('price', $book->price) }}" step="0.01">
                            </div>

                            <div>
                                <label for="book_source_id" class="form-label">Sumber Buku <span class="text-rose-500">*</span></label>
                                <select name="book_source_id" id="book_source_id" class="form-select" required>
                                    <option value="">Pilih Sumber</option>
                                    @foreach($bookSources as $source)
                                        <option value="{{ $source->id }}" {{ old('book_source_id', $book->book_source_id) == $source->id ? 'selected' : '' }}>
                                            {{ $source->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label for="entry_date" class="form-label">Tanggal Masuk <span class="text-rose-500">*</span></label>
                                <input type="date" name="entry_date" id="entry_date" class="form-input" 
                                       value="{{ old('entry_date', $book->entry_date?->format('Y-m-d') ?? now()->format('Y-m-d')) }}" required>
                            </div>

                            <div class="md:col-span-2 pt-4 border-t border-slate-100">
                                <label class="flex items-start gap-3 p-4 rounded-xl border border-slate-200 bg-slate-50 cursor-pointer hover:bg-slate-100 transition-colors">
                                    <input type="checkbox" name="is_textbook" value="1" 
                                           class="mt-1 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500"
                                           {{ old('is_textbook', $book->is_textbook ?? false) ? 'checked' : '' }}>
                                    <div>
                                        <span class="block font-medium text-slate-900">Buku Paket Pelajaran</span>
                                        <span class="block text-sm text-slate-500">Centang jika buku ini adalah buku paket pelajaran (tidak dihitung dalam batas peminjaman siswa).</span>
                                    </div>
                                </label>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </form>
    </div>

    @push('scripts')
    <script>
        document.getElementById('classification_id').addEventListener('change', function() {
            const classificationId = this.value;
            const subClassificationSelect = document.getElementById('sub_classification_id');
            
            subClassificationSelect.innerHTML = '<option value="">Pilih Sub Klasifikasi</option>';
            
            if (classificationId) {
                fetch(`/books/sub-classifications/${classificationId}`)
                    .then(response => response.json())
                    .then(data => {
                        data.forEach(sub => {
                            const option = document.createElement('option');
                            option.value = sub.id;
                            option.textContent = `${sub.sub_ddc_code} - ${sub.name}`;
                            subClassificationSelect.appendChild(option);
                        });
                    });
            }
        });
    </script>
    @endpush
</x-app-layout>
