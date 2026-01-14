<div class="card">
    <div class="card-header flex items-center justify-between">
        <span>Denda Belum Lunas</span>
        <span class="badge badge-danger">{{ $count }}</span>
    </div>
    
    @if($fines->isEmpty())
        <div class="text-center py-8 text-slate-400">
            <svg class="w-12 h-12 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <p>Tidak ada denda yang belum lunas</p>
        </div>
    @else
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-slate-200">
                        <th class="text-left py-3 px-4 text-sm font-medium text-slate-600">Siswa</th>
                        <th class="text-left py-3 px-4 text-sm font-medium text-slate-600">Jumlah</th>
                        <th class="text-left py-3 px-4 text-sm font-medium text-slate-600">Alasan</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($fines as $fine)
                        <tr class="border-b border-slate-100 hover:bg-slate-50">
                            <td class="py-3 px-4">
                                <div class="font-medium text-slate-800">{{ $fine->student->name }}</div>
                                <div class="text-sm text-slate-500">{{ $fine->student->nis }}</div>
                            </td>
                            <td class="py-3 px-4">
                                <span class="font-medium text-red-600">Rp {{ number_format($fine->amount, 0, ',', '.') }}</span>
                            </td>
                            <td class="py-3 px-4">
                                @if($fine->type === 'late')
                                    <span class="badge badge-warning">Terlambat {{ $fine->days_overdue }} hari</span>
                                @else
                                    <span class="badge badge-danger">Buku Hilang</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        <div class="border-t border-slate-200 px-4 py-3 bg-slate-50">
            <div class="flex justify-between items-center">
                <span class="text-sm text-slate-600">Total Denda Belum Lunas:</span>
                <span class="font-bold text-red-600">Rp {{ number_format($totalAmount, 0, ',', '.') }}</span>
            </div>
        </div>
    @endif
</div>
