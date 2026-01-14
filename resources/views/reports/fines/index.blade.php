<x-app-layout>
    <x-slot name="header">
        Laporan Denda
    </x-slot>

    <div class="card mb-6">
        <div class="card-body">
            <form method="GET" action="{{ route('reports.fines') }}" class="flex flex-wrap gap-4 items-end">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Tanggal Mulai</label>
                    <input type="date" name="start_date" value="{{ $startDate }}" class="form-input">
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Tanggal Akhir</label>
                    <input type="date" name="end_date" value="{{ $endDate }}" class="form-input">
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Status Pembayaran</label>
                    <select name="payment_status" class="form-input">
                        <option value="all" {{ $paymentStatus === 'all' ? 'selected' : '' }}>Semua</option>
                        <option value="paid" {{ $paymentStatus === 'paid' ? 'selected' : '' }}>Lunas</option>
                        <option value="unpaid" {{ $paymentStatus === 'unpaid' ? 'selected' : '' }}>Belum Lunas</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Tipe Denda</label>
                    <select name="fine_type" class="form-input">
                        <option value="all" {{ $fineType === 'all' ? 'selected' : '' }}>Semua</option>
                        <option value="late" {{ $fineType === 'late' ? 'selected' : '' }}>Keterlambatan</option>
                        <option value="lost" {{ $fineType === 'lost' ? 'selected' : '' }}>Buku Hilang</option>
                    </select>
                </div>

                <div>
                    <button type="submit" class="btn btn-primary">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        Tampilkan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow p-4">
            <div class="text-sm text-slate-500">Total Denda</div>
            <div class="text-xl font-bold text-slate-800">{{ $summary['total_fines'] }}</div>
            <div class="text-sm text-slate-600">Rp {{ number_format($summary['total_amount'], 0, ',', '.') }}</div>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <div class="text-sm text-slate-500">Sudah Dibayar</div>
            <div class="text-xl font-bold text-emerald-600">{{ $summary['paid_count'] }}</div>
            <div class="text-sm text-emerald-600">Rp {{ number_format($summary['paid_amount'], 0, ',', '.') }}</div>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <div class="text-sm text-slate-500">Belum Dibayar</div>
            <div class="text-xl font-bold text-red-600">{{ $summary['unpaid_count'] }}</div>
            <div class="text-sm text-red-600">Rp {{ number_format($summary['unpaid_amount'], 0, ',', '.') }}</div>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <div class="text-sm text-slate-500">Keterlambatan / Hilang</div>
            <div class="text-xl font-bold text-amber-600">{{ $summary['late_fines_count'] }} / {{ $summary['lost_fines_count'] }}</div>
            <div class="text-sm text-slate-600">Rp {{ number_format($summary['late_fines_amount'], 0, ',', '.') }} / Rp {{ number_format($summary['lost_fines_amount'], 0, ',', '.') }}</div>
        </div>
    </div>

    <div class="card">
        <div class="card-header flex justify-between items-center">
            <h3 class="text-lg font-semibold">Data Denda - {{ $periodLabel }}</h3>
            <div class="flex gap-2">
                <a href="{{ route('reports.fines.export-excel', request()->query()) }}" class="btn btn-success btn-sm">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Excel
                </a>
                <a href="{{ route('reports.fines.export-pdf', request()->query()) }}" class="btn btn-danger btn-sm">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    PDF
                </a>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="overflow-x-auto">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>NIS</th>
                            <th>Nama Siswa</th>
                            <th>Judul Buku</th>
                            <th>Tipe</th>
                            <th>Hari Terlambat</th>
                            <th>Jumlah</th>
                            <th>Status</th>
                            <th>Tanggal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($fines as $index => $fine)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $fine->student->nis ?? '-' }}</td>
                                <td>{{ $fine->student->name ?? '-' }}</td>
                                <td>{{ $fine->loan->bookCopy->book->title ?? '-' }}</td>
                                <td>
                                    @if($fine->type === 'late')
                                        <span class="badge badge-amber">Keterlambatan</span>
                                    @else
                                        <span class="badge badge-red">Buku Hilang</span>
                                    @endif
                                </td>
                                <td>{{ $fine->days_overdue ?? 0 }} hari</td>
                                <td class="font-semibold">Rp {{ number_format($fine->amount, 0, ',', '.') }}</td>
                                <td>
                                    @if($fine->is_paid)
                                        <span class="badge badge-green">Lunas</span>
                                    @else
                                        <span class="badge badge-red">Belum Lunas</span>
                                    @endif
                                </td>
                                <td>{{ $fine->created_at?->format('d/m/Y') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center py-8 text-slate-500">
                                    Tidak ada data denda untuk periode ini
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
