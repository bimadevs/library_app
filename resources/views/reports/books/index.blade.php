<x-app-layout>
    <x-slot name="header">
        Laporan Buku
    </x-slot>

    <div class="card mb-6">
        <div class="card-body">
            <form method="GET" action="{{ route('reports.books') }}" class="flex flex-wrap gap-4 items-end">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Tipe Laporan</label>
                    <select name="report_type" id="report_type" class="form-input" onchange="toggleDateInputs()">
                        <option value="top_borrowed" {{ $reportType === 'top_borrowed' ? 'selected' : '' }}>Buku Terpopuler</option>
                        <option value="never_borrowed" {{ $reportType === 'never_borrowed' ? 'selected' : '' }}>Tidak Pernah Dipinjam</option>
                    </select>
                </div>

                <div id="date_inputs" class="{{ $reportType === 'never_borrowed' ? 'hidden' : '' }}">
                    <div class="flex gap-2">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Tanggal Mulai</label>
                            <input type="date" name="start_date" value="{{ $startDate }}" class="form-input">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Tanggal Akhir</label>
                            <input type="date" name="end_date" value="{{ $endDate }}" class="form-input">
                        </div>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Kategori</label>
                    <select name="category_id" class="form-input">
                        <option value="all">Semua Kategori</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ $categoryId == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Jumlah Data</label>
                    <select name="limit" class="form-input">
                        <option value="10" {{ $limit == 10 ? 'selected' : '' }}>10</option>
                        <option value="20" {{ $limit == 20 ? 'selected' : '' }}>20</option>
                        <option value="50" {{ $limit == 50 ? 'selected' : '' }}>50</option>
                        <option value="100" {{ $limit == 100 ? 'selected' : '' }}>100</option>
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

    <div class="card">
        <div class="card-header flex justify-between items-center">
            <h3 class="text-lg font-semibold">
                @if($reportType === 'top_borrowed')
                    Buku Terpopuler - {{ $periodLabel }}
                @else
                    Buku Tidak Pernah Dipinjam
                @endif
            </h3>
            <div class="flex gap-2">
                <a href="{{ route('reports.books.export-excel', request()->query()) }}" class="btn btn-success btn-sm">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Excel
                </a>
                <a href="{{ route('reports.books.export-pdf', request()->query()) }}" class="btn btn-danger btn-sm">
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
                            <th>Kode</th>
                            <th>Judul</th>
                            <th>Pengarang</th>
                            <th>Kategori</th>
                            <th>Stok</th>
                            <th>Tanggal Masuk</th>
                            @if($reportType === 'top_borrowed')
                                <th>Jumlah Pinjam</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($books as $index => $book)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td class="font-mono">{{ $book->code }}</td>
                                <td>{{ $book->title }}</td>
                                <td>{{ $book->author }}</td>
                                <td>{{ $book->category->name ?? '-' }}</td>
                                <td>{{ $book->stock }}</td>
                                <td>{{ $book->entry_date?->format('d/m/Y') ?? '-' }}</td>
                                @if($reportType === 'top_borrowed')
                                    <td>
                                        <span class="badge badge-blue">{{ $book->loan_count ?? 0 }} kali</span>
                                    </td>
                                @endif
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ $reportType === 'top_borrowed' ? 8 : 7 }}" class="text-center py-8 text-slate-500">
                                    Tidak ada data buku
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
            const dateInputs = document.getElementById('date_inputs');
            
            if (reportType === 'never_borrowed') {
                dateInputs.classList.add('hidden');
            } else {
                dateInputs.classList.remove('hidden');
            }
        }
    </script>
</x-app-layout>
