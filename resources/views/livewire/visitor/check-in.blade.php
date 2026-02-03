<div class="h-full flex flex-col lg:flex-row gap-8 max-w-7xl mx-auto w-full">
    <!-- Left Panel: Action Zone -->
    <div class="flex-1 flex flex-col justify-center relative">
        
        <!-- Welcome / Success State -->
        @if($lastVisitor)
            <div class="absolute inset-0 z-20 flex flex-col items-center justify-center bg-white/90 backdrop-blur-md rounded-3xl border border-emerald-100 shadow-2xl animate-in fade-in zoom-in duration-300 p-8 text-center">
                <div class="w-32 h-32 rounded-full p-1 bg-gradient-to-br from-emerald-400 to-teal-600 mb-6 shadow-lg shadow-emerald-200/50">
                    <img src="{{ $lastVisitor->photo_url }}" alt="{{ $lastVisitor->name }}" class="w-full h-full rounded-full object-cover border-4 border-white">
                </div>
                
                <div class="space-y-2">
                    <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-emerald-100 text-emerald-700 text-sm font-bold tracking-wide uppercase mb-2">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                        </svg>
                        Check-in Berhasil
                    </div>
                    <h2 class="text-4xl font-bold text-slate-800 tracking-tight">{{ $lastVisitor->name }}</h2>
                    <p class="text-xl text-slate-500 font-medium">{{ $lastVisitor->class->name ?? '-' }} &bull; {{ $lastVisitor->major->code ?? '-' }}</p>
                </div>

                <div class="mt-8 grid grid-cols-2 gap-4 w-full max-w-sm">
                    <div class="bg-slate-50 p-4 rounded-2xl border border-slate-100">
                        <p class="text-xs text-slate-400 uppercase tracking-wider font-bold">Waktu Masuk</p>
                        <p class="text-lg font-mono font-bold text-slate-700">{{ now()->format('H:i') }}</p>
                    </div>
                    <div class="bg-slate-50 p-4 rounded-2xl border border-slate-100">
                        <p class="text-xs text-slate-400 uppercase tracking-wider font-bold">Tanggal</p>
                        <p class="text-lg font-mono font-bold text-slate-700">{{ now()->format('d M') }}</p>
                    </div>
                </div>

                <!-- Auto dismiss hint -->
                <div class="absolute bottom-8 text-slate-400 text-sm animate-pulse">
                    Siap untuk pemindaian berikutnya...
                </div>
            </div>
        @endif

        <!-- Scan Form -->
        <div class="bg-white rounded-[2.5rem] shadow-2xl shadow-slate-200/50 border border-white p-10 lg:p-16 text-center relative overflow-hidden group">
            <div class="absolute top-0 left-0 w-full h-2 bg-gradient-to-r from-emerald-400 via-teal-500 to-blue-500"></div>
            
            <div class="mb-10 relative">
                <div class="w-24 h-24 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-6 text-slate-300 group-hover:scale-110 group-hover:text-emerald-500 transition-all duration-500 ease-out">
                    <svg class="w-12 h-12 transition-colors duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>
                    </svg>
                </div>
                <h2 class="text-3xl lg:text-4xl font-bold text-slate-800 mb-3">Selamat Datang</h2>
                <p class="text-slate-500 text-lg">Silakan scan kartu anggota Anda</p>
            </div>

            <form wire:submit="checkIn" class="relative max-w-lg mx-auto">
                <div class="relative group/input">
                    <div class="absolute inset-y-0 left-0 pl-6 flex items-center pointer-events-none">
                        <svg class="h-8 w-8 text-slate-300 group-focus-within/input:text-emerald-500 transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 11c0 3.517-1.009 6.799-2.753 9.571m-3.44-2.04l.054-.09A13.916 13.916 0 008 11a4 4 0 118 0c0 1.017-.07 2.019-.203 3m-2.118 6.844A21.88 21.88 0 0015.171 17m3.839 1.132c.645-2.266.99-4.659.99-7.132A8 8 0 008 4.07M3 15.364c.64-1.319 1-2.8 1-4.364 0-1.457.2-2.85.5-4m1.5 1.5l1.042.52" />
                        </svg>
                    </div>
                    <input type="text" 
                           wire:model="nis" 
                           class="block w-full pl-20 pr-6 py-6 bg-slate-50 border-2 border-slate-100 rounded-2xl text-3xl font-mono text-slate-800 placeholder-slate-300 focus:ring-4 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all duration-300 shadow-inner"
                           placeholder="Scan / Ketik NIS"
                           autofocus
                           autocomplete="off">
                </div>

                @error('nis')
                    <div class="mt-4 p-4 bg-red-50 text-red-600 rounded-xl flex items-center justify-center gap-2 animate-shake">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span class="font-medium">{{ $message }}</span>
                    </div>
                @enderror

                <div class="mt-8 grid grid-cols-1 md:grid-cols-2 gap-4">
                    <button type="submit" class="bg-emerald-600 text-white py-4 px-8 rounded-xl text-lg font-bold hover:bg-emerald-700 hover:shadow-lg hover:shadow-emerald-500/30 active:scale-95 transition-all duration-200">
                        Check In
                    </button>
                    <button type="button" 
                            wire:click="$set('showSearchModal', true)"
                            class="bg-white text-slate-600 border-2 border-slate-200 py-4 px-8 rounded-xl text-lg font-bold hover:border-slate-300 hover:bg-slate-50 active:scale-95 transition-all duration-200">
                        Cari Manual
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Right Panel: Recent Visitors -->
    <div class="lg:w-96 flex flex-col h-full overflow-hidden">
        <div class="bg-white/50 backdrop-blur-sm rounded-3xl border border-white/50 shadow-xl flex-1 flex flex-col overflow-hidden">
            <div class="p-6 border-b border-slate-100/50 flex items-center justify-between bg-white/50">
                <h3 class="font-bold text-lg text-slate-800 flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    Terkini
                </h3>
                <span class="text-xs font-medium text-slate-400 bg-slate-100 px-2 py-1 rounded-full">{{ count($recentVisitors) }} Siswa</span>
            </div>
            
            <div class="flex-1 overflow-y-auto p-4 space-y-3 custom-scrollbar">
                @forelse($recentVisitors as $visitor)
                    <div class="bg-white p-4 rounded-2xl shadow-sm border border-slate-100 flex items-center gap-4 hover:shadow-md transition-shadow">
                        <div class="w-12 h-12 rounded-full p-0.5 bg-gradient-to-br from-slate-100 to-slate-200 flex-shrink-0">
                            <img src="{{ $visitor->student->photo_url }}" class="w-full h-full rounded-full object-cover border-2 border-white" alt="Avatar">
                        </div>
                        <div class="flex-1 min-w-0">
                            <h4 class="font-bold text-slate-800 truncate text-sm">{{ $visitor->student->name }}</h4>
                            <div class="flex items-center gap-2 text-xs text-slate-500 mt-0.5">
                                <span class="bg-slate-100 px-1.5 py-0.5 rounded text-slate-600 font-mono">{{ $visitor->student->class->name ?? '?' }}</span>
                                <span class="truncate">{{ $visitor->student->major->code ?? '-' }}</span>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="text-xs font-bold text-slate-400 font-mono">{{ $visitor->created_at->format('H:i') }}</p>
                        </div>
                    </div>
                @empty
                    <div class="h-full flex flex-col items-center justify-center text-slate-400 p-8 text-center space-y-4">
                        <div class="w-16 h-16 bg-slate-100 rounded-full flex items-center justify-center opacity-50">
                            <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <p class="text-sm">Belum ada pengunjung hari ini.<br>Jadilah yang pertama!</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Search Modal -->
    @if($showSearchModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 backdrop-blur-sm bg-slate-900/20"
             x-data
             @keydown.escape.window="$wire.set('showSearchModal', false)">
            <div class="bg-white rounded-3xl shadow-2xl w-full max-w-2xl overflow-hidden animate-in fade-in zoom-in-95 duration-200">
                <div class="p-6 border-b border-slate-100 flex items-center justify-between">
                    <div>
                        <h3 class="font-bold text-2xl text-slate-800">Cari Siswa</h3>
                        <p class="text-slate-500 text-sm">Cari berdasarkan Nama atau NIS</p>
                    </div>
                    <button wire:click="$set('showSearchModal', false)" class="w-10 h-10 rounded-full bg-slate-100 text-slate-500 flex items-center justify-center hover:bg-red-50 hover:text-red-500 transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
                
                <div class="p-6 bg-slate-50">
                    <div class="relative">
                        <input type="text" 
                               wire:model.live.debounce.300ms="searchQuery" 
                               class="w-full pl-12 pr-4 py-4 rounded-xl border-none shadow-sm focus:ring-2 focus:ring-emerald-500 text-lg" 
                               placeholder="Mulai mengetik nama..."
                               autofocus>
                        <div class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </div>
                    </div>
                    
                    <div class="mt-6 space-y-2 h-[400px] overflow-y-auto custom-scrollbar">
                        @forelse($searchResults as $student)
                            <button wire:click="selectStudent({{ $student->id }})" 
                                    class="w-full flex items-center gap-4 p-4 rounded-2xl bg-white border border-slate-200 hover:border-emerald-500 hover:ring-1 hover:ring-emerald-500 hover:shadow-md transition-all text-left group">
                                <div class="w-12 h-12 bg-slate-100 rounded-full overflow-hidden flex-shrink-0">
                                    <img src="{{ $student->photo_url }}" alt="Avatar" class="w-full h-full object-cover">
                                </div>
                                <div class="flex-1">
                                    <p class="font-bold text-lg text-slate-800 group-hover:text-emerald-600 transition-colors">{{ $student->name }}</p>
                                    <p class="text-sm text-slate-500">{{ $student->nis }} • <span class="font-mono bg-slate-100 px-1 rounded">{{ $student->class->name ?? '' }}</span></p>
                                </div>
                                <div class="text-slate-300 group-hover:text-emerald-500">
                                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                    </svg>
                                </div>
                            </button>
                        @empty
                            @if(strlen($searchQuery) >= 2)
                                <div class="text-center py-12">
                                    <div class="w-16 h-16 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-4 text-slate-400">
                                        <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </div>
                                    <p class="text-slate-500 font-medium">Tidak ada siswa ditemukan</p>
                                    <p class="text-slate-400 text-sm">Coba kata kunci lain</p>
                                </div>
                            @else
                                <div class="text-center py-12">
                                    <p class="text-slate-400">Ketik minimal 2 karakter untuk mencari</p>
                                </div>
                            @endif
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
