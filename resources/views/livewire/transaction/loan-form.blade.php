<div class="space-y-6">
    <!-- Messages -->
    @if($successMessage)
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-lg flex items-start gap-3">
            <svg class="w-5 h-5 text-emerald-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <span>{{ $successMessage }}</span>
        </div>
    @endif

    @if($errorMessage)
        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg flex items-start gap-3">
            <svg class="w-5 h-5 text-red-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <span>{{ $errorMessage }}</span>
        </div>
    @endif

    @if($warningMessage)
        <div class="bg-amber-50 border border-amber-200 text-amber-700 px-4 py-3 rounded-lg flex items-start gap-3">
            <svg class="w-5 h-5 text-amber-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
            <span>{{ $warningMessage }}</span>
        </div>
    @endif

    <form wire:submit="submit" class="space-y-6">
        <!-- Student Selection -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Data Siswa</h3>
            </div>
            <div class="card-body space-y-4">
                @if($selectedStudent)
                    <div class="bg-slate-50 rounded-lg p-4">
                        <div class="flex items-start justify-between">
                            <div class="space-y-2">
                                <div class="flex items-center gap-2">
                                    <span class="font-mono text-sm bg-slate-200 px-2 py-0.5 rounded">{{ $selectedStudent->nis }}</span>
                                    <span class="font-semibold text-slate-800">{{ $selectedStudent->name }}</span>
                                </div>
                                <div class="text-sm text-slate-600 space-y-1">
                                    <p>Kelas: {{ $selectedStudent->class->name ?? '-' }} - {{ $selectedStudent->major->name ?? '-' }}</p>
                                    <p>Tahun Ajaran: {{ $selectedStudent->academicYear->name ?? '-' }}</p>
                                    <p>Pinjaman Aktif: <span class="font-medium">{{ $selectedStudent->active_loans_count }}</span> / {{ $selectedStudent->max_loan }} buku</p>
                                </div>
                            </div>
                            <button type="button" wire:click="clearStudent" class="text-slate-400 hover:text-red-500 transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                @else
                    <div class="flex items-center gap-3">
                        <button type="button" wire:click="openStudentModal" class="btn btn-secondary">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                            Cari Siswa
                        </button>
                        <span class="text-sm text-slate-500">atau scan kartu siswa</span>
                    </div>
                @endif
                @error('studentId')
                    <p class="text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Book Selection -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Data Buku</h3>
            </div>
            <div class="card-body space-y-4">
                <!-- Barcode Scanner Input -->
                <div>
                    <label class="form-label">Scan Barcode</label>
                    <div class="flex gap-2">
                        <input type="text" 
                               wire:model="barcodeInput" 
                               wire:keydown.enter.prevent="scanBarcode"
                               placeholder="Scan atau ketik barcode buku..."
                               class="form-input flex-1"
                               autofocus>
                        <button type="button" wire:click="scanBarcode" class="btn btn-secondary">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>
                            </svg>
                        </button>
                    </div>
                    <p class="text-xs text-slate-500 mt-1">Tekan Enter setelah scan barcode</p>
                </div>

                <div class="flex items-center gap-3">
                    <div class="flex-1 border-t border-slate-200"></div>
                    <span class="text-sm text-slate-400">atau</span>
                    <div class="flex-1 border-t border-slate-200"></div>
                </div>

                @if($selectedBookCopy)
                    <div class="bg-slate-50 rounded-lg p-4">
                        <div class="flex items-start justify-between">
                            <div class="space-y-2">
                                <div class="flex items-center gap-2">
                                    <span class="font-mono text-sm bg-blue-100 text-blue-700 px-2 py-0.5 rounded">{{ $selectedBookCopy->barcode }}</span>
                                </div>
                                <p class="font-semibold text-slate-800">{{ $selectedBookCopy->book->title }}</p>
                                <div class="text-sm text-slate-600 space-y-1">
                                    <p>Kode Buku: {{ $selectedBookCopy->book->code }}</p>
                                    <p>Pengarang: {{ $selectedBookCopy->book->author }}</p>
                                </div>
                            </div>
                            <button type="button" wire:click="clearBookCopy" class="text-slate-400 hover:text-red-500 transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                @else
                    <button type="button" wire:click="openBookModal" class="btn btn-secondary">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        Cari Buku
                    </button>
                @endif
                @error('bookCopyId')
                    <p class="text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Loan Duration -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Durasi Peminjaman</h3>
            </div>
            <div class="card-body space-y-4">
                <div>
                    <label class="form-label">Tipe Peminjaman</label>
                    <select wire:model.live="loanType" class="form-select">
                        @foreach($loanTypeOptions as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('loanType')
                        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                @if($loanType === 'custom')
                    <div>
                        <label class="form-label">Tanggal Jatuh Tempo</label>
                        <input type="date" 
                               wire:model="customDueDate" 
                               min="{{ date('Y-m-d') }}"
                               class="form-input">
                        @error('customDueDate')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                @else
                    <div class="bg-slate-50 rounded-lg p-3">
                        <p class="text-sm text-slate-600">
                            @if($loanType === 'regular')
                                Jatuh tempo: <span class="font-medium">{{ now()->addDays(7)->format('d F Y') }}</span> (7 hari)
                            @elseif($loanType === 'semester')
                                Jatuh tempo: <span class="font-medium">{{ now()->addDays(180)->format('d F Y') }}</span> (6 bulan)
                            @endif
                        </p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Submit Buttons -->
        <div class="flex items-center justify-end gap-3">
            <button type="button" wire:click="resetForm" class="btn btn-secondary">
                Reset Form
            </button>
            <button type="submit" class="btn btn-primary" wire:loading.attr="disabled">
                <span wire:loading.remove wire:target="submit">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Simpan Peminjaman
                </span>
                <span wire:loading wire:target="submit">
                    <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Menyimpan...
                </span>
            </button>
        </div>
    </form>

    <!-- Student Search Modal -->
    @if($showStudentModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-slate-500 bg-opacity-75 transition-opacity" wire:click="closeStudentModal"></div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>

                <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-semibold text-slate-800">Cari Siswa</h3>
                            <button type="button" wire:click="closeStudentModal" class="text-slate-400 hover:text-slate-600">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>

                        <div class="mb-4">
                            <input type="text" 
                                   wire:model.live.debounce.300ms="studentSearch" 
                                   placeholder="Cari berdasarkan NIS atau nama..."
                                   class="form-input w-full"
                                   autofocus>
                        </div>

                        <div class="max-h-64 overflow-y-auto">
                            @if($this->students->isEmpty())
                                <p class="text-center text-slate-500 py-4">
                                    @if(empty($studentSearch))
                                        Ketik untuk mencari siswa
                                    @else
                                        Tidak ada siswa yang ditemukan
                                    @endif
                                </p>
                            @else
                                <div class="space-y-2">
                                    @foreach($this->students as $student)
                                        <button type="button"
                                                wire:click="selectStudent({{ $student->id }})"
                                                class="w-full text-left p-3 rounded-lg hover:bg-slate-50 border border-slate-200 transition-colors">
                                            <div class="flex items-center justify-between">
                                                <div>
                                                    <div class="flex items-center gap-2">
                                                        <span class="font-mono text-xs bg-slate-100 px-1.5 py-0.5 rounded">{{ $student->nis }}</span>
                                                        <span class="font-medium text-slate-800">{{ $student->name }}</span>
                                                    </div>
                                                    <p class="text-sm text-slate-500 mt-1">
                                                        {{ $student->class->name ?? '-' }} - {{ $student->major->name ?? '-' }}
                                                    </p>
                                                </div>
                                                <div class="text-right">
                                                    <span class="text-xs text-slate-500">Pinjaman</span>
                                                    <p class="font-medium {{ $student->active_loans_count >= $student->max_loan ? 'text-red-600' : 'text-slate-700' }}">
                                                        {{ $student->active_loans_count }}/{{ $student->max_loan }}
                                                    </p>
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
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-slate-500 bg-opacity-75 transition-opacity" wire:click="closeBookModal"></div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>

                <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-semibold text-slate-800">Cari Buku</h3>
                            <button type="button" wire:click="closeBookModal" class="text-slate-400 hover:text-slate-600">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>

                        <div class="mb-4">
                            <input type="text" 
                                   wire:model.live.debounce.300ms="bookSearch" 
                                   placeholder="Cari berdasarkan barcode, kode, atau judul..."
                                   class="form-input w-full"
                                   autofocus>
                        </div>

                        <div class="max-h-64 overflow-y-auto">
                            @if($this->bookCopies->isEmpty())
                                <p class="text-center text-slate-500 py-4">
                                    @if(empty($bookSearch))
                                        Ketik untuk mencari buku
                                    @else
                                        Tidak ada buku tersedia yang ditemukan
                                    @endif
                                </p>
                            @else
                                <div class="space-y-2">
                                    @foreach($this->bookCopies as $bookCopy)
                                        <button type="button"
                                                wire:click="selectBookCopy({{ $bookCopy->id }})"
                                                class="w-full text-left p-3 rounded-lg hover:bg-slate-50 border border-slate-200 transition-colors">
                                            <div class="flex items-center gap-2 mb-1">
                                                <span class="font-mono text-xs bg-blue-100 text-blue-700 px-1.5 py-0.5 rounded">{{ $bookCopy->barcode }}</span>
                                                <span class="badge badge-success text-xs">Tersedia</span>
                                            </div>
                                            <p class="font-medium text-slate-800">{{ $bookCopy->book->title }}</p>
                                            <p class="text-sm text-slate-500">{{ $bookCopy->book->author }}</p>
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
