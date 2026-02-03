<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-800 leading-tight">
            {{ __('Laporan Buku') }}
        </h2>
    </x-slot>

    <div class="space-y-6">
        <!-- Filter Section -->
        <div class="card">
            <div class="card-body">
                <form method="GET" action="{{ route('reports.books') }}" class="grid grid-cols-1 md:grid-cols-12 gap-4 items-end">
                    <div class="md:col-span-3">
                        <label class="form-label">Tipe Laporan</label>
                        <select name="report_type" id="report_type" class="form-input w-full" onchange="toggleDateInputs()">
                            <option value="top_borrowed" {{ $reportType === 'top_borrowed' ? 'selected' : '' }}>Buku Terpopuler</option>
                            <option value="never_borrowed" {{ $reportType === 'never_borrowed' ? 'selected' : '' }}>Tidak Pernah Dipinjam</option>
                        </select>
                    </div>

                    <div id="date_inputs_container" class="md:col-span-4 grid grid-cols-2 gap-2 {{ $reportType === 'never_borrowed' ? 'hidden' : '' }}">
                        <div>
                            <label class="form-label">Dari Tanggal</label>
                            <input type="date" name="start_date" value="{{ $startDate }}" class="form-input w-full">
                        </div>
                        <div>
                            <label class="form-label">Sampai Tanggal</label>
                            <input type="date" name="end_date" value="{{ $endDate }}" class="form-input w-full">
                        </div>
                    </div>

                    <div class="md:col-span-3">
                        <label class="form-label">Kategori</label>
                        <select name="category_id" class="form-input w-full">
                            <option value="all">Semua Kategori</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ $categoryId == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="md:col-span-1">
                        <label class="form-label">Limit</label>
                        <select name="limit" class="form-input w-full">
                            <option value="10" {{ $limit == 10 ? 'selected' : '' }}>10</option>
                            <option value="20" {{ $limit == 20 ? 'selected' : '' }}>20</option>
                            <option value="50" {{ $limit == 50 ? 'selected' : '' }}>50</option>
                            <option value="100" {{ $limit == 100 ? 'selected' : '' }}>100</option>
                        </select>
                    </div>

                    <div class="md:col-span-1">
                        <button type="submit" class="btn btn-primary w-full justify-center">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Data Section -->
        <div class="card">
            <div class="card-header flex flex-col md:flex-row justify-between items-center gap-4">
                <h3 class="card-title">
                    @if($reportType === 'top_borrowed')
                        Buku Terpopuler ({{ $periodLabel }})
                    @else
                        Buku Tidak Pernah Dipinjam
                    @endif
                </h3>
                <div class="flex gap-2">
                    <a href="{{ route('reports.books.export-excel', request()->query()) }}" class="btn btn-secondary">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        Excel
                    </a>
                    <a href="{{ route('reports.books.export-pdf', request()->query()) }}" class="btn btn-secondary">
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
                                <th>Kode</th>
                                <th>Judul Buku</th>
                                <th>Pengarang</th>
                                <th>Kategori</th>
                                <th class="text-center">Stok</th>
                                <th>Tgl Masuk</th>
                                @if($reportType === 'top_borrowed')
                                    <th class="text-center">Total Pinjam</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($books as $index => $book)
                                <tr class="hover:bg-slate-50">
                                    <td class="text-center">{{ $index + 1 }}</td>
                                    <td class="font-mono text-sm font-medium text-slate-600">{{ $book->code }}</td>
                                    <td>
                                        <div class="font-medium text-slate-800">{{ $book->title }}</div>
                                    </td>
                                    <td class="text-slate-600">{{ $book->author }}</td>
                                    <td>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-slate-100 text-slate-800">
                                            {{ $book->category->name ?? '-' }}
                                        </span>
                                    </td>
                                    <td class="text-center">{{ $book->stock }}</td>
                                    <td>{{ $book->entry_date?->format('d/m/Y') ?? '-' }}</td>
                                    @if($reportType === 'top_borrowed')
                                        <td class="text-center">
                                            <span class="badge badge-blue">
                                                {{ $book->loan_count ?? 0 }}
                                            </span>
                                        </td>
                                    @endif
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="{{ $reportType === 'top_borrowed' ? 8 : 7 }}" class="text-center py-12">
                                        <div class="flex flex-col items-center justify-center text-slate-500">
                                            <svg class="w-12 h-12 mb-3 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                            </svg>
                                            <p>Tidak ada data buku ditemukan.</p>
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
            const container = document.getElementById('date_inputs_container');
            
            if (reportType === 'never_borrowed') {
                container.classList.add('hidden');
            } else {
                container.classList.remove('hidden');
            }
        }
    </script>
</x-app-layout>
