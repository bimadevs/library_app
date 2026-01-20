<div class="max-w-4xl mx-auto">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
        <!-- Scan Section -->
        <div class="space-y-6">
            <div class="bg-white rounded-xl shadow-lg p-8 text-center border border-slate-100">
                <div class="w-20 h-20 bg-emerald-100 text-emerald-600 rounded-full flex items-center justify-center mx-auto mb-6">
                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>
                    </svg>
                </div>
                
                <h2 class="text-2xl font-bold text-slate-800 mb-2">Scan Kartu Anggota</h2>
                <p class="text-slate-500 mb-8">Tempelkan kartu pada scanner atau ketik NIS</p>

                <form wire:submit="checkIn" class="relative">
                    <div class="relative">
                        <input type="text" 
                               wire:model="nis" 
                               class="w-full text-center text-2xl font-mono tracking-wider py-3 border-2 border-slate-200 rounded-lg focus:border-emerald-500 focus:ring-emerald-500 pr-12"
                               placeholder="Scan Barcode..."
                               autofocus
                               autocomplete="off">
                        
                        <button type="button" 
                                wire:click="$set('showSearchModal', true)"
                                class="absolute right-2 top-1/2 -translate-y-1/2 p-2 text-slate-400 hover:text-emerald-600 hover:bg-emerald-50 rounded-lg transition-colors"
                                title="Cari Siswa Manual">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </button>
                    </div>
                    
                    @error('nis')
                        <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                    @enderror

                    <button type="submit" class="hidden">Submit</button>
                </form>
            </div>

            <!-- Last Visitor Card -->
            @if($lastVisitor)
                <div class="bg-emerald-50 border border-emerald-200 rounded-xl p-6 flex items-center gap-6 animate-fade-in-up">
                    <div class="w-20 h-20 bg-white rounded-full overflow-hidden border-2 border-emerald-200 flex-shrink-0">
                        <img src="{{ $lastVisitor->photo_url }}" alt="Foto" class="w-full h-full object-cover">
                    </div>
                    <div>
                        <p class="text-emerald-600 font-medium mb-1">Selamat Datang,</p>
                        <h3 class="text-xl font-bold text-slate-800">{{ $lastVisitor->name }}</h3>
                        <p class="text-slate-600">{{ $lastVisitor->class->name ?? '-' }} - {{ $lastVisitor->major->code ?? '-' }}</p>
                    </div>
                </div>
            @endif
        </div>

        <!-- Recent Visitors List -->
        <div class="bg-white rounded-xl shadow-lg border border-slate-100 overflow-hidden flex flex-col h-[500px]">
            <div class="p-6 border-b border-slate-100">
                <h3 class="font-bold text-lg text-slate-800">Pengunjung Terkini</h3>
                <p class="text-slate-500 text-sm">Hari ini, {{ now()->format('d F Y') }}</p>
            </div>
            
            <div class="flex-1 overflow-y-auto p-0">
                @forelse($recentVisitors as $visitor)
                    <div class="flex items-center gap-4 p-4 border-b border-slate-50 hover:bg-slate-50 transition-colors">
                        <div class="w-12 h-12 bg-slate-100 rounded-full overflow-hidden flex-shrink-0">
                            <img src="{{ $visitor->student->photo_url }}" alt="Avatar" class="w-full h-full object-cover">
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="font-medium text-slate-800 truncate">{{ $visitor->student->name }}</p>
                            <p class="text-xs text-slate-500">{{ $visitor->student->class->name ?? '' }} {{ $visitor->student->major->code ?? '' }}</p>
                        </div>
                        <div class="text-xs font-mono text-slate-400">
                            {{ $visitor->created_at->format('H:i') }}
                        </div>
                    </div>
                @empty
                    <div class="flex flex-col items-center justify-center h-full text-slate-400 p-8 text-center">
                        <svg class="w-12 h-12 mb-3 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                        </svg>
                        <p>Belum ada pengunjung hari ini</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Search Modal -->
    @if($showSearchModal)
        <div class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4"
             x-data
             @keydown.escape.window="$wire.set('showSearchModal', false)">
            <div class="bg-white rounded-xl shadow-xl w-full max-w-lg overflow-hidden animate-fade-in-up">
                <div class="p-4 border-b border-slate-100 flex items-center justify-between">
                    <h3 class="font-bold text-lg text-slate-800">Cari Siswa</h3>
                    <button wire:click="$set('showSearchModal', false)" class="text-slate-400 hover:text-slate-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
                
                <div class="p-4">
                    <input type="text" 
                           wire:model.live.debounce.300ms="searchQuery" 
                           class="form-input w-full" 
                           placeholder="Ketik Nama atau NIS..."
                           autofocus>
                    
                    <div class="mt-4 space-y-2 max-h-64 overflow-y-auto">
                        @forelse($searchResults as $student)
                            <button wire:click="selectStudent({{ $student->id }})" 
                                    class="w-full flex items-center gap-3 p-3 rounded-lg hover:bg-slate-50 border border-transparent hover:border-slate-200 transition-all text-left group">
                                <div class="w-10 h-10 bg-slate-100 rounded-full overflow-hidden flex-shrink-0">
                                    <img src="{{ $student->photo_url }}" alt="Avatar" class="w-full h-full object-cover">
                                </div>
                                <div>
                                    <p class="font-medium text-slate-800 group-hover:text-emerald-600">{{ $student->name }}</p>
                                    <p class="text-xs text-slate-500">{{ $student->nis }} • {{ $student->class->name ?? '' }} - {{ $student->major->code ?? '' }}</p>
                                </div>
                            </button>
                        @empty
                            @if(strlen($searchQuery) >= 2)
                                <p class="text-center text-slate-500 py-4">Tidak ada siswa ditemukan</p>
                            @else
                                <p class="text-center text-slate-400 py-4">Ketik minimal 2 karakter untuk mencari</p>
                            @endif
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
