<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2 text-sm text-slate-500">
            <a href="{{ route('books.index') }}" class="hover:text-indigo-600 transition-colors">Katalog</a>
            <span>/</span>
            <span class="text-slate-800 font-medium truncate">{{ $book->title }}</span>
        </div>
    </x-slot>

    <div class="max-w-7xl mx-auto pb-12">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
            
            <!-- Left Column: Cover & Quick Actions -->
            <div class="lg:col-span-4 xl:col-span-3 space-y-6 lg:sticky lg:top-8">
                <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-2">
                    <div class="relative aspect-[2/3] rounded-xl overflow-hidden bg-slate-100">
                        @if($book->cover_url)
                            <img src="{{ $book->cover_url }}" alt="{{ $book->title }}" class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full flex flex-col items-center justify-center text-slate-300">
                                <svg class="w-16 h-16 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                </svg>
                                <span class="text-sm font-medium">Tidak ada cover</span>
                            </div>
                        @endif
                        
                        @if($book->is_textbook)
                            <div class="absolute top-4 left-4">
                                <span class="px-3 py-1 rounded-full bg-indigo-600/90 text-white text-xs font-semibold backdrop-blur-sm shadow-sm">
                                    Buku Paket
                                </span>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <a href="{{ route('books.edit', $book) }}" class="btn btn-secondary justify-center w-full">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                        </svg>
                        Edit
                    </a>
                    <form action="{{ route('books.print-label') }}" method="POST" target="_blank" class="w-full">
                        @csrf
                        <input type="hidden" name="books[]" value="{{ $book->id }}">
                        <button type="submit" class="btn btn-secondary justify-center w-full">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                            </svg>
                            Label
                        </button>
                    </form>
                </div>
            </div>

            <!-- Right Column: Details -->
            <div class="lg:col-span-8 xl:col-span-9 space-y-8">
                
                <!-- Header Info -->
                <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-8">
                    <div class="flex flex-col md:flex-row gap-6 justify-between items-start">
                        <div class="space-y-4 flex-1">
                            <div>
                                <h1 class="text-3xl font-bold text-slate-900 leading-tight mb-2">{{ $book->title }}</h1>
                                <p class="text-xl text-slate-500 font-medium">{{ $book->author }}</p>
                            </div>
                            
                            <div class="flex flex-wrap gap-3">
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-slate-100 text-slate-600 text-sm font-medium">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                                    {{ $book->category->name ?? 'Tanpa Kategori' }}
                                </span>
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-slate-100 text-slate-600 text-sm font-medium">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                    {{ $book->publish_year }}
                                </span>
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-slate-100 text-slate-600 text-sm font-medium font-mono">
                                    {{ $book->classification->ddc_code ?? '' }}
                                </span>
                            </div>
                        </div>

                        <!-- Main Action -->
                        <div class="flex flex-col items-end gap-4 min-w-[200px]">
                            <div class="text-right">
                                <p class="text-sm text-slate-500 mb-1">Stok Tersedia</p>
                                <div class="flex items-baseline justify-end gap-1">
                                    <span class="text-3xl font-bold {{ $book->available_copies_count > 0 ? 'text-emerald-600' : 'text-rose-600' }}">
                                        {{ $book->available_copies_count }}
                                    </span>
                                    <span class="text-slate-400 font-medium">/ {{ $book->stock }}</span>
                                </div>
                            </div>
                            
                            @if($book->available_copies_count > 0)
                                <a href="{{ route('transactions.loans.create', ['book_id' => $book->id]) }}" class="btn btn-primary w-full shadow-indigo-200 shadow-lg hover:shadow-xl hover:-translate-y-0.5 transition-all">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                    </svg>
                                    Pinjam Buku
                                </a>
                            @else
                                <button disabled class="btn bg-slate-100 text-slate-400 border-slate-200 cursor-not-allowed w-full">
                                    Stok Habis
                                </button>
                            @endif
                        </div>
                    </div>

                    @if($book->description)
                        <div class="mt-8 pt-8 border-t border-slate-100">
                            <h3 class="text-sm font-semibold text-slate-900 uppercase tracking-wider mb-3">Deskripsi</h3>
                            <p class="text-slate-600 leading-relaxed">{{ $book->description }}</p>
                        </div>
                    @endif
                </div>

                <!-- Detailed Specs -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
                        <h3 class="text-lg font-semibold text-slate-900 mb-4">Informasi Pustaka</h3>
                        <dl class="space-y-3">
                            <div class="flex justify-between py-2 border-b border-slate-50">
                                <dt class="text-slate-500 text-sm">Kode Buku</dt>
                                <dd class="font-mono font-medium text-slate-900">{{ $book->code }}</dd>
                            </div>
                            <div class="flex justify-between py-2 border-b border-slate-50">
                                <dt class="text-slate-500 text-sm">ISBN</dt>
                                <dd class="font-mono text-slate-900">{{ $book->isbn ?? '-' }}</dd>
                            </div>
                            <div class="flex justify-between py-2 border-b border-slate-50">
                                <dt class="text-slate-500 text-sm">Klasifikasi</dt>
                                <dd class="text-slate-900 text-right">{{ $book->classification->name ?? '-' }}</dd>
                            </div>
                            <div class="flex justify-between py-2 border-b border-slate-50">
                                <dt class="text-slate-500 text-sm">Sub Klasifikasi</dt>
                                <dd class="text-slate-900 text-right">{{ $book->subClassification->name ?? '-' }}</dd>
                            </div>
                            <div class="flex justify-between py-2 pt-2">
                                <dt class="text-slate-500 text-sm">Lokasi Rak</dt>
                                <dd class="font-medium text-slate-900">{{ $book->shelf_location }}</dd>
                            </div>
                        </dl>
                    </div>

                    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
                        <h3 class="text-lg font-semibold text-slate-900 mb-4">Detail Fisik & Penerbit</h3>
                        <dl class="space-y-3">
                            <div class="flex justify-between py-2 border-b border-slate-50">
                                <dt class="text-slate-500 text-sm">Penerbit</dt>
                                <dd class="text-slate-900 text-right">{{ $book->publisher->name ?? '-' }}</dd>
                            </div>
                            <div class="flex justify-between py-2 border-b border-slate-50">
                                <dt class="text-slate-500 text-sm">Tempat Terbit</dt>
                                <dd class="text-slate-900">{{ $book->publish_place }}</dd>
                            </div>
                            <div class="flex justify-between py-2 border-b border-slate-50">
                                <dt class="text-slate-500 text-sm">Halaman</dt>
                                <dd class="text-slate-900">{{ $book->page_count }} Hal</dd>
                            </div>
                            <div class="flex justify-between py-2 border-b border-slate-50">
                                <dt class="text-slate-500 text-sm">Ketebalan</dt>
                                <dd class="text-slate-900">{{ $book->thickness ?? '-' }}</dd>
                            </div>
                            <div class="flex justify-between py-2 pt-2">
                                <dt class="text-slate-500 text-sm">Harga</dt>
                                <dd class="text-slate-900">{{ $book->price ? 'Rp ' . number_format($book->price, 0, ',', '.') : '-' }}</dd>
                            </div>
                        </dl>
                    </div>
                </div>

                <!-- Stock Table -->
                <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                    <div class="p-6 border-b border-slate-100 flex justify-between items-center">
                        <h3 class="text-lg font-semibold text-slate-900">Salinan Buku</h3>
                        <div class="flex gap-2 text-sm">
                            <span class="px-2 py-1 rounded bg-emerald-50 text-emerald-700 font-medium">Available: {{ $book->copies->where('status', 'available')->count() }}</span>
                            <span class="px-2 py-1 rounded bg-amber-50 text-amber-700 font-medium">Borrowed: {{ $book->copies->where('status', 'borrowed')->count() }}</span>
                        </div>
                    </div>
                    
                    <div class="overflow-x-auto">
                        @if($book->copies->count() > 0)
                            <table class="w-full text-left text-sm text-slate-600">
                                <thead class="bg-slate-50 text-slate-500 uppercase font-medium text-xs">
                                    <tr>
                                        <th class="px-6 py-3">No</th>
                                        <th class="px-6 py-3">Barcode</th>
                                        <th class="px-6 py-3">Status</th>
                                        <th class="px-6 py-3 text-right">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100">
                                    @foreach($book->copies as $index => $copy)
                                        <tr class="hover:bg-slate-50/50">
                                            <td class="px-6 py-3">{{ $index + 1 }}</td>
                                            <td class="px-6 py-3 font-mono text-slate-900">{{ $copy->barcode }}</td>
                                            <td class="px-6 py-3">
                                                @if($copy->status === 'available')
                                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-emerald-50 text-emerald-700 ring-1 ring-emerald-600/10">
                                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                                        Tersedia
                                                    </span>
                                                @elseif($copy->status === 'borrowed')
                                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-amber-50 text-amber-700 ring-1 ring-amber-600/10">
                                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                                        Dipinjam
                                                    </span>
                                                @else
                                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-rose-50 text-rose-700 ring-1 ring-rose-600/10">
                                                        <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                                                        {{ ucfirst($copy->status) }}
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-3 text-right">
                                                <!-- Contextual actions for specific copies could go here -->
                                                <span class="text-slate-400">-</span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @else
                            <div class="p-8 text-center text-slate-500">
                                <p class="mb-2">Belum ada barcode/copy untuk buku ini.</p>
                                <a href="{{ route('books.barcode') }}" class="text-indigo-600 hover:underline font-medium">Generate Barcode</a>
                            </div>
                        @endif
                    </div>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>
