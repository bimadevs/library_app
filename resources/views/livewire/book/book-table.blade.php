<div>
    <!-- Search and Filters -->
    <div class="mb-4 space-y-4">
        <div class="flex flex-wrap items-center gap-4">
            <div class="relative flex-1 min-w-[200px]">
                <input type="text" 
                       wire:model.live.debounce.300ms="search" 
                       placeholder="Cari kode, judul, pengarang, atau ISBN..."
                       class="form-input pl-10 w-full">
                <svg class="w-5 h-5 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </div>
            <div class="flex items-center gap-2">
                <label class="text-sm text-slate-600">Tampilkan:</label>
                <select wire:model.live="perPage" class="form-select w-20">
                    <option value="10">10</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                </select>
            </div>
        </div>

        <div class="flex flex-wrap items-center gap-3">
            <select wire:model.live="filterClassification" class="form-select">
                <option value="">Semua Klasifikasi</option>
                @foreach($classifications as $classification)
                    <option value="{{ $classification->id }}">{{ $classification->ddc_code }} - {{ $classification->name }}</option>
                @endforeach
            </select>

            <select wire:model.live="filterCategory" class="form-select">
                <option value="">Semua Kategori</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                @endforeach
            </select>

            <select wire:model.live="filterTextbook" class="form-select">
                <option value="">Semua Buku</option>
                <option value="1">Buku Paket</option>
                <option value="0">Buku Umum</option>
            </select>

            @if($search || $filterClassification || $filterCategory || $filterTextbook !== '')
                <button wire:click="resetFilters" class="btn btn-secondary text-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                    Reset Filter
                </button>
            @endif
        </div>
    </div>

    <!-- Table -->
    <div class="overflow-x-auto">
        <table class="data-table">
            <thead>
                <tr>
                    <th class="w-16">No</th>
                    <th class="w-16">Cover</th>
                    <th wire:click="sortBy('code')" class="cursor-pointer hover:bg-slate-100">
                        <div class="flex items-center gap-1">
                            Kode
                            @if($sortField === 'code')
                                <svg class="w-4 h-4 {{ $sortDirection === 'asc' ? '' : 'rotate-180' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/>
                                </svg>
                            @endif
                        </div>
                    </th>
                    <th wire:click="sortBy('title')" class="cursor-pointer hover:bg-slate-100">
                        <div class="flex items-center gap-1">
                            Judul
                            @if($sortField === 'title')
                                <svg class="w-4 h-4 {{ $sortDirection === 'asc' ? '' : 'rotate-180' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/>
                                </svg>
                            @endif
                        </div>
                    </th>
                    <th wire:click="sortBy('author')" class="cursor-pointer hover:bg-slate-100">
                        <div class="flex items-center gap-1">
                            Pengarang
                            @if($sortField === 'author')
                                <svg class="w-4 h-4 {{ $sortDirection === 'asc' ? '' : 'rotate-180' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/>
                                </svg>
                            @endif
                        </div>
                    </th>
                    <th>Klasifikasi</th>
                    <th>Kategori</th>
                    <th class="text-center">Stok</th>
                    <th class="text-center">Tersedia</th>
                    <th class="w-32 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($books as $index => $book)
                    <tr>
                        <td class="text-slate-500">{{ $books->firstItem() + $index }}</td>
                        <td class="px-2 py-2">
                            <div class="w-10 h-14 bg-slate-100 rounded overflow-hidden shadow-sm border border-slate-200">
                                <img src="{{ $book->cover_url }}" alt="Cover" class="w-full h-full object-cover">
                            </div>
                        </td>
                        <td class="font-mono text-sm">{{ $book->code }}</td>
                        <td class="font-medium text-slate-800">
                            <div class="flex items-center gap-2">
                                <span class="max-w-xs truncate" title="{{ $book->title }}">{{ $book->title }}</span>
                                @if($book->is_textbook)
                                    <span class="badge badge-primary text-xs">Paket</span>
                                @endif
                            </div>
                        </td>
                        <td>{{ $book->author }}</td>
                        <td>
                            <span class="text-xs text-slate-500">{{ $book->classification->ddc_code ?? '-' }}</span>
                            {{ $book->classification->name ?? '-' }}
                        </td>
                        <td>{{ $book->category->name ?? '-' }}</td>
                        <td class="text-center">
                            <span class="badge badge-info">{{ $book->stock }}</span>
                        </td>
                        <td class="text-center">
                            @if($book->available_copies_count > 0)
                                <span class="badge badge-success">{{ $book->available_copies_count }}</span>
                            @else
                                <span class="badge badge-warning">0</span>
                            @endif
                        </td>
                        <td>
                            <div class="flex items-center justify-center gap-1">
                                <a href="{{ route('books.show', $book) }}" 
                                   class="p-2 text-slate-600 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors"
                                   title="Detail">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                </a>
                                <a href="{{ route('books.edit', $book) }}" 
                                   class="p-2 text-slate-600 hover:text-emerald-600 hover:bg-emerald-50 rounded-lg transition-colors"
                                   title="Edit">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </a>
                                <form action="{{ route('books.destroy', $book) }}" 
                                      method="POST" 
                                      onsubmit="return confirm('Apakah Anda yakin ingin menghapus data buku ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            class="p-2 text-slate-600 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors"
                                            title="Hapus">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="10" class="text-center py-8 text-slate-500">
                            <svg class="w-12 h-12 mx-auto text-slate-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                            </svg>
                            @if($search || $filterClassification || $filterCategory || $filterTextbook !== '')
                                Tidak ada buku yang cocok dengan filter
                            @else
                                Belum ada data buku
                            @endif
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    @if($books->hasPages())
        <div class="mt-4 pt-4 border-t border-slate-200">
            {{ $books->links() }}
        </div>
    @endif
</div>
