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
                         if (this.$refs.studentSelectTrigger) {
                             this.$refs.studentSelectTrigger.focus();
                         }
                     });
                 }
             });
             
             // Initial focus logic
             this.$nextTick(() => {
                 if (!this.studentSelected) {
                     if (this.$refs.studentSelectTrigger) this.$refs.studentSelectTrigger.focus();
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

    @if($warningMessage)
        <div class="bg-amber-50 border border-amber-200 text-amber-700 px-4 py-3 rounded-xl flex items-start gap-3 shadow-sm animate-fade-in-down">
            <div class="p-1 bg-amber-100 rounded-full">
                <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
            </div>
            <div class="mt-0.5">
                <p class="font-medium">Perhatian</p>
                <p class="text-sm opacity-90">{{ $warningMessage }}</p>
            </div>
        </div>
    @endif

    <form wire:submit="submit" class="grid grid-cols-1 lg:grid-cols-3 gap-8 items-start">
        
        <!-- Left Column: Inputs -->
        <div class="lg:col-span-2 space-y-6">
            
            <!-- Student Selection -->
            <div class="card overflow-hidden">
                <div class="card-header bg-slate-50/50 border-b border-slate-100 py-4 px-6 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="p-2 bg-blue-100 text-blue-600 rounded-lg">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                        </div>
                        <h3 class="font-semibold text-slate-800">Identitas Peminjam</h3>
                    </div>
                    @if($selectedStudent)
                        <button type="button" wire:click="clearStudent" class="text-sm text-slate-400 hover:text-red-500 transition-colors flex items-center gap-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            <span>Ganti Siswa</span>
                        </button>
                    @endif
                </div>
                
                <div class="p-6">
                    @if($selectedStudent)
                        <div class="flex items-start gap-5 animate-fade-in-up">
                            <div class="w-16 h-16 rounded-full bg-slate-200 flex items-center justify-center text-slate-400 text-xl font-bold border-4 border-white shadow-sm">
                                {{ substr($selectedStudent->name, 0, 1) }}
                            </div>
                            <div class="flex-1">
                                <h4 class="text-lg font-bold text-slate-800">{{ $selectedStudent->name }}</h4>
                                <div class="flex flex-wrap gap-2 mt-2 mb-3">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-slate-100 text-slate-800">
                                        {{ $selectedStudent->nis }}
                                    </span>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-50 text-blue-700">
                                        {{ $selectedStudent->class->name ?? '-' }}
                                    </span>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-50 text-indigo-700">
                                        {{ $selectedStudent->major->name ?? '-' }}
                                    </span>
                                </div>
                                
                                <div class="grid grid-cols-2 gap-4 mt-4 bg-slate-50 rounded-xl p-4 border border-slate-100">
                                    <div>
                                        <p class="text-xs text-slate-500 uppercase tracking-wider font-semibold">Tahun Ajaran</p>
                                        <p class="text-slate-700 font-medium mt-1">{{ $selectedStudent->academicYear->name ?? '-' }}</p>
                                    </div>
                                    <div>
                                        <p class="text-xs text-slate-500 uppercase tracking-wider font-semibold">Status Pinjaman</p>
                                        <div class="flex items-center gap-2 mt-1">
                                            <div class="flex-1 h-2 bg-slate-200 rounded-full overflow-hidden">
                                                <div class="h-full {{ $selectedStudent->active_loans_count >= $selectedStudent->max_loan ? 'bg-red-500' : 'bg-emerald-500' }}" style="width: {{ ($selectedStudent->active_loans_count / max(1, $selectedStudent->max_loan)) * 100 }}%"></div>
                                            </div>
                                            <span class="text-sm font-bold {{ $selectedStudent->active_loans_count >= $selectedStudent->max_loan ? 'text-red-600' : 'text-emerald-600' }}">
                                                {{ $selectedStudent->active_loans_count }}/{{ $selectedStudent->max_loan }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="text-center py-8 px-4 border-2 border-dashed border-slate-200 rounded-xl hover:border-blue-300 transition-colors group cursor-pointer focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                             wire:click="openStudentModal"
                             x-ref="studentSelectTrigger"
                             tabindex="0"
                             @keydown.enter="$wire.openStudentModal()">
                            <div class="w-16 h-16 bg-blue-50 text-blue-500 rounded-full flex items-center justify-center mx-auto mb-4 group-hover:scale-110 transition-transform duration-300">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                                </svg>
                            </div>
                            <h3 class="text-lg font-medium text-slate-800 group-hover:text-blue-600 transition-colors">Pilih Siswa</h3>
                            <p class="text-slate-500 text-sm mt-1 max-w-xs mx-auto">Klik di sini atau tekan Enter untuk mencari siswa</p>
                        </div>
                        @error('studentId')
                            <p class="text-sm text-red-600 mt-2 text-center">{{ $message }}</p>
                        @enderror
                    @endif
                </div>
            </div>

            <!-- Book Selection -->
            <div class="card overflow-hidden transition-all duration-300"
                 x-bind:class="{ 'opacity-50 grayscale pointer-events-none': !studentSelected }">
                <div class="card-header bg-slate-50/50 border-b border-slate-100 py-4 px-6 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="p-2 bg-emerald-100 text-emerald-600 rounded-lg">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                            </svg>
                        </div>
                        <h3 class="font-semibold text-slate-800">Buku yang Dipinjam</h3>
                    </div>
                    @if($selectedBookCopy)
                        <button type="button" wire:click="clearBookCopy" class="text-sm text-slate-400 hover:text-red-500 transition-colors flex items-center gap-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            <span>Ganti Buku</span>
                        </button>
                    @endif
                </div>

                <div class="p-6">
                    @if(!$selectedBookCopy)
                        <div class="mb-6 relative">
                            <label class="block text-sm font-medium text-slate-700 mb-2">Scan Barcode Cepat</label>
                            <div class="relative group">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400 group-focus-within:text-emerald-500 transition-colors">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>
                                    </svg>
                                </div>
                                <input type="text" 
                                       wire:model="barcodeInput" 
                                       wire:keydown.enter.prevent="scanBarcode"
                                       x-ref="bookBarcode"
                                       x-bind:disabled="!studentSelected"
                                       @keydown.window="if ($event.key === 'Enter' && studentSelected && $refs.bookBarcode.value === '' && $wire.bookCopyId) { $wire.submit(); }"
                                       placeholder="Scan barcode buku di sini..."
                                       class="form-input pl-10 w-full transition-shadow focus:ring-2 focus:ring-emerald-500/20 disabled:bg-slate-100 disabled:cursor-not-allowed">
                                <button type="button" 
                                        wire:click="scanBarcode" 
                                        x-bind:disabled="!studentSelected"
                                        class="absolute inset-y-1 right-1 px-3 bg-slate-100 text-slate-600 hover:bg-emerald-500 hover:text-white rounded-md text-sm font-medium transition-all disabled:opacity-50 disabled:cursor-not-allowed">
                                    Cek
                                </button>
                            </div>
                        </div>

                        <div class="flex items-center gap-4 my-6">
                            <div class="flex-1 h-px bg-slate-100"></div>
                            <span class="text-xs font-semibold text-slate-400 uppercase tracking-widest">Atau</span>
                            <div class="flex-1 h-px bg-slate-100"></div>
                        </div>

                        <div class="text-center py-6 px-4 border-2 border-dashed border-slate-200 rounded-xl hover:border-emerald-300 transition-colors group cursor-pointer" wire:click="openBookModal">
                            <div class="w-12 h-12 bg-emerald-50 text-emerald-500 rounded-full flex items-center justify-center mx-auto mb-3 group-hover:scale-110 transition-transform duration-300">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                            </div>
                            <span class="text-slate-600 font-medium group-hover:text-emerald-600 transition-colors">Cari Manual di Katalog</span>
                        </div>
                        @error('bookCopyId')
                            <p class="text-sm text-red-600 mt-2 text-center">{{ $message }}</p>
                        @enderror
                    @else
                        <div class="flex gap-5 animate-fade-in-up">
                            <div class="w-24 h-32 bg-slate-100 rounded-lg flex-shrink-0 flex items-center justify-center border border-slate-200 shadow-sm">
                                <svg class="w-10 h-10 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                </svg>
                            </div>
                            <div class="flex-1 py-1">
                                <h4 class="text-lg font-bold text-slate-800 leading-tight">{{ $selectedBookCopy->book->title }}</h4>
                                <p class="text-slate-500 text-sm mt-1 mb-3">{{ $selectedBookCopy->book->author }}</p>
                                
                                <div class="flex flex-wrap gap-3">
                                    <div class="bg-slate-50 border border-slate-100 px-3 py-1.5 rounded-lg">
                                        <p class="text-xs text-slate-500 uppercase">Barcode</p>
                                        <p class="font-mono font-medium text-slate-800">{{ $selectedBookCopy->barcode }}</p>
                                    </div>
                                    <div class="bg-slate-50 border border-slate-100 px-3 py-1.5 rounded-lg">
                                        <p class="text-xs text-slate-500 uppercase">Kode Buku</p>
                                        <p class="font-mono font-medium text-slate-800">{{ $selectedBookCopy->book->code }}</p>
                                    </div>
                                    <div class="bg-slate-50 border border-slate-100 px-3 py-1.5 rounded-lg">
                                        <p class="text-xs text-slate-500 uppercase">Lokasi</p>
                                        <p class="font-medium text-slate-800">{{ $selectedBookCopy->shelf->name ?? '-' }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Right Column: Settings & Actions -->
        <div class="space-y-6 sticky top-6">
            <div class="card overflow-hidden border-t-4 border-t-blue-500">
                <div class="card-header bg-white border-b border-slate-100 py-4 px-6">
                    <h3 class="font-semibold text-slate-800">Detail Peminjaman</h3>
                </div>
                <div class="p-6 space-y-5">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Tipe Peminjaman</label>
                        <select wire:model.live="loanType" class="form-select w-full bg-slate-50 focus:bg-white transition-colors">
                            @foreach($loanTypeOptions as $value => $label)
                                <option value="{{ $value }}">{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('loanType') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>

                    @if($loanType === 'custom')
                        <div class="animate-fade-in-down">
                            <label class="block text-sm font-medium text-slate-700 mb-2">Tanggal Jatuh Tempo</label>
                            <input type="date" 
                                   wire:model="customDueDate" 
                                   min="{{ date('Y-m-d') }}"
                                   class="form-input w-full">
                            @error('customDueDate') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                        </div>
                    @else
                        <div class="bg-blue-50 rounded-lg p-4 border border-blue-100">
                            <div class="flex items-start gap-3">
                                <svg class="w-5 h-5 text-blue-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                <div>
                                    <p class="text-sm text-blue-800 font-medium">Estimasi Jatuh Tempo</p>
                                    <p class="text-lg font-bold text-blue-900 mt-1">
                                        @if($loanType === 'regular')
                                            {{ now()->addDays(7)->isoFormat('D MMMM Y') }}
                                        @elseif($loanType === 'semester')
                                            {{ now()->addDays(180)->isoFormat('D MMMM Y') }}
                                        @endif
                                    </p>
                                    <p class="text-xs text-blue-600 mt-1">
                                        @if($loanType === 'regular')
                                            Durasi: 7 Hari
                                        @elseif($loanType === 'semester')
                                            Durasi: 1 Semester (6 Bulan)
                                        @endif
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endif

                    <div class="pt-4 border-t border-slate-100">
                        <button type="submit" 
                                class="btn btn-primary w-full py-3 text-lg shadow-lg shadow-blue-500/20 hover:shadow-blue-500/30 transition-all transform hover:-translate-y-0.5"
                                wire:loading.attr="disabled">
                            <span wire:loading.remove wire:target="submit" class="flex items-center justify-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                Proses Peminjaman
                            </span>
                            <span wire:loading wire:target="submit" class="flex items-center justify-center gap-2">
                                <svg class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Memproses...
                            </span>
                        </button>
                        <button type="button" wire:click="resetForm" class="btn btn-secondary w-full mt-3 text-sm">
                            Reset Form
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>

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
                                            Tidak ditemukan siswa dengan kata kunci tersebut
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
                                                    <span class="text-xs text-slate-400 block mb-1">Pinjaman</span>
                                                    <span class="badge {{ $student->active_loans_count >= $student->max_loan ? 'badge-danger' : 'badge-success' }}">
                                                        {{ $student->active_loans_count }}/{{ $student->max_loan }}
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

    <!-- Book Search Modal -->
    @if($showBookModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm transition-opacity" wire:click="closeBookModal"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
                <div class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-xl sm:w-full">
                    <div class="bg-white px-6 pt-6 pb-4">
                        <div class="flex items-center justify-between mb-6">
                            <h3 class="text-xl font-bold text-slate-800">Cari Buku</h3>
                            <button type="button" wire:click="closeBookModal" class="text-slate-400 hover:text-slate-600 bg-slate-100 hover:bg-slate-200 p-2 rounded-full transition-colors">
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
                                   wire:model.live.debounce.300ms="bookSearch" 
                                   placeholder="Judul, penulis, atau barcode..."
                                   class="form-input pl-10 w-full text-lg"
                                   autofocus>
                        </div>

                        <div class="max-h-[60vh] overflow-y-auto custom-scrollbar">
                            @if($this->bookCopies->isEmpty())
                                <div class="text-center py-12">
                                    <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-3">
                                        <svg class="w-8 h-8 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                                    </div>
                                    <p class="text-slate-500">
                                        @if(empty($bookSearch))
                                            Ketik judul atau barcode untuk mencari buku
                                        @else
                                            Tidak ada buku tersedia yang cocok
                                        @endif
                                    </p>
                                </div>
                            @else
                                <div class="space-y-2">
                                    @foreach($this->bookCopies as $bookCopy)
                                        <button type="button"
                                                wire:click="selectBookCopy({{ $bookCopy->id }})"
                                                class="w-full text-left p-4 rounded-xl hover:bg-slate-50 border border-transparent hover:border-slate-200 transition-all group">
                                            <div class="flex items-start gap-4">
                                                <div class="w-10 h-10 rounded-lg bg-emerald-100 text-emerald-600 flex-shrink-0 flex items-center justify-center group-hover:bg-emerald-600 group-hover:text-white transition-colors">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                                                </div>
                                                <div class="flex-1 min-w-0">
                                                    <div class="flex items-center justify-between mb-1">
                                                        <span class="text-xs font-mono bg-slate-100 text-slate-600 px-2 py-0.5 rounded">{{ $bookCopy->barcode }}</span>
                                                        <span class="badge badge-success text-xs">Tersedia</span>
                                                    </div>
                                                    <h4 class="font-bold text-slate-800 truncate">{{ $bookCopy->book->title }}</h4>
                                                    <p class="text-sm text-slate-500 truncate">{{ $bookCopy->book->author }}</p>
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
</div>
