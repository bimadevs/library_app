<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-800 leading-tight">
            {{ __('Laporan Denda') }}
        </h2>
    </x-slot>

    <div class="space-y-6">
        <!-- Filter Section -->
        <div class="card">
            <div class="card-body">
                <form method="GET" action="{{ route('reports.fines') }}" class="grid grid-cols-1 md:grid-cols-12 gap-4 items-end">
                    <div class="md:col-span-3">
                        <label class="form-label">Tanggal Mulai</label>
                        <input type="date" name="start_date" value="{{ $startDate }}" class="form-input w-full">
                    </div>

                    <div class="md:col-span-3">
                        <label class="form-label">Tanggal Akhir</label>
                        <input type="date" name="end_date" value="{{ $endDate }}" class="form-input w-full">
                    </div>

                    <div class="md:col-span-2">
                        <label class="form-label">Status Pembayaran</label>
                        <select name="payment_status" class="form-input w-full">
                            <option value="all" {{ $paymentStatus === 'all' ? 'selected' : '' }}>Semua</option>
                            <option value="paid" {{ $paymentStatus === 'paid' ? 'selected' : '' }}>Lunas</option>
                            <option value="unpaid" {{ $paymentStatus === 'unpaid' ? 'selected' : '' }}>Belum Lunas</option>
                        </select>
                    </div>

                    <div class="md:col-span-2">
                        <label class="form-label">Tipe Denda</label>
                        <select name="fine_type" class="form-input w-full">
                            <option value="all" {{ $fineType === 'all' ? 'selected' : '' }}>Semua</option>
                            <option value="late" {{ $fineType === 'late' ? 'selected' : '' }}>Keterlambatan</option>
                            <option value="lost" {{ $fineType === 'lost' ? 'selected' : '' }}>Buku Hilang</option>
                        </select>
                    </div>

                    <div class="md:col-span-2">
                        <button type="submit" class="btn btn-primary w-full justify-center">
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
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="stat-card bg-white">
                <div class="stat-label">Total Denda</div>
                <div class="stat-value text-slate-800">{{ $summary['total_fines'] }}</div>
                <div class="stat-desc text-slate-600">Rp {{ number_format($summary['total_amount'], 0, ',', '.') }}</div>
            </div>
            <div class="stat-card bg-white">
                <div class="stat-label">Sudah Dibayar</div>
                <div class="stat-value text-emerald-600">{{ $summary['paid_count'] }}</div>
                <div class="stat-desc text-emerald-600">Rp {{ number_format($summary['paid_amount'], 0, ',', '.') }}</div>
            </div>
            <div class="stat-card bg-white">
                <div class="stat-label">Belum Dibayar</div>
                <div class="stat-value text-red-600">{{ $summary['unpaid_count'] }}</div>
                <div class="stat-desc text-red-600">Rp {{ number_format($summary['unpaid_amount'], 0, ',', '.') }}</div>
            </div>
            <div class="stat-card bg-white">
                <div class="stat-label">Keterlambatan / Hilang</div>
                <div class="stat-value text-amber-600">{{ $summary['late_fines_count'] }} / {{ $summary['lost_fines_count'] }}</div>
                <div class="stat-desc text-slate-600">Rp {{ number_format($summary['late_fines_amount'], 0, ',', '.') }} / Rp {{ number_format($summary['lost_fines_amount'], 0, ',', '.') }}</div>
            </div>
        </div>

        <!-- Data Section -->
        <div class="card">
            <div class="card-header flex flex-col md:flex-row justify-between items-center gap-4">
                <h3 class="card-title">Data Denda ({{ $periodLabel }})</h3>
                <div class="flex gap-2">
                    <a href="{{ route('reports.fines.export-excel', request()->query()) }}" class="btn btn-secondary">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        Excel
                    </a>
                    <a href="{{ route('reports.fines.export-pdf', request()->query()) }}" class="btn btn-secondary">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
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
                                <th class="w-12 text-center">No</th>
                                <th>NIS</th>
                                <th>Nama Siswa</th>
                                <th>Judul Buku</th>
                                <th class="text-center">Tipe</th>
                                <th class="text-center">Hari Terlambat</th>
                                <th class="text-right">Jumlah</th>
                                <th class="text-center">Status Pembayaran</th>
                                <th>Tanggal Denda</th>
                                <th>Tanggal Bayar</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($fines as $index => $fine)
                                <tr class="hover:bg-slate-50">
                                    <td class="text-center">{{ $index + 1 }}</td>
                                    <td class="font-mono text-sm text-slate-600">{{ $fine->student->nis ?? '-' }}</td>
                                    <td class="font-medium text-slate-800">{{ $fine->student->name ?? '-' }}</td>
                                    <td>{{ $fine->loan->bookCopy->book->title ?? '-' }}</td>
                                    <td class="text-center">
                                        @if($fine->type === 'late')
                                            <span class="badge badge-amber">Keterlambatan</span>
                                        @else
                                            <span class="badge badge-red">Buku Hilang</span>
                                        @endif
                                    </td>
                                    <td class="text-center">{{ $fine->days_overdue ?? 0 }} hari</td>
                                    <td class="text-right font-medium text-slate-800">Rp {{ number_format($fine->amount, 0, ',', '.') }}</td>
                                    <td class="text-center">
                                        @if($fine->is_paid)
                                            <span class="badge badge-success">Lunas</span>
                                        @else
                                            <span class="badge badge-danger">Belum Lunas</span>
                                        @endif
                                    </td>
                                    <td>{{ $fine->created_at?->format('d/m/Y') }}</td>
                                    <td>
                                        @if($fine->is_paid && $fine->paid_at)
                                            {{ \Carbon\Carbon::parse($fine->paid_at)->format('d/m/Y H:i') }}
                                        @elseif($fine->is_paid)
                                            <span class="text-slate-400 text-xs italic">(Manual)</span>
                                        @else
                                            -
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="10" class="text-center py-12">
                                        <div class="flex flex-col items-center justify-center text-slate-500">
                                            <svg class="w-12 h-12 mb-3 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                            <p>Tidak ada data denda untuk periode ini.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
