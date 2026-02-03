<div class="space-y-6"
     x-data="{ 
         studentSelected: @entangle('studentId').live,
         init() {
             this.$watch('studentSelected', (value) => {
                 if (value) {
                     this.$nextTick(() => {
                         if (this.$refs.bookBarcode) {
                             this.$refs.bookBarcode.focus();
                         }
                     });
                 } else {
                     this.$nextTick(() => {
                         if (this.$refs.studentSearchTrigger) {
                             this.$refs.studentSearchTrigger.focus();
                         }
                     });
                 }
             });
             
             // Initial focus logic
             this.$nextTick(() => {
                 if (!this.studentSelected) {
                     if (this.$refs.studentSearchTrigger) this.$refs.studentSearchTrigger.focus();
                 } else {
                     if (this.$refs.bookBarcode) this.$refs.bookBarcode.focus();
                 }
             });
         }
     }">
    <!-- Messages -->
    @if($successMessage)
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-xl flex items-start gap-3 shadow-sm animate-fade-in-down">
            <div class="p-1 bg-emerald-100 rounded-full">
                <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
            </div>
            <div class="mt-0.5">
                <p class="font-medium">Berhasil</p>
                <p class="text-sm opacity-90">{{ $successMessage }}</p>
            </div>
        </div>
    @endif

    @if($errorMessage)
        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl flex items-start gap-3 shadow-sm animate-fade-in-down">
            <div class="p-1 bg-red-100 rounded-full">
                <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </div>
            <div class="mt-0.5">
                <p class="font-medium">Terjadi Kesalahan</p>
                <p class="text-sm opacity-90">{{ $errorMessage }}</p>
            </div>
        </div>
    @endif

    <!-- Return Results (Post-Submit) -->
    @if(!empty($returnResults))
        <div class="bg-blue-50 border border-blue-200 rounded-xl p-6 shadow-sm animate-fade-in-down">
            <h4 class="font-bold text-blue-800 mb-4 flex items-center gap-2">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                Ringkasan Pengembalian
            </h4>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($returnResults as $result)
                    <div class="bg-white rounded-lg p-4 border border-blue-100 shadow-sm flex items-start gap-3">
                        <div class="p-2 {{ $result['status'] === 'lost' ? 'bg-red-100 text-red-600' : 'bg-emerald-100 text-emerald-600' }} rounded-lg">
                            @if($result['status'] === 'lost')
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            @else
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            @endif
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="font-bold text-slate-800 truncate">{{ $result['book_title'] }}</p>
                            <p class="text-xs text-slate-500 font-mono mb-1">{{ $result['barcode'] }}</p>
                            
                            <div class="flex items-center gap-2 mt-2">
                                <span class="text-xs font-medium px-2 py-0.5 rounded {{ $result['status'] === 'lost' ? 'bg-red-100 text-red-700' : 'bg-emerald-100 text-emerald-700' }}">
                                    {{ $result['status'] === 'lost' ? 'Hilang' : 'Dikembalikan' }}
                                </span>
                            </div>

                            @if($result['has_fine'])
                                <div class="mt-2 pt-2 border-t border-slate-100">
                                    <p class="text-xs text-slate-500">Denda</p>
                                    <p class="text-sm font-bold text-red-600">
                                        Rp {{ number_format($result['fine_amount'], 0, ',', '.') }}
                                        @if($result['fine_type'] === 'late')
                                            <span class="text-xs font-normal text-slate-500">({{ $result['days_overdue'] }} hari)</span>
                                        @endif
                                    </p>
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    @if(!$selectedStudent)
        <!-- Initial State: Search Student -->
        <!-- Initial State: Search Student or Book -->
        <div class="max-w-2xl mx-auto py-12">
            <div class="text-center mb-8">
                <h3 class="text-2xl font-bold text-slate-800">Mulai Pengembalian</h3>
                <p class="text-slate-500 mt-2">Cari siswa atau scan buku yang akan dikembalikan</p>
            </div>

            <!-- Option 1: Scan/Search Book -->
            <div class="bg-white rounded-2xl shadow-lg border border-slate-100 p-6 mb-6 relative overflow-hidden">
                <div class="absolute top-0 right-0 w-24 h-24 bg-blue-500 rounded-full opacity-5 -mr-10 -mt-10"></div>
                <h4 class="font-semibold text-slate-800 mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/></svg>
                    Scan / Cari Buku
                </h4>
                <div class="flex gap-2">
                    <input type="text" 
                           wire:model="bookSearch" 
                           wire:keydown.enter="searchBorrower"
                           placeholder="Scan barcode atau ketik judul buku..."
                           class="form-input flex-1">
                    <button wire:click="searchBorrower" class="btn btn-primary px-6">
                        Cari
                    </button>
                </div>
                <p class="text-xs text-slate-400 mt-2">Sistem akan otomatis mendeteksi siswa peminjam.</p>
            </div>

            <div class="flex items-center gap-4 mb-6">
                <div class="h-px bg-slate-200 flex-1"></div>
                <span class="text-slate-400 text-sm font-medium">ATAU</span>
                <div class="h-px bg-slate-200 flex-1"></div>
            </div>

            <!-- Option 2: Search Student -->
            <div class="bg-white rounded-2xl shadow-lg border border-slate-100 p-1 group cursor-pointer hover:border-blue-400 transition-colors"
                 wire:click="openStudentModal">
                <div class="p-6 flex items-center gap-6">
                    <div class="w-16 h-16 bg-slate-50 rounded-xl flex items-center justify-center text-slate-400 group-hover:bg-blue-50 group-hover:text-blue-500 transition-colors">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                    </div>
                    <div class="flex-1">
                        <h4 class="text-lg font-bold text-slate-800 group-hover:text-blue-600 transition-colors">Cari Siswa Manual</h4>
                        <p class="text-slate-500 text-sm">Cari berdasarkan Nama atau NIS siswa</p>
                    </div>
                    <div>
                        <svg class="w-6 h-6 text-slate-300 group-hover:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </div>
                </div>
            </div>
        </div>
    @else
        <!-- Active State: Two Column Layout -->
        <form wire:submit="submit" class="grid grid-cols-1 lg:grid-cols-3 gap-8 items-start">
            
            <!-- Left Column: Books -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Book Selection Header -->
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-bold text-slate-800 flex items-center gap-2">
                        <span class="w-2 h-8 bg-blue-500 rounded-full"></span>
                        Buku Dipinjam
                    </h3>
                    @if(count($borrowedBooks) > 0)
                        <div class="flex gap-3 text-sm">
                            <button type="button" wire:click="selectAll" class="text-blue-600 hover:text-blue-700 font-medium">
                                Pilih Semua
                            </button>
                            <span class="text-slate-300">|</span>
                            <button type="button" wire:click="deselectAll" class="text-slate-500 hover:text-slate-700">
                                Batal Pilih
                            </button>
                        </div>
                    @endif
                </div>

                <!-- Quick Scan -->
                <div class="card p-4 flex items-center gap-4 bg-slate-800 text-white">
                    <div class="flex-shrink-0">
                        <svg class="w-6 h-6 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <input type="text" 
                               wire:model="barcodeInput" 
                               wire:keydown.enter.prevent="scanBarcode"
                               x-ref="bookBarcode"
                               placeholder="Scan barcode buku untuk memilih otomatis..."
                               class="bg-transparent border-none text-white placeholder-slate-400 focus:ring-0 w-full text-lg">
                    </div>
                    <button type="button" wire:click="scanBarcode" class="px-4 py-1.5 bg-slate-700 hover:bg-slate-600 rounded-lg text-sm font-medium transition-colors">
                        Enter
                    </button>
                </div>

                <!-- Books List -->
                @if(count($borrowedBooks) === 0)
                    <div class="card p-12 text-center">
                        <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                            </svg>
                        </div>
                        <h3 class="text-lg font-medium text-slate-800">Tidak Ada Pinjaman</h3>
                        <p class="text-slate-500 mt-1">Siswa ini tidak sedang meminjam buku apapun.</p>
                    </div>
                @else
                    <div class="space-y-4">
                        @foreach($borrowedBooks as $book)
                            <div class="group relative card overflow-hidden transition-all duration-300 cursor-pointer {{ in_array($book['loan_id'], $selectedLoans) ? 'ring-2 ring-blue-500 shadow-md bg-blue-50/30' : 'hover:shadow-md hover:bg-slate-50' }}"
                                 wire:click="toggleLoanSelection({{ $book['loan_id'] }})">
                                <div class="p-5 flex items-start gap-5">
                                    <!-- Checkbox Area -->
                                    <div class="pt-1">
                                        <div class="relative flex items-center">
                                            <input type="checkbox" 
                                                   {{ in_array($book['loan_id'], $selectedLoans) ? 'checked' : '' }}
                                                   class="w-6 h-6 rounded border-slate-300 text-blue-600 focus:ring-blue-500 pointer-events-none transition-colors">
                                        </div>
                                    </div>

                                    <!-- Content Area -->
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-start justify-between mb-2">
                                            <div>
                                                <div class="flex items-center gap-2 mb-1">
                                                    <span class="font-mono text-xs bg-slate-100 text-slate-600 px-2 py-0.5 rounded border border-slate-200">
                                                        {{ $book['barcode'] }}
                                                    </span>
                                                    @if($book['is_overdue'])
                                                        <span class="inline-flex items-center gap-1 bg-red-50 text-red-700 px-2 py-0.5 rounded text-xs font-semibold border border-red-100">
                                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                            Telat {{ $book['days_overdue'] }} Hari
                                                        </span>
                                                    @else
                                                        <span class="inline-flex items-center gap-1 bg-emerald-50 text-emerald-700 px-2 py-0.5 rounded text-xs font-semibold border border-emerald-100">
                                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                                            Tepat Waktu
                                                        </span>
                                                    @endif
                                                </div>
                                                <h4 class="text-lg font-bold text-slate-800 leading-tight">{{ $book['book_title'] }}</h4>
                                                <p class="text-slate-500 text-sm mt-1">{{ $book['book_code'] }}</p>
                                            </div>
                                            
                                            <!-- Due Date Badge -->
                                            <div class="text-right">
                                                <p class="text-xs text-slate-400 uppercase tracking-wider font-semibold">Jatuh Tempo</p>
                                                <p class="font-medium {{ $book['is_overdue'] ? 'text-red-600' : 'text-slate-700' }}">
                                                    {{ $book['due_date'] }}
                                                </p>
                                            </div>
                                        </div>

                                        <!-- Expanded Actions (Visible when selected) -->
                                        @if(in_array($book['loan_id'], $selectedLoans))
                                            <div class="mt-4 pt-4 border-t border-slate-100 animate-fade-in-down">
                                                <div class="flex flex-wrap items-center justify-between gap-4">
                                                    <!-- Fine Calculation -->
                                                    @if($book['fine_amount'] > 0 || in_array($book['loan_id'], $lostBooks))
                                                        <div class="flex items-center gap-3">
                                                            <div class="bg-red-50 px-3 py-2 rounded-lg border border-red-100">
                                                                <p class="text-xs text-red-600 uppercase font-bold">Total Denda</p>
                                                                <p class="text-lg font-bold text-red-700">Rp {{ number_format($book['fine_amount'] + (in_array($book['loan_id'], $lostBooks) ? ($book['book_price'] ?? 50000) : 0), 0, ',', '.') }}</p>
                                                            </div>
                                                            <label class="flex items-center gap-2 cursor-pointer select-none"
                                                                   @click.stop>
                                                                <input type="checkbox" 
                                                                       wire:click="togglePaidFine({{ $book['loan_id'] }})"
                                                                       {{ in_array($book['loan_id'], $paidFines) ? 'checked' : '' }}
                                                                       class="w-5 h-5 rounded border-slate-300 text-emerald-600 focus:ring-emerald-500">
                                                                <span class="text-sm font-medium text-slate-700">Bayar Sekarang</span>
                                                            </label>
                                                        </div>
                                                    @else
                                                        <div class="text-sm text-emerald-600 font-medium flex items-center gap-1">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                            Bebas Denda
                                                        </div>
                                                    @endif

                                                    <!-- Lost Book Toggle -->
                                                    <label class="flex items-center gap-2 cursor-pointer select-none px-3 py-1.5 rounded-lg hover:bg-slate-50 transition-colors"
                                                           @click.stop>
                                                        <input type="checkbox" 
                                                               wire:click="toggleLostBook({{ $book['loan_id'] }})"
                                                               {{ in_array($book['loan_id'], $lostBooks) ? 'checked' : '' }}
                                                               class="w-4 h-4 rounded border-slate-300 text-slate-600 focus:ring-slate-500">
                                                        <span class="text-sm text-slate-600">Tandai Buku Hilang</span>
                                                    </label>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- Right Column: Info & Summary -->
            <div class="space-y-6 sticky top-6">
                <!-- Student Card -->
                <div class="card overflow-hidden">
                    <div class="card-header bg-slate-50/50 border-b border-slate-100 py-4 px-6 flex items-center justify-between">
                        <h3 class="font-semibold text-slate-800">Siswa</h3>
                        <button type="button" wire:click="clearStudent" class="text-xs text-red-500 hover:text-red-700 font-medium uppercase tracking-wider">
                            Ganti
                        </button>
                    </div>
                    <div class="p-5">
                        <div class="flex items-center gap-4 mb-4">
                            <div class="w-12 h-12 rounded-full bg-slate-200 flex items-center justify-center text-slate-500 font-bold text-lg">
                                {{ substr($selectedStudent->name, 0, 1) }}
                            </div>
                            <div>
                                <p class="font-bold text-slate-800">{{ $selectedStudent->name }}</p>
                                <p class="text-sm text-slate-500 font-mono">{{ $selectedStudent->nis }}</p>
                            </div>
                        </div>
                        <div class="text-sm text-slate-600 space-y-1">
                            <p>{{ $selectedStudent->class->name ?? '-' }} - {{ $selectedStudent->major->name ?? '-' }}</p>
                            <p class="text-xs text-slate-400">{{ $selectedStudent->academicYear->name ?? '-' }}</p>
                        </div>
                    </div>
                </div>

                <!-- Payment Summary Card -->
                <div class="card overflow-hidden border-t-4 border-t-emerald-500">
                    <div class="card-header bg-white border-b border-slate-100 py-4 px-6">
                        <h3 class="font-semibold text-slate-800">Pembayaran</h3>
                    </div>
                    <div class="p-6">
                        <div class="flex justify-between items-center mb-4">
                            <span class="text-slate-600">Buku Dikembalikan</span>
                            <span class="font-bold text-slate-800 text-lg">{{ count($selectedLoans) }}</span>
                        </div>

                        <div class="space-y-4">
                            <!-- Total Tagihan -->
                            <div class="bg-slate-50 p-4 rounded-xl border border-slate-200">
                                <p class="text-sm text-slate-500 mb-1">Total Tagihan (Dibayar)</p>
                                <p class="text-3xl font-bold text-slate-800">
                                    Rp {{ number_format($this->getTotalPayableProperty(), 0, ',', '.') }}
                                </p>
                            </div>

                            @if($this->totalFine - $this->getTotalPayableProperty() > 0)
                                <div class="px-2">
                                    <div class="flex justify-between items-center text-sm text-amber-600">
                                        <span>Dicatat sebagai Hutang:</span>
                                        <span class="font-bold">Rp {{ number_format($this->totalFine - $this->getTotalPayableProperty(), 0, ',', '.') }}</span>
                                    </div>
                                </div>
                            @endif

                            @if($this->getTotalPayableProperty() > 0)
                                <!-- Payment Input -->
                                <div>
                                    <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Uang Tunai</label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <span class="text-slate-500 font-bold">Rp</span>
                                        </div>
                                        <input type="number" 
                                               wire:model.live="cashAmount" 
                                               class="form-input pl-10 w-full text-lg font-mono font-medium" 
                                               placeholder="0">
                                    </div>
                                </div>

                                <!-- Change -->
                                <div class="flex justify-between items-center pt-2 border-t border-slate-100">
                                    <span class="text-sm font-medium text-slate-600">Kembalian</span>
                                    @php
                                        $cash = is_numeric($cashAmount) ? (float) $cashAmount : 0;
                                        $change = $cash - $this->getTotalPayableProperty();
                                    @endphp
                                    <span class="text-xl font-bold {{ $change < 0 ? 'text-red-500' : 'text-emerald-600' }}">
                                        Rp {{ number_format(max(0, $change), 0, ',', '.') }}
                                    </span>
                                </div>
                                @if($cash > 0 && $cash < $this->getTotalPayableProperty())
                                    <p class="text-xs text-red-500 text-right font-medium">Uang Kurang!</p>
                                @endif
                            @endif

                            <button type="submit" 
                                    class="w-full btn btn-primary py-3 text-lg shadow-lg shadow-emerald-500/30 hover:shadow-emerald-500/40 transition-all transform hover:-translate-y-0.5"
                                    {{ count($selectedLoans) === 0 || ($this->getTotalPayableProperty() > 0 && (is_numeric($cashAmount) ? (float) $cashAmount : 0) < $this->getTotalPayableProperty()) ? 'disabled' : '' }}
                                    onclick="return confirm('Proses pengembalian buku? Pastikan data sudah benar.')">
                                <span wire:loading.remove wire:target="submit">
                                    Proses Transaksi
                                </span>
                                <span wire:loading wire:target="submit" class="flex items-center justify-center gap-2">
                                    <svg class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    Memproses...
                                </span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    @endif

    <!-- Student Search Modal -->
    @if($showStudentModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm transition-opacity" wire:click="closeStudentModal"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
                <div class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-xl sm:w-full">
                    <div class="bg-white px-6 pt-6 pb-4">
                        <div class="flex items-center justify-between mb-6">
                            <h3 class="text-xl font-bold text-slate-800">Cari Siswa</h3>
                            <button type="button" wire:click="closeStudentModal" class="text-slate-400 hover:text-slate-600 bg-slate-100 hover:bg-slate-200 p-2 rounded-full transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>

                        <div class="mb-4 relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                            </div>
                            <input type="text" 
                                   wire:model.live.debounce.300ms="studentSearch" 
                                   placeholder="Ketik NIS atau nama siswa..."
                                   class="form-input pl-10 w-full text-lg"
                                   autofocus>
                            <p class="text-xs text-slate-500 mt-2 ml-1">Menampilkan siswa yang memiliki pinjaman aktif</p>
                        </div>

                        <div class="max-h-[60vh] overflow-y-auto custom-scrollbar">
                            @if($this->students->isEmpty())
                                <div class="text-center py-12">
                                    <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-3">
                                        <svg class="w-8 h-8 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                                    </div>
                                    <p class="text-slate-500">
                                        @if(empty($studentSearch))
                                            Ketik nama atau NIS untuk mulai mencari
                                        @else
                                            Tidak ditemukan siswa dengan pinjaman aktif
                                        @endif
                                    </p>
                                </div>
                            @else
                                <div class="space-y-2">
                                    @foreach($this->students as $student)
                                        <button type="button"
                                                wire:click="selectStudent({{ $student->id }})"
                                                class="w-full text-left p-4 rounded-xl hover:bg-slate-50 border border-transparent hover:border-slate-200 transition-all group">
                                            <div class="flex items-center justify-between">
                                                <div class="flex items-center gap-4">
                                                    <div class="w-10 h-10 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center font-bold text-sm group-hover:bg-blue-600 group-hover:text-white transition-colors">
                                                        {{ substr($student->name, 0, 1) }}
                                                    </div>
                                                    <div>
                                                        <div class="flex items-center gap-2">
                                                            <span class="font-bold text-slate-800">{{ $student->name }}</span>
                                                            <span class="text-xs bg-slate-100 text-slate-600 px-2 py-0.5 rounded-full font-mono">{{ $student->nis }}</span>
                                                        </div>
                                                        <p class="text-sm text-slate-500 mt-0.5">
                                                            {{ $student->class->name ?? '-' }} • {{ $student->major->name ?? '-' }}
                                                        </p>
                                                    </div>
                                                </div>
                                                <div class="text-right">
                                                    <span class="text-xs text-slate-400 block mb-1">Pinjaman Aktif</span>
                                                    <span class="badge badge-warning">
                                                        {{ $student->active_loans_count }} buku
                                                    </span>
                                                </div>
                                            </div>
                                        </button>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Loan Selection Modal (For Ambiguous Search Results) -->
    @if($showLoanSelectionModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm transition-opacity" wire:click="closeLoanSelectionModal"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
                <div class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full">
                    <div class="bg-white px-6 pt-6 pb-4">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-xl font-bold text-slate-800">Pilih Peminjaman</h3>
                            <button type="button" wire:click="closeLoanSelectionModal" class="text-slate-400 hover:text-slate-600 bg-slate-100 hover:bg-slate-200 p-2 rounded-full transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>
                        
                        <p class="text-slate-500 mb-4">Ditemukan beberapa peminjaman aktif dengan kata kunci tersebut. Pilih salah satu untuk melanjutkan:</p>

                        <div class="max-h-[60vh] overflow-y-auto custom-scrollbar space-y-3">
                            @foreach($foundLoans as $loan)
                                <button type="button"
                                        wire:click="selectLoanFromSearch({{ $loan->id }})"
                                        class="w-full text-left p-4 rounded-xl hover:bg-slate-50 border border-slate-100 hover:border-blue-300 transition-all group ring-0 focus:ring-2 focus:ring-blue-500">
                                    <div class="flex items-start justify-between gap-4">
                                        <div class="flex items-start gap-4">
                                            <!-- Book Icon -->
                                            <div class="w-12 h-12 rounded-lg bg-emerald-100 text-emerald-600 flex-shrink-0 flex items-center justify-center">
                                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                                            </div>
                                            
                                            <!-- Details -->
                                            <div>
                                                <div class="flex items-center gap-2 mb-1">
                                                    <span class="badge badge-success text-xs">{{ $loan->bookCopy->barcode }}</span>
                                                    <span class="text-xs text-slate-400">{{ $loan->loan_date->format('d/m/Y') }}</span>
                                                </div>
                                                <h4 class="font-bold text-slate-800">{{ $loan->bookCopy->book->title }}</h4>
                                                
                                                <div class="flex items-center gap-2 mt-2">
                                                    <div class="w-6 h-6 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center text-xs font-bold">
                                                        {{ substr($loan->student->name, 0, 1) }}
                                                    </div>
                                                    <span class="text-sm font-medium text-blue-600">{{ $loan->student->name }}</span>
                                                    <span class="text-xs text-slate-500">({{ $loan->student->class->name ?? '-' }})</span>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="hidden group-hover:block text-blue-500">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                        </div>
                                    </div>
                                </button>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
