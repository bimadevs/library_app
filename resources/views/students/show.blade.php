<x-app-layout>
    <x-slot name="header">
        Detail Siswa
    </x-slot>

    <div class="max-w-4xl space-y-6">
        <!-- Student Info Card -->
        <div class="card">
            <div class="flex items-start justify-between mb-6">
                <h3 class="card-header mb-0">Informasi Siswa</h3>
                <div class="flex items-center gap-2">
                    <a href="{{ route('students.edit', $student) }}" class="btn btn-primary">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        Edit
                    </a>
                    <a href="{{ route('students.index') }}" class="btn btn-secondary">Kembali</a>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <div class="space-y-4">
                        <div>
                            <label class="text-sm text-slate-500">NIS</label>
                            <p class="font-mono text-lg font-medium">{{ $student->nis }}</p>
                        </div>
                        <div>
                            <label class="text-sm text-slate-500">Nama Lengkap</label>
                            <p class="text-lg font-medium">{{ $student->name }}</p>
                        </div>
                        <div>
                            <label class="text-sm text-slate-500">Tempat, Tanggal Lahir</label>
                            <p>{{ $student->birth_place }}, {{ $student->birth_date->format('d F Y') }}</p>
                        </div>
                        <div>
                            <label class="text-sm text-slate-500">Jenis Kelamin</label>
                            <p>{{ $student->gender === 'L' ? 'Laki-laki' : 'Perempuan' }}</p>
                        </div>
                        <div>
                            <label class="text-sm text-slate-500">No. Telepon</label>
                            <p>{{ $student->phone }}</p>
                        </div>
                    </div>
                </div>
                <div>
                    <div class="space-y-4">
                        <div>
                            <label class="text-sm text-slate-500">Kelas</label>
                            <p>{{ $student->class->name ?? '-' }}</p>
                        </div>
                        <div>
                            <label class="text-sm text-slate-500">Jurusan</label>
                            <p>{{ $student->major->name ?? '-' }}</p>
                        </div>
                        <div>
                            <label class="text-sm text-slate-500">Tahun Ajaran</label>
                            <p>{{ $student->academicYear->name ?? '-' }}</p>
                        </div>
                        <div>
                            <label class="text-sm text-slate-500">Maks. Peminjaman</label>
                            <p>{{ $student->max_loan }} buku</p>
                        </div>
                        <div>
                            <label class="text-sm text-slate-500">Status</label>
                            <p>
                                @if($student->is_active)
                                    <span class="badge badge-success">Aktif</span>
                                @else
                                    <span class="badge badge-warning">Tidak Aktif</span>
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
                <div class="md:col-span-2">
                    <label class="text-sm text-slate-500">Alamat</label>
                    <p>{{ $student->address }}</p>
                </div>
            </div>
        </div>

        <!-- Active Loans -->
        <div class="card">
            <h3 class="card-header">Peminjaman Aktif</h3>
            
            @if($student->activeLoans->count() > 0)
                <div class="overflow-x-auto">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Judul Buku</th>
                                <th>Barcode</th>
                                <th>Tanggal Pinjam</th>
                                <th>Jatuh Tempo</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($student->activeLoans as $index => $loan)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td class="font-medium">{{ $loan->bookCopy->book->title ?? '-' }}</td>
                                    <td class="font-mono text-sm">{{ $loan->bookCopy->barcode ?? '-' }}</td>
                                    <td>{{ $loan->loan_date->format('d/m/Y') }}</td>
                                    <td>{{ $loan->due_date->format('d/m/Y') }}</td>
                                    <td>
                                        @if($loan->isOverdue())
                                            <span class="badge badge-danger">Terlambat {{ $loan->getDaysOverdue() }} hari</span>
                                        @else
                                            <span class="badge badge-info">Dipinjam</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="text-slate-500 text-center py-4">Tidak ada peminjaman aktif</p>
            @endif
        </div>

        <!-- Unpaid Fines -->
        <div class="card">
            <h3 class="card-header">Denda Belum Lunas</h3>
            
            @if($student->unpaidFines->count() > 0)
                <div class="overflow-x-auto">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Tipe</th>
                                <th>Jumlah</th>
                                <th>Keterangan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($student->unpaidFines as $index => $fine)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>
                                        @if($fine->type === 'late')
                                            <span class="badge badge-warning">Keterlambatan</span>
                                        @else
                                            <span class="badge badge-danger">Buku Hilang</span>
                                        @endif
                                    </td>
                                    <td class="font-medium">Rp {{ number_format($fine->amount, 0, ',', '.') }}</td>
                                    <td>
                                        @if($fine->type === 'late')
                                            Terlambat {{ $fine->days_overdue }} hari
                                        @else
                                            Kompensasi buku hilang
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="bg-slate-50">
                                <td colspan="2" class="font-medium text-right">Total Denda:</td>
                                <td colspan="2" class="font-bold text-red-600">
                                    Rp {{ number_format($student->unpaidFines->sum('amount'), 0, ',', '.') }}
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            @else
                <p class="text-slate-500 text-center py-4">Tidak ada denda yang belum lunas</p>
            @endif
        </div>
    </div>
</x-app-layout>
