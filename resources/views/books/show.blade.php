<x-app-layout>
    <x-slot name="header">
        Detail Buku
    </x-slot>

    <div class="max-w-5xl space-y-6">
        <!-- Book Info Card -->
        <div class="card">
            <div class="flex items-start justify-between mb-6">
                <div>
                    <h3 class="card-header mb-1">{{ $book->title }}</h3>
                    <p class="text-slate-500">{{ $book->author }}</p>
                </div>
                <div class="flex items-center gap-2">
                    <a href="{{ route('books.edit', $book) }}" class="btn btn-secondary">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        Edit
                    </a>
                    <a href="{{ route('books.index') }}" class="btn btn-secondary">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                        Kembali
                    </a>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Left Column -->
                <div class="space-y-4">
                    <div>
                        <label class="text-xs text-slate-500 uppercase tracking-wide">Kode Buku</label>
                        <p class="font-mono text-lg font-semibold text-slate-800">{{ $book->code }}</p>
                    </div>
                    <div>
                        <label class="text-xs text-slate-500 uppercase tracking-wide">ISBN</label>
                        <p class="text-slate-800">{{ $book->isbn ?? '-' }}</p>
                    </div>
                    <div>
                        <label class="text-xs text-slate-500 uppercase tracking-wide">Penerbit</label>
                        <p class="text-slate-800">{{ $book->publisher->name ?? '-' }}</p>
                    </div>
                    <div>
                        <label class="text-xs text-slate-500 uppercase tracking-wide">Tempat & Tahun Terbit</label>
                        <p class="text-slate-800">{{ $book->publish_place }}, {{ $book->publish_year }}</p>
                    </div>
                </div>

                <!-- Middle Column -->
                <div class="space-y-4">
                    <div>
                        <label class="text-xs text-slate-500 uppercase tracking-wide">Klasifikasi DDC</label>
                        <p class="text-slate-800">
                            <span class="font-mono text-sm text-slate-500">{{ $book->classification->ddc_code ?? '' }}</span>
                            {{ $book->classification->name ?? '-' }}
                        </p>
                    </div>
                    <div>
                        <label class="text-xs text-slate-500 uppercase tracking-wide">Sub Klasifikasi</label>
                        <p class="text-slate-800">
                            @if($book->subClassification)
                                <span class="font-mono text-sm text-slate-500">{{ $book->subClassification->sub_ddc_code }}</span>
                                {{ $book->subClassification->name }}
                            @else
                                -
                            @endif
                        </p>
                    </div>
                    <div>
                        <label class="text-xs text-slate-500 uppercase tracking-wide">Kategori</label>
                        <p class="text-slate-800">{{ $book->category->name ?? '-' }}</p>
                    </div>
                    <div>
                        <label class="text-xs text-slate-500 uppercase tracking-wide">Lokasi Rak</label>
                        <p class="text-slate-800 font-mono">{{ $book->shelf_location }}</p>
                    </div>
                </div>

                <!-- Right Column -->
                <div class="space-y-4">
                    <div>
                        <label class="text-xs text-slate-500 uppercase tracking-wide">Jumlah Halaman</label>
                        <p class="text-slate-800">{{ $book->page_count }} halaman</p>
                    </div>
                    <div>
                        <label class="text-xs text-slate-500 uppercase tracking-wide">Ketebalan</label>
                        <p class="text-slate-800">{{ $book->thickness ?? '-' }}</p>
                    </div>
                    <div>
                        <label class="text-xs text-slate-500 uppercase tracking-wide">Sumber</label>
                        <p class="text-slate-800">{{ $book->source }}</p>
                    </div>
                    <div>
                        <label class="text-xs text-slate-500 uppercase tracking-wide">Tanggal Masuk</label>
                        <p class="text-slate-800">{{ $book->entry_date->format('d F Y') }}</p>
                    </div>
                    <div>
                        <label class="text-xs text-slate-500 uppercase tracking-wide">Harga</label>
                        <p class="text-slate-800">{{ $book->price ? 'Rp ' . number_format($book->price, 0, ',', '.') : '-' }}</p>
                    </div>
                </div>
            </div>

            @if($book->description)
                <div class="mt-6 pt-6 border-t border-slate-200">
                    <label class="text-xs text-slate-500 uppercase tracking-wide">Deskripsi</label>
                    <p class="text-slate-800 mt-1">{{ $book->description }}</p>
                </div>
            @endif
        </div>

        <!-- Stock Info Card -->
        <div class="card">
            <h4 class="card-header">Informasi Stok</h4>
            
            <div class="grid grid-cols-3 gap-4 mb-6">
                <div class="bg-slate-50 rounded-lg p-4 text-center">
                    <p class="text-3xl font-bold text-slate-800">{{ $book->stock }}</p>
                    <p class="text-sm text-slate-500">Total Stok</p>
                </div>
                <div class="bg-emerald-50 rounded-lg p-4 text-center">
                    <p class="text-3xl font-bold text-emerald-600">{{ $book->copies->where('status', 'available')->count() }}</p>
                    <p class="text-sm text-slate-500">Tersedia</p>
                </div>
                <div class="bg-amber-50 rounded-lg p-4 text-center">
                    <p class="text-3xl font-bold text-amber-600">{{ $book->copies->where('status', 'borrowed')->count() }}</p>
                    <p class="text-sm text-slate-500">Dipinjam</p>
                </div>
            </div>

            @if($book->copies->count() > 0)
                <h5 class="font-medium text-slate-700 mb-3">Daftar Copy Buku</h5>
                <div class="overflow-x-auto">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Barcode</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($book->copies as $index => $copy)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td class="font-mono">{{ $copy->barcode }}</td>
                                    <td>
                                        @if($copy->status === 'available')
                                            <span class="badge badge-success">Tersedia</span>
                                        @elseif($copy->status === 'borrowed')
                                            <span class="badge badge-warning">Dipinjam</span>
                                        @else
                                            <span class="badge badge-danger">Hilang</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-8 text-slate-500">
                    <svg class="w-12 h-12 mx-auto text-slate-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>
                    </svg>
                    <p>Belum ada barcode yang di-generate untuk buku ini.</p>
                    <a href="{{ route('books.barcode') }}" class="btn btn-primary mt-3">
                        Generate Barcode
                    </a>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
