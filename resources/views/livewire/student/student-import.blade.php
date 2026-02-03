<div>
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        @if(!$showResults)
            <div class="p-6 md:p-10">
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
                    <div>
                        <h2 class="text-xl font-bold text-slate-900">Import Data Siswa</h2>
                        <p class="text-slate-500 mt-1">Upload file Excel atau CSV untuk mengimport data siswa secara massal.</p>
                    </div>
                    <a href="{{ route('students.import.template') }}" 
                       class="inline-flex items-center justify-center px-4 py-2 text-sm font-medium text-slate-700 transition-all duration-200 bg-white border border-slate-300 rounded-lg shadow-sm hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        <svg class="w-5 h-5 mr-2 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                        </svg>
                        Download Template
                    </a>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <!-- Upload Area -->
                    <div class="lg:col-span-2 space-y-6">
                        <div class="border-2 border-dashed border-slate-300 rounded-2xl p-10 text-center transition-all duration-200 hover:bg-slate-50 hover:border-indigo-400 group relative"
                             x-data="{ isDragging: false }"
                             x-on:dragover.prevent="isDragging = true"
                             x-on:dragleave.prevent="isDragging = false"
                             x-on:drop.prevent="isDragging = false; $refs.fileInput.files = $event.dataTransfer.files; $refs.fileInput.dispatchEvent(new Event('change'))"
                             :class="{ 'border-indigo-500 bg-indigo-50 ring-4 ring-indigo-500/10': isDragging }">
                            
                            <input type="file" 
                                   wire:model="file" 
                                   x-ref="fileInput"
                                   class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10" 
                                   accept=".xlsx,.xls,.csv">
                            
                            <div class="space-y-4 pointer-events-none relative z-0">
                                <div class="w-20 h-20 bg-indigo-50 text-indigo-500 rounded-full flex items-center justify-center mx-auto group-hover:bg-indigo-100 group-hover:scale-110 transition-all duration-200 shadow-sm">
                                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                                    </svg>
                                </div>
                                
                                <div>
                                    <p class="text-xl font-bold text-slate-800">
                                        <span class="text-indigo-600 underline decoration-indigo-300 underline-offset-2 group-hover:decoration-indigo-500 transition-all">Klik untuk upload</span> atau drag & drop
                                    </p>
                                    <p class="text-slate-500 mt-2 text-sm font-medium">Excel (.xlsx, .xls) atau CSV, maksimal 5MB</p>
                                </div>
                            </div>
                        </div>

                        @error('file')
                            <div class="bg-rose-50 text-rose-700 px-4 py-3 rounded-xl text-sm flex items-start gap-3 border border-rose-100 shadow-sm">
                                <svg class="w-5 h-5 text-rose-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                <div>
                                    <span class="font-bold block mb-1">Upload Gagal!</span>
                                    {{ $message }}
                                </div>
                            </div>
                        @enderror

                        @if($file)
                            <div class="bg-indigo-50/50 rounded-xl p-4 border border-indigo-100 flex items-center justify-between shadow-sm">
                                <div class="flex items-center gap-4">
                                    <div class="w-12 h-12 bg-indigo-100 text-indigo-600 rounded-lg flex items-center justify-center shadow-sm">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="font-bold text-slate-900">{{ $file->getClientOriginalName() }}</p>
                                        <p class="text-xs font-medium text-slate-500 uppercase tracking-wide">{{ number_format($file->getSize() / 1024, 2) }} KB</p>
                                    </div>
                                </div>
                                <button wire:click="$set('file', null)" class="p-2 hover:bg-rose-100 text-slate-400 hover:text-rose-600 rounded-lg transition-colors" title="Hapus File">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </button>
                            </div>

                            <button wire:click="import" 
                                    wire:loading.attr="disabled"
                                    class="w-full inline-flex items-center justify-center px-6 py-4 text-base font-bold text-white transition-all duration-200 bg-indigo-600 border border-transparent rounded-xl shadow-lg hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 shadow-indigo-200 hover:shadow-indigo-300 active:scale-95 disabled:opacity-75 disabled:cursor-not-allowed"
                                    {{ $importing ? 'disabled' : '' }}>
                                <span wire:loading.remove wire:target="import" class="flex items-center gap-2">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                                    </svg>
                                    Mulai Import Data
                                </span>
                                <span wire:loading wire:target="import" class="flex items-center gap-2">
                                    <svg class="animate-spin w-5 h-5" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    Sedang Memproses...
                                </span>
                            </button>
                        @endif
                    </div>

                    <!-- Instructions -->
                    <div class="bg-blue-50 rounded-2xl p-6 border border-blue-100 h-fit">
                        <h4 class="font-bold text-blue-900 mb-4 flex items-center gap-2">
                            <div class="w-8 h-8 rounded-lg bg-blue-100 text-blue-600 flex items-center justify-center">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </div>
                            Petunjuk Import
                        </h4>
                        <ul class="space-y-4 text-sm text-blue-800">
                            <li class="flex items-start gap-3">
                                <span class="w-6 h-6 rounded-full bg-blue-200 text-blue-700 flex-shrink-0 flex items-center justify-center text-xs font-bold">1</span>
                                <span class="leading-relaxed">File harus berformat <span class="font-semibold">Excel (.xlsx, .xls)</span> atau <span class="font-semibold">CSV</span>.</span>
                            </li>
                            <li class="flex items-start gap-3">
                                <span class="w-6 h-6 rounded-full bg-blue-200 text-blue-700 flex-shrink-0 flex items-center justify-center text-xs font-bold">2</span>
                                <span class="leading-relaxed">Gunakan template yang disediakan agar format kolom sesuai.</span>
                            </li>
                            <li class="flex items-start gap-3">
                                <span class="w-6 h-6 rounded-full bg-blue-200 text-blue-700 flex-shrink-0 flex items-center justify-center text-xs font-bold">3</span>
                                <span class="leading-relaxed">Kolom wajib diisi: <span class="font-semibold">NIS, Nama, Kelas</span>.</span>
                            </li>
                            <li class="flex items-start gap-3">
                                <span class="w-6 h-6 rounded-full bg-blue-200 text-blue-700 flex-shrink-0 flex items-center justify-center text-xs font-bold">4</span>
                                <span class="leading-relaxed">Pastikan data master (Kelas, Jurusan) sudah ada di sistem dengan penulisan yang sama.</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        @else
            <!-- Results -->
            <div class="p-8">
                <div class="flex items-center justify-between mb-8 pb-6 border-b border-slate-100">
                    <div>
                        <h2 class="text-2xl font-bold text-slate-900">Hasil Import</h2>
                        <p class="text-slate-500 mt-1">Proses import selesai.</p>
                    </div>
                    <div class="flex gap-3">
                        <button wire:click="resetImport" class="inline-flex items-center justify-center px-4 py-2 text-sm font-medium text-slate-700 transition-all duration-200 bg-white border border-slate-300 rounded-lg shadow-sm hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-slate-500">
                            Import Lagi
                        </button>
                        <a href="{{ route('students.index') }}" class="inline-flex items-center justify-center px-4 py-2 text-sm font-medium text-white transition-all duration-200 bg-indigo-600 border border-transparent rounded-lg shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Lihat Daftar Siswa
                        </a>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
                    <div class="bg-slate-50 rounded-2xl p-6 border border-slate-100 text-center shadow-sm">
                        <p class="text-4xl font-bold text-slate-800 mb-2">{{ $summary['total'] }}</p>
                        <p class="text-sm font-bold text-slate-500 uppercase tracking-wider">Total Baris</p>
                    </div>
                    <div class="bg-emerald-50 rounded-2xl p-6 border border-emerald-100 text-center shadow-sm">
                        <p class="text-4xl font-bold text-emerald-600 mb-2">{{ $summary['imported'] }}</p>
                        <p class="text-sm font-bold text-emerald-700 uppercase tracking-wider">Berhasil</p>
                    </div>
                    <div class="bg-amber-50 rounded-2xl p-6 border border-amber-100 text-center shadow-sm">
                        <p class="text-4xl font-bold text-amber-600 mb-2">{{ $summary['skipped'] }}</p>
                        <p class="text-sm font-bold text-amber-700 uppercase tracking-wider">Dilewati</p>
                    </div>
                    <div class="bg-rose-50 rounded-2xl p-6 border border-rose-100 text-center shadow-sm">
                        <p class="text-4xl font-bold text-rose-600 mb-2">{{ $summary['failed'] }}</p>
                        <p class="text-sm font-bold text-rose-700 uppercase tracking-wider">Gagal</p>
                    </div>
                </div>

                <div class="space-y-8">
                    <!-- Imported -->
                    @if(count($imported) > 0)
                        <div class="border border-emerald-200 rounded-xl overflow-hidden shadow-sm">
                            <div class="bg-emerald-50 px-6 py-4 border-b border-emerald-200 flex items-center gap-2">
                                <div class="w-2 h-2 rounded-full bg-emerald-500"></div>
                                <h4 class="font-bold text-emerald-900">Berhasil Diimport</h4>
                            </div>
                            <div class="max-h-60 overflow-y-auto">
                                <table class="w-full text-sm">
                                    <thead class="bg-white sticky top-0 shadow-sm z-10">
                                        <tr>
                                            <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider bg-slate-50/80 backdrop-blur-sm">Baris</th>
                                            <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider bg-slate-50/80 backdrop-blur-sm">NIS</th>
                                            <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider bg-slate-50/80 backdrop-blur-sm">Nama</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-emerald-100 bg-white">
                                        @foreach($imported as $item)
                                            <tr class="hover:bg-emerald-50/30 transition-colors">
                                                <td class="px-6 py-3 text-slate-500 font-mono">{{ $item['row'] }}</td>
                                                <td class="px-6 py-3 font-mono font-medium text-emerald-700">{{ $item['nis'] }}</td>
                                                <td class="px-6 py-3 text-slate-900 font-medium">{{ $item['name'] }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endif

                    <!-- Skipped -->
                    @if(count($skipped) > 0)
                        <div class="border border-amber-200 rounded-xl overflow-hidden shadow-sm">
                            <div class="bg-amber-50 px-6 py-4 border-b border-amber-200 flex items-center gap-2">
                                <div class="w-2 h-2 rounded-full bg-amber-500"></div>
                                <h4 class="font-bold text-amber-900">Dilewati (Duplikat/Tidak Valid)</h4>
                            </div>
                            <div class="max-h-60 overflow-y-auto">
                                <table class="w-full text-sm">
                                    <thead class="bg-white sticky top-0 shadow-sm z-10">
                                        <tr>
                                            <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider bg-slate-50/80 backdrop-blur-sm">Baris</th>
                                            <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider bg-slate-50/80 backdrop-blur-sm">NIS</th>
                                            <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider bg-slate-50/80 backdrop-blur-sm">Nama</th>
                                            <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider bg-slate-50/80 backdrop-blur-sm">Alasan</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-amber-100 bg-white">
                                        @foreach($skipped as $item)
                                            <tr class="hover:bg-amber-50/30 transition-colors">
                                                <td class="px-6 py-3 text-slate-500 font-mono">{{ $item['row'] }}</td>
                                                <td class="px-6 py-3 font-mono font-medium text-amber-700">{{ $item['nis'] }}</td>
                                                <td class="px-6 py-3 text-slate-700">{{ $item['name'] }}</td>
                                                <td class="px-6 py-3 text-amber-600 font-medium italic">{{ $item['reason'] }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endif

                    <!-- Failed -->
                    @if(count($failed) > 0)
                        <div class="border border-rose-200 rounded-xl overflow-hidden shadow-sm">
                            <div class="bg-rose-50 px-6 py-4 border-b border-rose-200 flex items-center gap-2">
                                <div class="w-2 h-2 rounded-full bg-rose-500"></div>
                                <h4 class="font-bold text-rose-900">Gagal (Error)</h4>
                            </div>
                            <div class="max-h-60 overflow-y-auto">
                                <table class="w-full text-sm">
                                    <thead class="bg-white sticky top-0 shadow-sm z-10">
                                        <tr>
                                            <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider bg-slate-50/80 backdrop-blur-sm">Baris</th>
                                            <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider bg-slate-50/80 backdrop-blur-sm">Error Detail</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-rose-100 bg-white">
                                        @foreach($failed as $item)
                                            <tr class="hover:bg-rose-50/30 transition-colors">
                                                <td class="px-6 py-3 text-slate-500 font-mono align-top">{{ $item['row'] }}</td>
                                                <td class="px-6 py-3 text-rose-600">
                                                    <ul class="list-disc list-inside space-y-1">
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
                </div>
            </div>
        @endif
    </div>
</div>
