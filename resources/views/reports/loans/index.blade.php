<x-app-layout>
    <x-slot name="header">
        Laporan Peminjaman
    </x-slot>

    <div class="card mb-6">
        <div class="card-body">
            <form method="GET" action="{{ route('reports.loans') }}" class="flex flex-wrap gap-4 items-end">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Tipe Laporan</label>
                    <select name="report_type" id="report_type" class="form-input" onchange="toggleDateInputs()">
                        <option value="daily" {{ $reportType === 'daily' ? 'selected' : '' }}>Harian</option>
                        <option value="monthly" {{ $reportType === 'monthly' ? 'selected' : '' }}>Bulanan</option>
                    </select>
                </div>

                <div id="daily_input" class="{{ $reportType === 'monthly' ? 'hidden' : '' }}">
                    <label class="block text-sm font-medium text-slate-700 mb-1">Tanggal</label>
                    <input type="date" name="date" value="{{ $date }}" class="form-input">
                </div>

                <div id="monthly_input" class="{{ $reportType === 'daily' ? 'hidden' : '' }}">
                    <label class="block text-sm font-medium text-slate-700 mb-1">Bulan</label>
                    <input type="month" name="month" value="{{ $month }}" class="form-input">
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
    <div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow p-4">
            <div class="text-sm text-slate-500">Total Peminjaman</div>
            <div class="text-2xl font-bold text-slate-800">{{ $summary['total_loans'] }}</div>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <div class="text-sm text-slate-500">Aktif</div>
            <div class="text-2xl font-bold text-blue-600">{{ $summary['active_loans'] }}</div>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <div class="text-sm text-slate-500">Dikembalikan</div>
            <div class="text-2xl font-bold text-emerald-600">{{ $summary['returned_loans'] }}</div>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <div class="text-sm text-slate-500">Terlambat</div>
            <div class="text-2xl font-bold text-amber-600">{{ $summary['overdue_loans'] }}</div>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <div class="text-sm text-slate-500">Hilang</div>
            <div class="text-2xl font-bold text-red-600">{{ $summary['lost_loans'] }}</div>
        </div>
    </div>

    <div class="card">
        <div class="card-header flex justify-between items-center">
            <h3 class="text-lg font-semibold">Data Peminjaman - {{ $periodLabel }}</h3>
            <div class="flex gap-2">
                <a href="{{ route('reports.loans.export-excel', request()->query()) }}" class="btn btn-success btn-sm">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Excel
                </a>
                <a href="{{ route('reports.loans.export-pdf', request()->query()) }}" class="btn btn-danger btn-sm">
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
                            <th>Barcode</th>
                            <th>Tgl Pinjam</th>
                            <th>Jatuh Tempo</th>
                            <th>Tgl Kembali</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($loans as $index => $loan)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $loan->student->nis ?? '-' }}</td>
                                <td>{{ $loan->student->name ?? '-' }}</td>
                                <td>{{ $loan->bookCopy->book->title ?? '-' }}</td>
                                <td class="font-mono text-sm">{{ $loan->bookCopy->barcode ?? '-' }}</td>
                                <td>{{ $loan->loan_date?->format('d/m/Y') }}</td>
                                <td>{{ $loan->due_date?->format('d/m/Y') }}</td>
                                <td>{{ $loan->return_date?->format('d/m/Y') ?? '-' }}</td>
                                <td>
                                    @switch($loan->status)
                                        @case('active')
                                            <span class="badge badge-blue">Aktif</span>
                                            @break
                                        @case('returned')
                                            <span class="badge badge-green">Dikembalikan</span>
                                            @break
                                        @case('overdue')
                                            <span class="badge badge-amber">Terlambat</span>
                                            @break
                                        @case('lost')
                                            <span class="badge badge-red">Hilang</span>
                                            @break
                                        @default
                                            <span class="badge">{{ $loan->status }}</span>
                                    @endswitch
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center py-8 text-slate-500">
                                    Tidak ada data peminjaman untuk periode ini
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        function toggleDateInputs() {
            const reportType = document.getElementById('report_type').value;
            const dailyInput = document.getElementById('daily_input');
            const monthlyInput = document.getElementById('monthly_input');
            
            if (reportType === 'daily') {
                dailyInput.classList.remove('hidden');
                monthlyInput.classList.add('hidden');
            } else {
                dailyInput.classList.add('hidden');
                monthlyInput.classList.remove('hidden');
            }
        }
    </script>
</x-app-layout>
