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
                <h4 class="font-medium text-slate-800">Preview Cetak Label</h4>
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
                @php
                    $colCount = 0;
                    $totalCount = 0;
                @endphp

                <div class="label-grid">
                    <div class="label-row">
                        @foreach($printData as $data)
                            <div class="label-cell">
                                <div class="label-content">
                                    <div class="library-name">PERPUSTAKAAN SMK MUDITA SINGKAWANG</div>
                                    <div class="call-number">
                                        <span class="classification">{{ $data['ddc_code'] }}</span>
                                        <span class="author-code">{{ substr($data['author'], 0, 3) }}</span>
                                        <span class="title-code">{{ substr($data['title'], 0, 1) }}</span>
                                    </div>
                                </div>
                            </div>

                            @php
                                $colCount++;
                                $totalCount++;
                            @endphp

                            @if($colCount % 3 == 0)
                                </div><div class="label-row">
                            @endif

                            @if($totalCount % 21 == 0 && !$loop->last)
                                </div></div><div class="page-break"></div><div class="label-grid"><div class="label-row">
                            @endif
                        @endforeach
                        
                        {{-- Fill empty cells to complete the row --}}
                        @while($colCount % 3 != 0)
                            <div class="label-cell"></div>
                            @php $colCount++; @endphp
                        @endwhile
                    </div>
                </div>
            </div>
        </div>

        <style>
            @media print {
                @page {
                    size: A4;
                    margin: 1cm;
                }
                body {
                    background: white;
                    margin: 0;
                    padding: 0;
                    font-family: Arial, sans-serif;
                    font-size: 10pt;
                }
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
                    margin: 0;
                    padding: 0 !important;
                }
                .btn, button, nav, header, footer, .hidden-print {
                    display: none !important;
                }
                
                /* Page Break */
                .page-break {
                    page-break-after: always;
                }
            }

            /* Styles from Reference */
            #printable-area {
                font-family: Arial, sans-serif;
                font-size: 10pt;
            }

            .label-grid {
                display: table;
                width: 100%;
                border-collapse: collapse;
            }
            .label-row {
                display: table-row;
            }
            .label-cell {
                display: table-cell;
                width: 6.4cm; /* Standard width for 3-column A4 labels */
                height: 3.4cm; /* Standard height */
                padding: 5px;
                vertical-align: top;
                /* border: 1px dashed #ccc; /* Uncomment for guide lines */
            }
            .label-content {
                border: 1px solid #000;
                height: 2.8cm;
                width: 5.8cm;
                margin: 0 auto;
                padding: 2px;
                text-align: center;
                display: block;
                background: white;
            }
            .library-name {
                font-size: 8pt;
                font-weight: bold;
                border-bottom: 1px solid #000;
                padding-bottom: 2px;
                margin-bottom: 5px;
                text-transform: uppercase;
            }
            .call-number {
                font-weight: bold;
                font-size: 12pt;
                line-height: 1.2;
                margin-top: 5px;
            }
            .call-number span {
                display: block;
            }
            .classification {
                font-size: 12pt;
            }
            .author-code {
                font-size: 11pt;
                text-transform: uppercase;
            }
            .title-code {
                font-size: 11pt;
                text-transform: lowercase;
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
                                    <th class="px-4 py-2"></th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @forelse($books as $book)
                                    <tr class="hover:bg-slate-50 cursor-pointer {{ $selectedBookId === $book->id ? 'bg-indigo-50' : '' }}"
                                        wire:click="selectBook({{ $book->id }})">
                                        <td class="px-4 py-2 font-mono text-xs">{{ $book->code }}</td>
                                        <td class="px-4 py-2">
                                            <div class="max-w-xs truncate" title="{{ $book->title }}">{{ $book->title }}</div>
                                        </td>
                                        <td class="px-4 py-2 text-center">
                                            <span class="badge badge-info">{{ $book->stock }}</span>
                                        </td>
                                        <td class="px-4 py-2">
                                            @if($selectedBookId === $book->id)
                                                <svg class="w-5 h-5 text-indigo-600" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                                </svg>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-4 py-8 text-center text-slate-500">
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

            <!-- Label Selection -->
            <div class="space-y-4">
                @if($selectedBook)
                    <div class="bg-slate-50 rounded-lg p-4">
                        <h4 class="font-medium text-slate-800 mb-3">{{ $selectedBook->title }}</h4>
                        <div class="grid grid-cols-2 gap-4 text-sm">
                            <div>
                                <p class="text-slate-500">Penulis</p>
                                <p class="font-medium">{{ $selectedBook->author }}</p>
                            </div>
                            <div>
                                <p class="text-slate-500">Rak</p>
                                <p class="font-medium">{{ $selectedBook->shelf_location ?? '-' }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Existing Barcodes -->
                    @if($selectedBook->copies->count() > 0)
                        <div class="border border-slate-200 rounded-lg overflow-hidden">
                            <div class="bg-slate-50 px-4 py-3 border-b border-slate-200 flex items-center justify-between">
                                <h5 class="font-medium text-slate-700">Pilih Item untuk Label</h5>
                                <div class="flex items-center gap-2">
                                    <button wire:click="selectAll" class="text-sm text-indigo-600 hover:text-indigo-700">
                                        Pilih Semua
                                    </button>
                                    @if(count($selectedCopies) > 0)
                                        <span class="text-slate-300">|</span>
                                        <button wire:click="clearSelection" class="text-sm text-slate-600 hover:text-slate-700">
                                            Batal Pilih
                                        </button>
                                    @endif
                                </div>
                            </div>
                            <div class="max-h-80 overflow-y-auto">
                                <table class="w-full text-sm">
                                    <thead class="bg-slate-50 sticky top-0">
                                        <tr>
                                            <th class="px-4 py-2 w-10"></th>
                                            <th class="px-4 py-2 text-left">Kode Eksemplar</th>
                                            <th class="px-4 py-2 text-center">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-100">
                                        @foreach($selectedBook->copies as $copy)
                                            <tr class="hover:bg-slate-50">
                                                <td class="px-4 py-2">
                                                    <input type="checkbox" 
                                                           wire:model.live="selectedCopies"
                                                           value="{{ $copy->id }}"
                                                           class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
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

                        @if(count($selectedCopies) > 0)
                            <button wire:click="generateLabels" class="btn btn-primary w-full">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                                </svg>
                                Cetak {{ count($selectedCopies) }} Label
                            </button>
                        @endif
                    @else
                        <div class="bg-amber-50 border border-amber-200 rounded-lg p-4 text-center">
                            <p class="text-amber-700">Belum ada barcode yang dibuat untuk buku ini.</p>
                            <a href="{{ route('books.barcode') }}" class="text-amber-800 font-medium underline hover:text-amber-900 mt-2 inline-block">
                                Generate Barcode Dulu
                            </a>
                        </div>
                    @endif
                @else
                    <div class="bg-slate-50 rounded-lg p-8 text-center h-full flex flex-col items-center justify-center">
                        <svg class="w-16 h-16 text-slate-200 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                        </svg>
                        <p class="text-slate-500 font-medium">Pilih buku untuk melihat daftar label</p>
                    </div>
                @endif
            </div>
        </div>
    @endif
</div>
