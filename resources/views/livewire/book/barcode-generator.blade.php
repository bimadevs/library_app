<div>
    <!-- Flash Messages -->
    @if (session()->has('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-lg mb-4 flex items-start gap-3">
            <svg class="w-5 h-5 text-emerald-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-4 flex items-start gap-3">
            <svg class="w-5 h-5 text-red-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    @if($showPrintView)
        <!-- Print View -->
        <div class="space-y-4">
            <div class="flex items-center justify-between">
                <h4 class="font-medium text-slate-800">Preview Cetak Barcode</h4>
                <div class="flex items-center gap-2">
                    <button onclick="window.print()" class="btn btn-primary">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                        </svg>
                        Cetak
                    </button>
                    <button wire:click="closePrint" class="btn btn-secondary">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                        Tutup
                    </button>
                </div>
            </div>

            <!-- Printable Area -->
            <div id="printable-area" class="bg-white p-4">
                <div class="grid grid-cols-3 gap-4">
                    @foreach($printData as $data)
                        <div class="border border-slate-200 rounded p-3 text-center">
                            <img src="data:image/svg+xml;base64,{{ $data['image'] }}" 
                                 alt="{{ $data['barcode'] }}" 
                                 class="mx-auto mb-2">
                            <p class="font-mono text-sm font-medium">{{ $data['barcode'] }}</p>
                            <p class="text-xs text-slate-600 truncate" title="{{ $data['title'] }}">{{ Str::limit($data['title'], 30) }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <style>
            @media print {
                body * {
                    visibility: hidden;
                }
                #printable-area, #printable-area * {
                    visibility: visible;
                }
                #printable-area {
                    position: absolute;
                    left: 0;
                    top: 0;
                    width: 100%;
                }
                .btn, button {
                    display: none !important;
                }
            }
        </style>
    @else
        <!-- Main View -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Book Selection -->
            <div class="space-y-4">
                <h4 class="font-medium text-slate-800">Pilih Buku</h4>
                
                <!-- Search -->
                <div class="relative">
                    <input type="text" 
                           wire:model.live.debounce.300ms="search" 
                           placeholder="Cari kode atau judul buku..."
                           class="form-input pl-10 w-full">
                    <svg class="w-5 h-5 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>

                <!-- Book List -->
                <div class="border border-slate-200 rounded-lg overflow-hidden">
                    <div class="max-h-96 overflow-y-auto">
                        <table class="w-full text-sm">
                            <thead class="bg-slate-50 sticky top-0">
                                <tr>
                                    <th class="px-4 py-2 text-left">Kode</th>
                                    <th class="px-4 py-2 text-left">Judul</th>
                                    <th class="px-4 py-2 text-center">Stok</th>
                                    <th class="px-4 py-2 text-center">Barcode</th>
                                    <th class="px-4 py-2"></th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @forelse($books as $book)
                                    <tr class="hover:bg-slate-50 cursor-pointer {{ $selectedBookId === $book->id ? 'bg-emerald-50' : '' }}"
                                        wire:click="selectBook({{ $book->id }})">
                                        <td class="px-4 py-2 font-mono text-xs">{{ $book->code }}</td>
                                        <td class="px-4 py-2">
                                            <div class="max-w-xs truncate" title="{{ $book->title }}">{{ $book->title }}</div>
                                        </td>
                                        <td class="px-4 py-2 text-center">
                                            <span class="badge badge-info">{{ $book->stock }}</span>
                                        </td>
                                        <td class="px-4 py-2 text-center">
                                            <span class="badge {{ $book->copies_count >= $book->stock ? 'badge-success' : 'badge-warning' }}">
                                                {{ $book->copies_count }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-2">
                                            @if($selectedBookId === $book->id)
                                                <svg class="w-5 h-5 text-emerald-600" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                                </svg>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-4 py-8 text-center text-slate-500">
                                            @if($search)
                                                Tidak ada buku yang cocok dengan pencarian
                                            @else
                                                Belum ada data buku
                                            @endif
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                @if($books->hasPages())
                    <div class="mt-2">
                        {{ $books->links() }}
                    </div>
                @endif
            </div>

            <!-- Barcode Generation -->
            <div class="space-y-4">
                @if($selectedBook)
                    <div class="bg-slate-50 rounded-lg p-4">
                        <h4 class="font-medium text-slate-800 mb-3">{{ $selectedBook->title }}</h4>
                        <div class="grid grid-cols-3 gap-4 text-sm">
                            <div>
                                <p class="text-slate-500">Kode Buku</p>
                                <p class="font-mono font-medium">{{ $selectedBook->code }}</p>
                            </div>
                            <div>
                                <p class="text-slate-500">Total Stok</p>
                                <p class="font-medium">{{ $selectedBook->stock }}</p>
                            </div>
                            <div>
                                <p class="text-slate-500">Barcode Dibuat</p>
                                <p class="font-medium">{{ $selectedBook->copies->count() }}</p>
                            </div>
                        </div>
                    </div>

                    @php
                        $availableSlots = $selectedBook->stock - $selectedBook->copies->count();
                    @endphp

                    @if($availableSlots > 0)
                        <div class="bg-white border border-slate-200 rounded-lg p-4">
                            <h5 class="font-medium text-slate-700 mb-3">Generate Barcode Baru</h5>
                            <div class="flex items-end gap-3">
                                <div class="flex-1">
                                    <label class="form-label">Jumlah Barcode</label>
                                    <input type="number" 
                                           wire:model="quantity" 
                                           min="1" 
                                           max="{{ $availableSlots }}"
                                           class="form-input">
                                    <p class="text-xs text-slate-500 mt-1">Maksimal {{ $availableSlots }} barcode</p>
                                </div>
                                <button wire:click="generateBarcodes" class="btn btn-primary">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>
                                    </svg>
                                    Generate
                                </button>
                            </div>
                        </div>
                    @else
                        <div class="bg-emerald-50 border border-emerald-200 rounded-lg p-4 text-center">
                            <svg class="w-8 h-8 mx-auto text-emerald-600 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <p class="text-emerald-700">Semua barcode sudah di-generate untuk buku ini.</p>
                        </div>
                    @endif

                    <!-- Generated Barcodes Preview -->
                    @if(count($generatedBarcodes) > 0)
                        <div class="bg-emerald-50 border border-emerald-200 rounded-lg p-4">
                            <h5 class="font-medium text-emerald-800 mb-3">Barcode Baru Dibuat</h5>
                            <div class="grid grid-cols-2 gap-3">
                                @foreach($generatedBarcodes as $data)
                                    <div class="bg-white rounded p-3 text-center">
                                        <img src="data:image/png;base64,{{ $data['image'] }}" 
                                             alt="{{ $data['barcode'] }}" 
                                             class="mx-auto mb-2">
                                        <p class="font-mono text-sm">{{ $data['barcode'] }}</p>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Existing Barcodes -->
                    @if($selectedBook->copies->count() > 0)
                        <div class="border border-slate-200 rounded-lg overflow-hidden">
                            <div class="bg-slate-50 px-4 py-3 border-b border-slate-200 flex items-center justify-between">
                                <h5 class="font-medium text-slate-700">Daftar Barcode ({{ $selectedBook->copies->count() }})</h5>
                                <div class="flex items-center gap-2">
                                    <button wire:click="selectAllForPrint" class="text-sm text-emerald-600 hover:text-emerald-700">
                                        Pilih Semua
                                    </button>
                                    @if(count($selectedForPrint) > 0)
                                        <span class="text-slate-300">|</span>
                                        <button wire:click="clearPrintSelection" class="text-sm text-slate-600 hover:text-slate-700">
                                            Batal Pilih
                                        </button>
                                    @endif
                                </div>
                            </div>
                            <div class="max-h-64 overflow-y-auto">
                                <table class="w-full text-sm">
                                    <thead class="bg-slate-50 sticky top-0">
                                        <tr>
                                            <th class="px-4 py-2 w-10"></th>
                                            <th class="px-4 py-2 text-left">Barcode</th>
                                            <th class="px-4 py-2 text-center">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-100">
                                        @foreach($selectedBook->copies as $copy)
                                            <tr class="hover:bg-slate-50">
                                                <td class="px-4 py-2">
                                                    <input type="checkbox" 
                                                           wire:click="togglePrintSelection('{{ $copy->barcode }}')"
                                                           {{ in_array($copy->barcode, $selectedForPrint) ? 'checked' : '' }}
                                                           class="rounded border-slate-300 text-emerald-600 focus:ring-emerald-500">
                                                </td>
                                                <td class="px-4 py-2 font-mono">{{ $copy->barcode }}</td>
                                                <td class="px-4 py-2 text-center">
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
                        </div>

                        @if(count($selectedForPrint) > 0)
                            <button wire:click="showPrint" class="btn btn-primary w-full">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                                </svg>
                                Cetak {{ count($selectedForPrint) }} Barcode
                            </button>
                        @endif
                    @endif
                @else
                    <div class="bg-slate-50 rounded-lg p-8 text-center">
                        <svg class="w-12 h-12 mx-auto text-slate-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                        </svg>
                        <p class="text-slate-500">Pilih buku dari daftar di sebelah kiri untuk generate barcode</p>
                    </div>
                @endif
            </div>
        </div>
    @endif
</div>
