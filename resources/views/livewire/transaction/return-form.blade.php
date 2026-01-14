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

    <!-- Return Results -->
    @if(!empty($returnResults))
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
            <h4 class="font-semibold text-blue-800 mb-3">Detail Pengembalian</h4>
            <div class="space-y-2">
                @foreach($returnResults as $result)
                    <div class="bg-white rounded p-3 border border-blue-100">
                        <div class="flex items-start justify-between">
                            <div>
                                <p class="font-medium text-slate-800">{{ $result['book_title'] }}</p>
                                <p class="text-sm text-slate-500">Barcode: {{ $result['barcode'] }}</p>
                            </div>
                            <div class="text-right">
                                @if($result['status'] === 'lost')
                                    <span class="badge badge-danger">Hilang</span>
                                @else
                                    <span class="badge badge-success">Dikembalikan</span>
                                @endif
                                @if($result['has_fine'])
                                    <p class="text-sm text-red-600 mt-1">
                                        Denda: Rp {{ number_format($result['fine_amount'], 0, ',', '.') }}
                                        @if($result['fine_type'] === 'late')
                                            ({{ $result['days_overdue'] }} hari)
                                        @endif
                                    </p>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
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
                        <span class="text-sm text-slate-500">Cari siswa yang memiliki pinjaman aktif</span>
                    </div>
                @endif
            </div>
        </div>

        <!-- Borrowed Books -->
        @if($selectedStudent)
            <div class="card">
                <div class="card-header flex items-center justify-between">
                    <h3 class="card-title">Buku yang Dipinjam</h3>
                    @if(count($borrowedBooks) > 0)
                        <div class="flex gap-2">
                            <button type="button" wire:click="selectAll" class="text-sm text-blue-600 hover:text-blue-800">
                                Pilih Semua
                            </button>
                            <span class="text-slate-300">|</span>
                            <button type="button" wire:click="deselectAll" class="text-sm text-slate-600 hover:text-slate-800">
                                Batal Pilih
                            </button>
                        </div>
                    @endif
                </div>
                <div class="card-body space-y-4">
                    <!-- Barcode Scanner Input -->
                    <div>
                        <label class="form-label">Scan Barcode untuk Memilih</label>
                        <div class="flex gap-2">
                            <input type="text" 
                                   wire:model="barcodeInput" 
                                   wire:keydown.enter.prevent="scanBarcode"
                                   placeholder="Scan barcode buku..."
                                   class="form-input flex-1">
                            <button type="button" wire:click="scanBarcode" class="btn btn-secondary">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                    @if(count($borrowedBooks) === 0)
                        <div class="text-center py-8 text-slate-500">
                            <svg class="w-12 h-12 mx-auto text-slate-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                            </svg>
                            <p>Siswa tidak memiliki pinjaman aktif</p>
                        </div>
                    @else
                        <div class="space-y-3">
                            @foreach($borrowedBooks as $book)
                                <div class="border rounded-lg p-4 {{ in_array($book['loan_id'], $selectedLoans) ? 'border-blue-500 bg-blue-50' : 'border-slate-200' }} transition-colors">
                                    <div class="flex items-start gap-4">
                                        <!-- Checkbox -->
                                        <div class="pt-1">
                                            <input type="checkbox" 
                                                   wire:click="toggleLoanSelection({{ $book['loan_id'] }})"
                                                   {{ in_array($book['loan_id'], $selectedLoans) ? 'checked' : '' }}
                                                   class="w-5 h-5 rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                                        </div>

                                        <!-- Book Info -->
                                        <div class="flex-1">
                                            <div class="flex items-center gap-2 mb-1">
                                                <span class="font-mono text-xs bg-blue-100 text-blue-700 px-1.5 py-0.5 rounded">{{ $book['barcode'] }}</span>
                                                @if($book['is_overdue'])
                                                    <span class="badge badge-danger text-xs">Terlambat {{ $book['days_overdue'] }} hari</span>
                                                @endif
                                            </div>
                                            <p class="font-medium text-slate-800">{{ $book['book_title'] }}</p>
                                            <div class="text-sm text-slate-500 mt-1">
                                                <span>Kode: {{ $book['book_code'] }}</span>
                                                <span class="mx-2">•</span>
                                                <span>Pinjam: {{ $book['loan_date'] }}</span>
                                                <span class="mx-2">•</span>
                                                <span>Jatuh Tempo: {{ $book['due_date'] }}</span>
                                            </div>
                                        </div>

                                        <!-- Fine & Lost Toggle -->
                                        <div class="text-right">
                                            @if($book['fine_amount'] > 0 && !in_array($book['loan_id'], $lostBooks))
                                                <p class="text-red-600 font-medium">
                                                    Denda: Rp {{ number_format($book['fine_amount'], 0, ',', '.') }}
                                                </p>
                                            @endif
                                            
                                            @if(in_array($book['loan_id'], $selectedLoans))
                                                <label class="flex items-center gap-2 mt-2 cursor-pointer">
                                                    <input type="checkbox" 
                                                           wire:click="toggleLostBook({{ $book['loan_id'] }})"
                                                           {{ in_array($book['loan_id'], $lostBooks) ? 'checked' : '' }}
                                                           class="w-4 h-4 rounded border-slate-300 text-red-600 focus:ring-red-500">
                                                    <span class="text-sm text-red-600">Buku Hilang</span>
                                                </label>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            <!-- Summary & Submit -->
            @if(count($borrowedBooks) > 0)
                <div class="card">
                    <div class="card-body">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-slate-600">
                                    Buku dipilih: <span class="font-semibold">{{ count($selectedLoans) }}</span>
                                </p>
                                @if($totalFine > 0)
                                    <p class="text-lg font-semibold text-red-600">
                                        Total Denda: Rp {{ number_format($totalFine, 0, ',', '.') }}
                                    </p>
                                @endif
                            </div>
                            <div class="flex items-center gap-3">
                                <button type="button" wire:click="resetForm" class="btn btn-secondary">
                                    Reset Form
                                </button>
                                <button type="submit" 
                                        class="btn btn-primary" 
                                        wire:loading.attr="disabled"
                                        {{ count($selectedLoans) === 0 ? 'disabled' : '' }}>
                                    <span wire:loading.remove wire:target="submit">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                        </svg>
                                        Proses Pengembalian
                                    </span>
                                    <span wire:loading wire:target="submit">
                                        <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
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
            @endif
        @endif
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
                            <p class="text-xs text-slate-500 mt-1">Hanya menampilkan siswa dengan pinjaman aktif</p>
                        </div>

                        <div class="max-h-64 overflow-y-auto">
                            @if($this->students->isEmpty())
                                <p class="text-center text-slate-500 py-4">
                                    @if(empty($studentSearch))
                                        Ketik untuk mencari siswa
                                    @else
                                        Tidak ada siswa dengan pinjaman aktif yang ditemukan
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
                                                    <p class="font-medium text-blue-600">
                                                        {{ $student->active_loans_count }} buku
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
</div>
