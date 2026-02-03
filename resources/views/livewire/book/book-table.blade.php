<div x-data="{ view: localStorage.getItem('book_view') || 'grid' }" 
     x-init="$watch('view', value => localStorage.setItem('book_view', value))"
     class="space-y-6">
    
    <!-- Filters & Toolbar -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-5">
        <div class="flex flex-col lg:flex-row gap-4 justify-between items-start lg:items-center mb-6">
            <!-- Search -->
            <div class="relative w-full lg:w-96">
                <input type="text" 
                       wire:model.live.debounce.300ms="search" 
                       placeholder="Cari buku, pengarang, ISBN..."
                       class="form-input pl-11 bg-slate-50 border-transparent focus:bg-white transition-colors">
                <svg class="w-5 h-5 text-slate-400 absolute left-3.5 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </div>

            <!-- View Toggles & Per Page -->
            <div class="flex items-center gap-3 w-full lg:w-auto justify-end">
                <div class="flex bg-slate-100 rounded-lg p-1 border border-slate-200/60">
                    <button @click="view = 'grid'" 
                            :class="{ 'bg-white text-indigo-600 shadow-sm': view === 'grid', 'text-slate-500 hover:text-slate-700': view !== 'grid' }"
                            class="p-2 rounded-md transition-all duration-200" title="Grid View">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                        </svg>
                    </button>
                    <button @click="view = 'list'" 
                            :class="{ 'bg-white text-indigo-600 shadow-sm': view === 'list', 'text-slate-500 hover:text-slate-700': view !== 'list' }"
                            class="p-2 rounded-md transition-all duration-200" title="List View">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                    </button>
                </div>
                
                <select wire:model.live="perPage" class="form-select w-20 py-2 text-sm bg-slate-50 border-slate-200">
                    <option value="12">12</option>
                    <option value="24">24</option>
                    <option value="48">48</option>
                </select>
            </div>
        </div>

        <!-- Filters -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 pt-4 border-t border-slate-100">
            <select wire:model.live="filterClassification" class="form-select text-sm py-2">
                <option value="">Semua Klasifikasi</option>
                @foreach($classifications as $classification)
                    <option value="{{ $classification->id }}">{{ $classification->ddc_code }} - {{ $classification->name }}</option>
                @endforeach
            </select>

            <select wire:model.live="filterCategory" class="form-select text-sm py-2">
                <option value="">Semua Kategori</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                @endforeach
            </select>

            <select wire:model.live="filterTextbook" class="form-select text-sm py-2">
                <option value="">Semua Jenis</option>
                <option value="1">Buku Paket</option>
                <option value="0">Buku Umum</option>
            </select>

            @if($search || $filterClassification || $filterCategory || $filterTextbook !== '')
                <button wire:click="resetFilters" class="btn btn-secondary text-sm py-2 w-full md:w-auto justify-center">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                    Reset Filter
                </button>
            @endif
        </div>
    </div>

    <!-- Content Area -->
    <div class="relative min-h-[400px]">
        <div wire:loading.flex class="absolute inset-0 z-10 bg-white/50 backdrop-blur-sm flex items-center justify-center rounded-xl">
            <div class="flex items-center gap-2 text-indigo-600 font-medium">
                <svg class="animate-spin h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Memuat...
            </div>
        </div>

        <!-- GRID VIEW -->
        <div x-show="view === 'grid'" class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-6">
            @forelse($books as $book)
                <div class="group bg-white rounded-xl shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300 border border-slate-200 overflow-hidden flex flex-col h-full">
                    <div class="relative aspect-[2/3] overflow-hidden bg-slate-100">
                        @if($book->cover_url)
                            <img src="{{ $book->cover_url }}" alt="{{ $book->title }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                        @else
                            <div class="w-full h-full flex flex-col items-center justify-center text-slate-300 p-4">
                                <svg class="w-12 h-12 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                </svg>
                                <span class="text-xs font-medium text-center text-slate-400">No Cover</span>
                            </div>
                        @endif
                        
                        <!-- Badges -->
                        <div class="absolute top-2 right-2 flex flex-col gap-1 items-end">
                            @if($book->is_textbook)
                                <span class="badge bg-indigo-500/90 text-white border-0 backdrop-blur-sm shadow-sm">Paket</span>
                            @endif
                        </div>

                        <!-- Quick Actions Overlay -->
                        <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity duration-200 flex items-center justify-center gap-2 backdrop-blur-[1px]">
                            <a href="{{ route('books.show', $book) }}" class="p-2 bg-white text-slate-900 rounded-full hover:scale-110 transition-transform shadow-lg" title="Lihat Detail">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                            </a>
                            <a href="{{ route('books.edit', $book) }}" class="p-2 bg-white text-emerald-600 rounded-full hover:scale-110 transition-transform shadow-lg" title="Edit">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                                </svg>
                            </a>
                        </div>
                    </div>
                    
                    <div class="p-4 flex flex-col flex-1">
                        <div class="flex items-start justify-between gap-2 mb-2">
                            <span class="text-xs font-mono font-medium text-slate-500 bg-slate-100 px-2 py-0.5 rounded">{{ $book->code }}</span>
                            @if($book->available_copies_count > 0)
                                <span class="text-xs font-bold text-emerald-600 flex items-center gap-1">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                    {{ $book->available_copies_count }}
                                </span>
                            @else
                                <span class="text-xs font-bold text-rose-600 flex items-center gap-1">
                                    <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                                    Habis
                                </span>
                            @endif
                        </div>
                        
                        <h3 class="font-bold text-slate-800 leading-snug mb-1 line-clamp-2" title="{{ $book->title }}">
                            <a href="{{ route('books.show', $book) }}" class="hover:text-indigo-600 transition-colors">
                                {{ $book->title }}
                            </a>
                        </h3>
                        
                        <p class="text-sm text-slate-500 mb-3 line-clamp-1">{{ $book->author }}</p>
                        
                        <div class="mt-auto pt-3 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
                            <span class="truncate max-w-[60%]">{{ $book->category->name ?? '-' }}</span>
                            <span class="font-medium">{{ $book->publish_year }}</span>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full flex flex-col items-center justify-center py-12 text-slate-500">
                    <div class="bg-slate-50 p-4 rounded-full mb-3">
                        <svg class="w-8 h-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                        </svg>
                    </div>
                    <p class="text-lg font-medium text-slate-900">Tidak ada buku ditemukan</p>
                    <p class="text-sm">Coba sesuaikan filter atau kata kunci pencarian Anda.</p>
                </div>
            @endforelse
        </div>

        <!-- LIST VIEW -->
        <div x-show="view === 'list'" class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden" style="display: none;">
            <div class="overflow-x-auto">
                <table class="data-table">
                    <thead class="bg-slate-50 border-b border-slate-200">
                        <tr>
                            <th class="w-16 px-6 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider">Cover</th>
                            <th wire:click="sortBy('code')" class="px-6 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider cursor-pointer hover:bg-slate-100 transition-colors group">
                                <div class="flex items-center gap-1">
                                    Kode
                                    @if($sortField === 'code')
                                        <svg class="w-3 h-3 {{ $sortDirection === 'asc' ? '' : 'rotate-180' }} text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                    @else
                                        <svg class="w-3 h-3 text-slate-300 opacity-0 group-hover:opacity-100" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                    @endif
                                </div>
                            </th>
                            <th wire:click="sortBy('title')" class="px-6 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider cursor-pointer hover:bg-slate-100 transition-colors group">
                                <div class="flex items-center gap-1">
                                    Judul
                                    @if($sortField === 'title')
                                        <svg class="w-3 h-3 {{ $sortDirection === 'asc' ? '' : 'rotate-180' }} text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                    @else
                                        <svg class="w-3 h-3 text-slate-300 opacity-0 group-hover:opacity-100" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                    @endif
                                </div>
                            </th>
                            <th wire:click="sortBy('author')" class="px-6 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider cursor-pointer hover:bg-slate-100 transition-colors group">
                                <div class="flex items-center gap-1">
                                    Pengarang
                                    @if($sortField === 'author')
                                        <svg class="w-3 h-3 {{ $sortDirection === 'asc' ? '' : 'rotate-180' }} text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                    @else
                                        <svg class="w-3 h-3 text-slate-300 opacity-0 group-hover:opacity-100" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                    @endif
                                </div>
                            </th>
                            <th class="px-6 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider">Kategori</th>
                            <th class="px-6 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider text-center">Stok</th>
                            <th class="px-6 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($books as $book)
                            <tr class="hover:bg-slate-50/80 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="w-10 h-14 bg-slate-100 rounded overflow-hidden shadow-sm border border-slate-200">
                                        @if($book->cover_url)
                                            <img src="{{ $book->cover_url }}" alt="Cover" class="w-full h-full object-cover">
                                        @else
                                            <div class="w-full h-full flex items-center justify-center text-slate-300">
                                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                            </div>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4 font-mono text-sm text-slate-600">{{ $book->code }}</td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-2">
                                        <span class="font-medium text-slate-800">{{ $book->title }}</span>
                                        @if($book->is_textbook)
                                            <span class="badge badge-primary text-[10px] px-1.5">Paket</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-slate-600">{{ $book->author }}</td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center px-2 py-1 rounded-md bg-slate-100 text-xs font-medium text-slate-600">
                                        {{ $book->category->name ?? '-' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <div class="flex flex-col items-center gap-1">
                                        <span class="text-sm font-semibold {{ $book->available_copies_count > 0 ? 'text-emerald-600' : 'text-rose-600' }}">
                                            {{ $book->available_copies_count }} / {{ $book->stock }}
                                        </span>
                                        <span class="text-[10px] text-slate-400">Tersedia</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="{{ route('books.show', $book) }}" class="p-1.5 text-slate-500 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition-colors" title="Lihat">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                        </a>
                                        <a href="{{ route('books.edit', $book) }}" class="p-1.5 text-slate-500 hover:text-emerald-600 hover:bg-emerald-50 rounded-lg transition-colors" title="Edit">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                        </a>
                                        <form action="{{ route('books.destroy', $book) }}" method="POST" onsubmit="return confirm('Yakin hapus buku ini?')" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-1.5 text-slate-500 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition-colors" title="Hapus">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center text-slate-500">
                                    Tidak ada data buku
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Pagination -->
    <div class="mt-6">
        {{ $books->links() }}
    </div>
</div>
