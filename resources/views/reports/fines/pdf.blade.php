<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Denda - {{ $periodLabel }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }
        .header h1 {
            margin: 0;
            font-size: 18px;
        }
        .header p {
            margin: 5px 0 0;
            color: #666;
        }
        .summary {
            margin-bottom: 20px;
        }
        .summary-row {
            display: flex;
            margin-bottom: 10px;
        }
        .summary-item {
            display: inline-block;
            margin-right: 20px;
            padding: 8px 12px;
            background: #f5f5f5;
            border-radius: 4px;
        }
        .summary-label {
            font-size: 10px;
            color: #666;
        }
        .summary-value {
            font-size: 14px;
            font-weight: bold;
        }
        .summary-amount {
            font-size: 11px;
            color: #333;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 6px;
            text-align: left;
            font-size: 11px;
        }
        th {
            background-color: #4a5568;
            color: white;
            font-weight: bold;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .type-late { color: #d69e2e; }
        .type-lost { color: #e53e3e; }
        .status-paid { color: #38a169; }
        .status-unpaid { color: #e53e3e; }
        .amount { text-align: right; }
        .footer {
            margin-top: 30px;
            text-align: right;
            font-size: 10px;
            color: #666;
        }
        .totals {
            margin-top: 20px;
            border-top: 2px solid #333;
            padding-top: 10px;
        }
        .totals-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 5px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>LAPORAN DENDA PERPUSTAKAAN</h1>
        <p>Periode: {{ $periodLabel }}</p>
    </div>

    <div class="summary">
        <div class="summary-row">
            <div class="summary-item">
                <div class="summary-label">Total Denda</div>
                <div class="summary-value">{{ $summary['total_fines'] }}</div>
                <div class="summary-amount">Rp {{ number_format($summary['total_amount'], 0, ',', '.') }}</div>
            </div>
            <div class="summary-item">
                <div class="summary-label">Sudah Dibayar</div>
                <div class="summary-value" style="color: #38a169;">{{ $summary['paid_count'] }}</div>
                <div class="summary-amount">Rp {{ number_format($summary['paid_amount'], 0, ',', '.') }}</div>
            </div>
            <div class="summary-item">
                <div class="summary-label">Belum Dibayar</div>
                <div class="summary-value" style="color: #e53e3e;">{{ $summary['unpaid_count'] }}</div>
                <div class="summary-amount">Rp {{ number_format($summary['unpaid_amount'], 0, ',', '.') }}</div>
            </div>
        </div>
        <div class="summary-row">
            <div class="summary-item">
                <div class="summary-label">Denda Keterlambatan</div>
                <div class="summary-value">{{ $summary['late_fines_count'] }}</div>
                <div class="summary-amount">Rp {{ number_format($summary['late_fines_amount'], 0, ',', '.') }}</div>
            </div>
            <div class="summary-item">
                <div class="summary-label">Kompensasi Buku Hilang</div>
                <div class="summary-value">{{ $summary['lost_fines_count'] }}</div>
                <div class="summary-amount">Rp {{ number_format($summary['lost_fines_amount'], 0, ',', '.') }}</div>
            </div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>NIS</th>
                <th>Nama Siswa</th>
                <th>Judul Buku</th>
                <th>Tipe</th>
                <th>Hari</th>
                <th>Jumlah</th>
                <th>Status Pembayaran</th>
                <th>Tanggal Bayar</th>
                <th>Tanggal Denda</th>
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
                            <span class="type-late">Keterlambatan</span>
                        @else
                            <span class="type-lost">Buku Hilang</span>
                        @endif
                    </td>
                    <td>{{ $fine->days_overdue ?? 0 }}</td>
                    <td class="amount">Rp {{ number_format($fine->amount, 0, ',', '.') }}</td>
                    <td>
                        @if($fine->is_paid)
                            <span class="status-paid">Lunas</span>
                        @else
                            <span class="status-unpaid">Belum Lunas</span>
                        @endif
                    </td>
                    <td>
                        @if($fine->is_paid && $fine->paid_at)
                            {{ \Carbon\Carbon::parse($fine->paid_at)->format('d/m/Y H:i') }}
                        @else
                            -
                        @endif
                    </td>
                    <td>{{ $fine->created_at?->format('d/m/Y') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" style="text-align: center;">Tidak ada data denda</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        Dicetak pada: {{ now()->format('d/m/Y H:i:s') }}
    </div>
</body>
</html>
