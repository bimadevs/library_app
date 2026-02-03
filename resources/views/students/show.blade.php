<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2 text-sm text-slate-500">
            <a href="{{ route('students.index') }}" class="hover:text-indigo-600 transition-colors">Daftar Siswa</a>
            <svg class="w-4 h-4 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
            </svg>
            <span class="text-slate-800 font-medium truncate">{{ $student->name }}</span>
        </div>
    </x-slot>

    <div class="max-w-7xl mx-auto pb-12 px-4 sm:px-6 lg:px-8 py-8" x-data="{ activeTab: 'details' }">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
            
            <!-- Left Column: Photo & Quick Info -->
            <div class="lg:col-span-4 xl:col-span-3 space-y-6 lg:sticky lg:top-8">
                <!-- Profile Card -->
                <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 flex flex-col items-center text-center relative overflow-hidden">
                    <div class="absolute inset-0 bg-gradient-to-b from-indigo-50/50 to-transparent h-32 z-0"></div>
                    
                    <div class="relative z-10 w-32 h-32 rounded-full overflow-hidden bg-slate-100 mb-4 ring-4 ring-white shadow-md">
                        <img src="{{ $student->photo_url }}" alt="{{ $student->name }}" class="w-full h-full object-cover">
                    </div>
                    
                    <div class="relative z-10">
                        <h2 class="text-xl font-bold text-slate-900 mb-1">{{ $student->name }}</h2>
                        <p class="text-slate-500 font-mono text-sm mb-4">{{ $student->nis }}</p>

                        <div class="w-full flex justify-center mb-6">
                            @if($student->is_active)
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-sm font-medium bg-emerald-50 text-emerald-700 ring-1 ring-emerald-600/10">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                    Aktif
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-sm font-medium bg-slate-50 text-slate-600 ring-1 ring-slate-600/10">
                                    <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span>
                                    Tidak Aktif
                                </span>
                            @endif
                        </div>

                        <div class="flex flex-col gap-3 w-full">
                            <a href="{{ route('students.edit', $student) }}" class="inline-flex items-center justify-center px-4 py-2 text-sm font-medium text-slate-700 transition-all duration-200 bg-white border border-slate-300 rounded-lg shadow-sm hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 w-full">
                                <svg class="w-4 h-4 mr-2 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                                </svg>
                                Edit Profil
                            </a>
                            <button type="button" class="inline-flex items-center justify-center px-4 py-2 text-sm font-medium text-slate-700 transition-all duration-200 bg-white border border-slate-300 rounded-lg shadow-sm hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 w-full">
                                <svg class="w-4 h-4 mr-2 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                                </svg>
                                Cetak Kartu
                            </button>
                            <form action="{{ route('students.destroy', $student) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data siswa ini?')" class="w-full">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="inline-flex items-center justify-center px-4 py-2 text-sm font-medium text-rose-600 transition-all duration-200 bg-rose-50 border border-transparent rounded-lg shadow-sm hover:bg-rose-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-rose-500 w-full">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                    Hapus Siswa
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Stats Summary -->
                <div class="grid grid-cols-2 gap-4">
                    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-4 text-center hover:shadow-md transition-shadow duration-200">
                        <div class="text-3xl font-bold text-indigo-600 mb-1">{{ $student->activeLoans->count() }}</div>
                        <div class="text-xs text-slate-500 font-bold uppercase tracking-wider">Peminjaman</div>
                    </div>
                    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-4 text-center hover:shadow-md transition-shadow duration-200">
                        <div class="text-3xl font-bold text-rose-600 mb-1">{{ $student->unpaidFines->count() }}</div>
                        <div class="text-xs text-slate-500 font-bold uppercase tracking-wider">Denda</div>
                    </div>
                </div>
            </div>

            <!-- Right Column: Tabs & Content -->
            <div class="lg:col-span-8 xl:col-span-9">
                
                <!-- Tab Navigation -->
                <div class="bg-white rounded-xl shadow-sm border border-slate-200 mb-6 px-1 py-1 flex space-x-1">
                    <button @click="activeTab = 'details'" 
                            :class="activeTab === 'details' ? 'bg-indigo-50 text-indigo-700 shadow-sm' : 'text-slate-500 hover:text-slate-700 hover:bg-slate-50'"
                            class="flex-1 py-2.5 px-4 rounded-lg text-sm font-medium transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-indigo-500/50">
                        Detail Biodata
                    </button>
                    <button @click="activeTab = 'loans'" 
                            :class="activeTab === 'loans' ? 'bg-indigo-50 text-indigo-700 shadow-sm' : 'text-slate-500 hover:text-slate-700 hover:bg-slate-50'"
                            class="flex-1 py-2.5 px-4 rounded-lg text-sm font-medium transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-indigo-500/50">
                        Riwayat Peminjaman
                    </button>
                    <button @click="activeTab = 'fines'" 
                            :class="activeTab === 'fines' ? 'bg-indigo-50 text-indigo-700 shadow-sm' : 'text-slate-500 hover:text-slate-700 hover:bg-slate-50'"
                            class="flex-1 py-2.5 px-4 rounded-lg text-sm font-medium transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-indigo-500/50">
                        Denda & Pelanggaran
                    </button>
                </div>

                <!-- Tab Contents -->
                <div class="bg-white rounded-2xl shadow-sm border border-slate-200 min-h-[500px]">
                    
                    <!-- Details Tab -->
                    <div x-show="activeTab === 'details'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" class="p-6 md:p-8">
                        <h3 class="text-lg font-bold text-slate-900 mb-6 flex items-center gap-2">
                            <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                            Biodata & Akademik
                        </h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-x-12 gap-y-8">
                            <dl class="space-y-6">
                                <div class="border-b border-slate-100 pb-4">
                                    <dt class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">Nama Lengkap</dt>
                                    <dd class="text-base font-medium text-slate-900">{{ $student->name }}</dd>
                                </div>
                                <div class="border-b border-slate-100 pb-4">
                                    <dt class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">Tempat, Tanggal Lahir</dt>
                                    <dd class="text-base font-medium text-slate-900">{{ $student->birth_place }}, {{ $student->birth_date->format('d M Y') }}</dd>
                                </div>
                                <div class="border-b border-slate-100 pb-4">
                                    <dt class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">Jenis Kelamin</dt>
                                    <dd class="text-base font-medium text-slate-900">{{ $student->gender === 'L' ? 'Laki-laki' : 'Perempuan' }}</dd>
                                </div>
                                <div>
                                    <dt class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">Kontak</dt>
                                    <dd class="text-base font-medium text-slate-900">{{ $student->phone }}</dd>
                                </div>
                            </dl>

                            <dl class="space-y-6">
                                <div class="border-b border-slate-100 pb-4">
                                    <dt class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">Kelas</dt>
                                    <dd class="text-base font-medium text-slate-900">{{ $student->class->name ?? '-' }}</dd>
                                </div>
                                <div class="border-b border-slate-100 pb-4">
                                    <dt class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">Jurusan</dt>
                                    <dd class="text-base font-medium text-slate-900">{{ $student->major->name ?? '-' }}</dd>
                                </div>
                                <div class="border-b border-slate-100 pb-4">
                                    <dt class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">Tahun Ajaran</dt>
                                    <dd class="text-base font-medium text-slate-900">{{ $student->academicYear->name ?? '-' }}</dd>
                                </div>
                                <div>
                                    <dt class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">Alamat</dt>
                                    <dd class="text-base font-medium text-slate-900 leading-relaxed">{{ $student->address }}</dd>
                                </div>
                            </dl>
                        </div>
                    </div>

                    <!-- Loans Tab -->
                    <div x-show="activeTab === 'loans'" x-cloak x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" class="flex flex-col h-full">
                        <div class="p-6 border-b border-slate-100 flex justify-between items-center bg-slate-50/50 rounded-t-2xl">
                            <h3 class="text-lg font-bold text-slate-900 flex items-center gap-2">
                                <svg class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                </svg>
                                Peminjaman Aktif
                            </h3>
                            @if($student->activeLoans->count() > 0)
                                <span class="bg-amber-100 text-amber-800 text-xs font-bold px-2.5 py-1 rounded-full border border-amber-200">{{ $student->activeLoans->count() }} Buku</span>
                            @endif
                        </div>

                        <div class="overflow-x-auto flex-1">
                            @if($student->activeLoans->count() > 0)
                                <table class="w-full text-left text-sm text-slate-600">
                                    <thead class="bg-slate-50 text-slate-500 uppercase font-bold text-xs tracking-wider">
                                        <tr>
                                            <th class="px-6 py-4">No</th>
                                            <th class="px-6 py-4">Judul Buku</th>
                                            <th class="px-6 py-4">Barcode</th>
                                            <th class="px-6 py-4">Tanggal Pinjam</th>
                                            <th class="px-6 py-4">Jatuh Tempo</th>
                                            <th class="px-6 py-4 text-right">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-100">
                                        @foreach($student->activeLoans as $index => $loan)
                                            <tr class="hover:bg-slate-50/80 transition-colors">
                                                <td class="px-6 py-4">{{ $index + 1 }}</td>
                                                <td class="px-6 py-4 font-semibold text-slate-900">{{ $loan->bookCopy->book->title ?? '-' }}</td>
                                                <td class="px-6 py-4 font-mono text-slate-500">{{ $loan->bookCopy->barcode ?? '-' }}</td>
                                                <td class="px-6 py-4">{{ $loan->loan_date->format('d/m/Y') }}</td>
                                                <td class="px-6 py-4 {{ $loan->isOverdue() ? 'text-rose-600 font-bold' : '' }}">{{ $loan->due_date->format('d/m/Y') }}</td>
                                                <td class="px-6 py-4 text-right">
                                                    @if($loan->isOverdue())
                                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-rose-50 text-rose-700 ring-1 ring-rose-600/10">
                                                            Terlambat {{ $loan->getDaysOverdue() }} hari
                                                        </span>
                                                    @else
                                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-amber-50 text-amber-700 ring-1 ring-amber-600/10">
                                                            Dipinjam
                                                        </span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            @else
                                <div class="flex flex-col items-center justify-center py-16 text-center text-slate-500">
                                    <div class="bg-slate-50 p-4 rounded-full mb-4">
                                        <svg class="w-8 h-8 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                        </svg>
                                    </div>
                                    <p class="font-medium text-slate-900 mb-1">Tidak ada peminjaman aktif</p>
                                    <p class="text-sm mb-4">Siswa ini tidak sedang meminjam buku apapun.</p>
                                    <a href="{{ route('transactions.loans.create', ['student_nis' => $student->nis]) }}" class="inline-flex items-center justify-center px-4 py-2 text-sm font-medium text-indigo-700 transition-all duration-200 bg-indigo-50 border border-transparent rounded-lg hover:bg-indigo-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                        Buat Peminjaman Baru
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Fines Tab -->
                    <div x-show="activeTab === 'fines'" x-cloak x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" class="flex flex-col h-full">
                        <div class="p-6 border-b border-slate-100 flex justify-between items-center bg-slate-50/50 rounded-t-2xl">
                            <h3 class="text-lg font-bold text-slate-900 flex items-center gap-2">
                                <svg class="w-5 h-5 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                Denda Belum Lunas
                            </h3>
                            @if($student->unpaidFines->count() > 0)
                                <span class="bg-rose-100 text-rose-800 text-xs font-bold px-2.5 py-1 rounded-full border border-rose-200">Total: Rp {{ number_format($student->unpaidFines->sum('amount'), 0, ',', '.') }}</span>
                            @endif
                        </div>
                        
                        <div class="overflow-x-auto flex-1">
                            @if($student->unpaidFines->count() > 0)
                                <table class="w-full text-left text-sm text-slate-600">
                                    <thead class="bg-slate-50 text-slate-500 uppercase font-bold text-xs tracking-wider">
                                        <tr>
                                            <th class="px-6 py-4">No</th>
                                            <th class="px-6 py-4">Tipe</th>
                                            <th class="px-6 py-4">Jumlah</th>
                                            <th class="px-6 py-4">Keterangan</th>
                                            <th class="px-6 py-4 text-right">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-100">
                                        @foreach($student->unpaidFines as $index => $fine)
                                            <tr class="hover:bg-slate-50/80 transition-colors">
                                                <td class="px-6 py-4">{{ $index + 1 }}</td>
                                                <td class="px-6 py-4">
                                                    @if($fine->type === 'late')
                                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded text-xs font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                                            Keterlambatan
                                                        </span>
                                                    @else
                                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded text-xs font-bold bg-rose-50 text-rose-700 border border-rose-200">
                                                            Buku Hilang
                                                        </span>
                                                    @endif
                                                </td>
                                                <td class="px-6 py-4 font-mono font-bold text-slate-900">Rp {{ number_format($fine->amount, 0, ',', '.') }}</td>
                                                <td class="px-6 py-4 text-slate-500">
                                                    @if($fine->type === 'late')
                                                        Terlambat {{ $fine->days_overdue }} hari
                                                    @else
                                                        Kompensasi buku hilang
                                                    @endif
                                                </td>
                                                <td class="px-6 py-4 text-right">
                                                    <form action="{{ route('fines.pay', $fine) }}" method="POST" onsubmit="return confirm('Konfirmasi pembayaran denda sebesar Rp {{ number_format($fine->amount, 0, ',', '.') }}?')">
                                                        @csrf
                                                        <button type="submit" class="inline-flex items-center justify-center px-3 py-1.5 text-xs font-bold text-white transition-all duration-200 bg-emerald-600 rounded-md shadow-sm hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500">
                                                            Bayar
                                                        </button>
                                                    </form>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            @else
                                <div class="flex flex-col items-center justify-center py-16 text-center text-slate-500">
                                    <div class="bg-slate-50 p-4 rounded-full mb-4">
                                        <svg class="w-8 h-8 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                    </div>
                                    <p class="font-medium text-slate-900 mb-1">Bebas Denda</p>
                                    <p class="text-sm">Siswa ini tidak memiliki tanggungan denda.</p>
                                </div>
                            @endif
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
