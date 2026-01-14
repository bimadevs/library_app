<div class="card">
    <div class="card-header flex items-center justify-between">
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
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-slate-200">
                        <th class="text-left py-3 px-4 text-sm font-medium text-slate-600">Peminjam</th>
                        <th class="text-left py-3 px-4 text-sm font-medium text-slate-600">Judul Buku</th>
                        <th class="text-left py-3 px-4 text-sm font-medium text-slate-600">Jatuh Tempo</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($loans as $loan)
                        <tr class="border-b border-slate-100 hover:bg-slate-50">
                            <td class="py-3 px-4">
                                <div class="font-medium text-slate-800">{{ $loan->student->name }}</div>
                                <div class="text-sm text-slate-500">{{ $loan->student->nis }}</div>
                            </td>
                            <td class="py-3 px-4">
                                <div class="text-slate-800">{{ $loan->bookCopy->book->title }}</div>
                                <div class="text-sm text-slate-500">{{ $loan->bookCopy->barcode }}</div>
                            </td>
                            <td class="py-3 px-4">
                                <span class="badge badge-warning">{{ $loan->due_date->format('d/m/Y') }}</span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
