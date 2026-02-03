<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-800 leading-tight">
            {{ __('Laporan Peminjaman') }}
        </h2>
    </x-slot>

    <div class="space-y-6">
        <!-- Filter Section -->
        <div class="card">
            <div class="card-body">
                <form method="GET" action="{{ route('reports.loans') }}" class="grid grid-cols-1 md:grid-cols-12 gap-4 items-end">
                    <div class="md:col-span-3">
                        <label class="form-label">Tipe Laporan</label>
                        <select name="report_type" id="report_type" class="form-input w-full" onchange="toggleDateInputs()">
                            <option value="daily" {{ $reportType === 'daily' ? 'selected' : '' }}>Harian</option>
                            <option value="monthly" {{ $reportType === 'monthly' ? 'selected' : '' }}>Bulanan</option>
                        </select>
                    </div>

                    <div id="daily_input" class="md:col-span-4 {{ $reportType === 'monthly' ? 'hidden' : '' }}">
                        <label class="form-label">Tanggal</label>
                        <input type="date" name="date" value="{{ $date }}" class="form-input w-full">
                    </div>

                    <div id="monthly_input" class="md:col-span-4 {{ $reportType === 'daily' ? 'hidden' : '' }}">
                        <label class="form-label">Bulan</label>
                        <input type="month" name="month" value="{{ $month }}" class="form-input w-full">
                    </div>

                    <div class="md:col-span-5 flex items-end">
                        <button type="submit" class="btn btn-primary w-full md:w-auto justify-center">
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
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
            <div class="stat-card bg-white">
                <div class="stat-label">Total Peminjaman</div>
                <div class="stat-value text-slate-800">{{ $summary['total_loans'] }}</div>
            </div>
            <div class="stat-card bg-white">
                <div class="stat-label">Aktif</div>
                <div class="stat-value text-blue-600">{{ $summary['active_loans'] }}</div>
            </div>
            <div class="stat-card bg-white">
                <div class="stat-label">Dikembalikan</div>
                <div class="stat-value text-emerald-600">{{ $summary['returned_loans'] }}</div>
            </div>
            <div class="stat-card bg-white">
                <div class="stat-label">Terlambat</div>
                <div class="stat-value text-amber-600">{{ $summary['overdue_loans'] }}</div>
            </div>
            <div class="stat-card bg-white">
                <div class="stat-label">Hilang</div>
                <div class="stat-value text-red-600">{{ $summary['lost_loans'] }}</div>
            </div>
        </div>

        <!-- Data Section -->
        <div class="card">
            <div class="card-header flex flex-col md:flex-row justify-between items-center gap-4">
                <h3 class="card-title">Data Peminjaman ({{ $periodLabel }})</h3>
                <div class="flex gap-2">
                    <a href="{{ route('reports.loans.export-excel', request()->query()) }}" class="btn btn-secondary">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        Excel
                    </a>
                    <a href="{{ route('reports.loans.export-pdf', request()->query()) }}" class="btn btn-secondary">
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
                                <th>Barcode</th>
                                <th>Tgl Pinjam</th>
                                <th>Jatuh Tempo</th>
                                <th>Tgl Kembali</th>
                                <th class="text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($loans as $index => $loan)
                                <tr class="hover:bg-slate-50">
                                    <td class="text-center">{{ $index + 1 }}</td>
                                    <td class="font-mono text-sm text-slate-600">{{ $loan->student->nis ?? '-' }}</td>
                                    <td class="font-medium text-slate-800">{{ $loan->student->name ?? '-' }}</td>
                                    <td>{{ $loan->bookCopy->book->title ?? '-' }}</td>
                                    <td class="font-mono text-sm">{{ $loan->bookCopy->barcode ?? '-' }}</td>
                                    <td>{{ $loan->loan_date?->format('d/m/Y') }}</td>
                                    <td>{{ $loan->due_date?->format('d/m/Y') }}</td>
                                    <td>{{ $loan->return_date?->format('d/m/Y') ?? '-' }}</td>
                                    <td class="text-center">
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
                                    <td colspan="9" class="text-center py-12">
                                        <div class="flex flex-col items-center justify-center text-slate-500">
                                            <svg class="w-12 h-12 mb-3 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                                            </svg>
                                            <p>Tidak ada data peminjaman untuk periode ini.</p>
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
