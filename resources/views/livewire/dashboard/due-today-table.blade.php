<div class="card h-full">
    <div class="card-header">
        <span>Jatuh Tempo Hari Ini</span>
        <span class="badge badge-warning">{{ $count }}</span>
    </div>
    
    @if($loans->isEmpty())
        <div class="text-center py-8 text-slate-400">
            <svg class="w-12 h-12 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
            </svg>
            <p>Tidak ada peminjaman jatuh tempo hari ini</p>
        </div>
    @else
        <div class="data-table-container">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Peminjam</th>
                        <th>Judul Buku</th>
                        <th>Jatuh Tempo</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($loans as $loan)
                        <tr>
                            <td>
                                <div class="font-medium text-slate-900">{{ $loan->student->name }}</div>
                                <div class="text-xs text-slate-500">{{ $loan->student->nis }}</div>
                            </td>
                            <td>
                                <div class="font-medium text-slate-900">{{ $loan->bookCopy->book->title }}</div>
                                <div class="text-xs text-slate-500">{{ $loan->bookCopy->barcode }}</div>
                            </td>
                            <td>
                                <span class="badge badge-warning">{{ $loan->due_date->format('d/m/Y') }}</span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
