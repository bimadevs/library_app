<div>
    @if(!$showResults)
        <!-- Upload Form -->
        <div class="space-y-6">
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                <h4 class="font-medium text-blue-800 mb-2">Petunjuk Import</h4>
                <ul class="text-sm text-blue-700 space-y-1 list-disc list-inside">
                    <li>File harus berformat Excel (.xlsx, .xls) atau CSV</li>
                    <li>Baris pertama harus berisi header kolom</li>
                    <li>Kolom wajib: kode_buku, judul, pengarang, penerbit, tempat_terbit, tahun_terbit, stok, jumlah_halaman, klasifikasi_ddc, kategori, lokasi_rak, sumber</li>
                    <li>Kolom opsional: isbn, ketebalan, sub_klasifikasi, deskripsi, tanggal_masuk, harga</li>
                    <li>Format tanggal: YYYY-MM-DD atau DD/MM/YYYY</li>
                    <li>Klasifikasi DDC harus sesuai dengan kode DDC di data master (contoh: 000, 100, 200)</li>
                    <li>Nama penerbit dan kategori harus sesuai dengan data master</li>
                </ul>
            </div>

            <div class="flex items-center gap-4">
                <a href="{{ route('books.import.template') }}" class="btn btn-secondary">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                    </svg>
                    Download Template
                </a>
            </div>

            <div class="border-2 border-dashed border-slate-300 rounded-lg p-8 text-center"
                 x-data="{ isDragging: false }"
                 x-on:dragover.prevent="isDragging = true"
                 x-on:dragleave.prevent="isDragging = false"
                 x-on:drop.prevent="isDragging = false; $refs.fileInput.files = $event.dataTransfer.files; $refs.fileInput.dispatchEvent(new Event('change'))"
                 :class="{ 'border-emerald-500 bg-emerald-50': isDragging }">
                
                <input type="file" 
                       wire:model="file" 
                       x-ref="fileInput"
                       class="hidden" 
                       accept=".xlsx,.xls,.csv">
                
                <div class="space-y-4">
                    <svg class="w-12 h-12 mx-auto text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                    </svg>
                    
                    <div>
                        <button type="button" 
                                x-on:click="$refs.fileInput.click()"
                                class="text-emerald-600 hover:text-emerald-700 font-medium">
                            Pilih file
                        </button>
                        <span class="text-slate-500"> atau drag & drop file di sini</span>
                    </div>
                    
                    <p class="text-sm text-slate-400">Excel (.xlsx, .xls) atau CSV, maksimal 5MB</p>
                </div>
            </div>

            @error('file')
                <p class="text-red-500 text-sm">{{ $message }}</p>
            @enderror

            @if($file)
                <div class="flex items-center justify-between bg-slate-50 rounded-lg p-4">
                    <div class="flex items-center gap-3">
                        <svg class="w-8 h-8 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        <div>
                            <p class="font-medium text-slate-800">{{ $file->getClientOriginalName() }}</p>
                            <p class="text-sm text-slate-500">{{ number_format($file->getSize() / 1024, 2) }} KB</p>
                        </div>
                    </div>
                    <button wire:click="$set('file', null)" class="text-slate-400 hover:text-red-500">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <button wire:click="import" 
                        wire:loading.attr="disabled"
                        class="btn btn-primary"
                        {{ $importing ? 'disabled' : '' }}>
                    <span wire:loading.remove wire:target="import">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                        </svg>
                        Import Data
                    </span>
                    <span wire:loading wire:target="import" class="flex items-center gap-2">
                        <svg class="animate-spin w-5 h-5" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Mengimport...
                    </span>
                </button>
            @endif
        </div>
    @else
        <!-- Import Results -->
        <div class="space-y-6">
            <!-- Summary Cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="bg-slate-50 rounded-lg p-4 text-center">
                    <p class="text-2xl font-bold text-slate-800">{{ $summary['total'] }}</p>
                    <p class="text-sm text-slate-500">Total Baris</p>
                </div>
                <div class="bg-emerald-50 rounded-lg p-4 text-center">
                    <p class="text-2xl font-bold text-emerald-600">{{ $summary['imported'] }}</p>
                    <p class="text-sm text-emerald-700">Berhasil Import</p>
                </div>
                <div class="bg-amber-50 rounded-lg p-4 text-center">
                    <p class="text-2xl font-bold text-amber-600">{{ $summary['skipped'] }}</p>
                    <p class="text-sm text-amber-700">Dilewati (Duplikat)</p>
                </div>
                <div class="bg-red-50 rounded-lg p-4 text-center">
                    <p class="text-2xl font-bold text-red-600">{{ $summary['failed'] }}</p>
                    <p class="text-sm text-red-700">Gagal</p>
                </div>
            </div>

            <!-- Imported Records -->
            @if(count($imported) > 0)
                <div class="border border-emerald-200 rounded-lg overflow-hidden">
                    <div class="bg-emerald-50 px-4 py-3 border-b border-emerald-200">
                        <h4 class="font-medium text-emerald-800">Data Berhasil Diimport ({{ count($imported) }})</h4>
                    </div>
                    <div class="max-h-48 overflow-y-auto">
                        <table class="w-full text-sm">
                            <thead class="bg-emerald-50 sticky top-0">
                                <tr>
                                    <th class="px-4 py-2 text-left">Baris</th>
                                    <th class="px-4 py-2 text-left">Kode</th>
                                    <th class="px-4 py-2 text-left">Judul</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-emerald-100">
                                @foreach($imported as $item)
                                    <tr>
                                        <td class="px-4 py-2">{{ $item['row'] }}</td>
                                        <td class="px-4 py-2 font-mono">{{ $item['code'] }}</td>
                                        <td class="px-4 py-2">{{ $item['title'] }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif

            <!-- Skipped Records -->
            @if(count($skipped) > 0)
                <div class="border border-amber-200 rounded-lg overflow-hidden">
                    <div class="bg-amber-50 px-4 py-3 border-b border-amber-200">
                        <h4 class="font-medium text-amber-800">Data Dilewati - Duplikat Kode ({{ count($skipped) }})</h4>
                    </div>
                    <div class="max-h-48 overflow-y-auto">
                        <table class="w-full text-sm">
                            <thead class="bg-amber-50 sticky top-0">
                                <tr>
                                    <th class="px-4 py-2 text-left">Baris</th>
                                    <th class="px-4 py-2 text-left">Kode</th>
                                    <th class="px-4 py-2 text-left">Judul</th>
                                    <th class="px-4 py-2 text-left">Alasan</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-amber-100">
                                @foreach($skipped as $item)
                                    <tr>
                                        <td class="px-4 py-2">{{ $item['row'] }}</td>
                                        <td class="px-4 py-2 font-mono">{{ $item['code'] }}</td>
                                        <td class="px-4 py-2">{{ $item['title'] }}</td>
                                        <td class="px-4 py-2 text-amber-700">{{ $item['reason'] }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif

            <!-- Failed Records -->
            @if(count($failed) > 0)
                <div class="border border-red-200 rounded-lg overflow-hidden">
                    <div class="bg-red-50 px-4 py-3 border-b border-red-200">
                        <h4 class="font-medium text-red-800">Data Gagal Import ({{ count($failed) }})</h4>
                    </div>
                    <div class="max-h-48 overflow-y-auto">
                        <table class="w-full text-sm">
                            <thead class="bg-red-50 sticky top-0">
                                <tr>
                                    <th class="px-4 py-2 text-left">Baris</th>
                                    <th class="px-4 py-2 text-left">Error</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-red-100">
                                @foreach($failed as $item)
                                    <tr>
                                        <td class="px-4 py-2">{{ $item['row'] }}</td>
                                        <td class="px-4 py-2 text-red-700">
                                            <ul class="list-disc list-inside">
                                                @foreach($item['errors'] as $error)
                                                    <li>{{ $error }}</li>
                                                @endforeach
                                            </ul>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif

            <!-- Actions -->
            <div class="flex items-center gap-3 pt-4 border-t border-slate-200">
                <button wire:click="resetImport" class="btn btn-secondary">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                    </svg>
                    Import Lagi
                </button>
                <a href="{{ route('books.index') }}" class="btn btn-primary">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
                    </svg>
                    Lihat Daftar Buku
                </a>
            </div>
        </div>
    @endif
</div>
