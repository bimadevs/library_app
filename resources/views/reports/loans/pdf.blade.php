<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Peminjaman - {{ $periodLabel }}</title>
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
            display: flex;
            margin-bottom: 20px;
        }
        .summary-item {
            display: inline-block;
            margin-right: 30px;
            padding: 10px;
            background: #f5f5f5;
            border-radius: 4px;
        }
        .summary-label {
            font-size: 10px;
            color: #666;
        }
        .summary-value {
            font-size: 16px;
            font-weight: bold;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #4a5568;
            color: white;
            font-weight: bold;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .status-active { color: #3182ce; }
        .status-returned { color: #38a169; }
        .status-overdue { color: #d69e2e; }
        .status-lost { color: #e53e3e; }
        .footer {
            margin-top: 30px;
            text-align: right;
            font-size: 10px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>LAPORAN PEMINJAMAN BUKU</h1>
        <p>Periode: {{ $periodLabel }}</p>
    </div>

    <div class="summary">
        <div class="summary-item">
            <div class="summary-label">Total Peminjaman</div>
            <div class="summary-value">{{ $summary['total_loans'] }}</div>
        </div>
        <div class="summary-item">
            <div class="summary-label">Aktif</div>
            <div class="summary-value">{{ $summary['active_loans'] }}</div>
        </div>
        <div class="summary-item">
            <div class="summary-label">Dikembalikan</div>
            <div class="summary-value">{{ $summary['returned_loans'] }}</div>
        </div>
        <div class="summary-item">
            <div class="summary-label">Terlambat</div>
            <div class="summary-value">{{ $summary['overdue_loans'] }}</div>
        </div>
        <div class="summary-item">
            <div class="summary-label">Hilang</div>
            <div class="summary-value">{{ $summary['lost_loans'] }}</div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>NIS</th>
                <th>Nama Siswa</th>
                <th>Judul Buku</th>
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
                    <td>{{ $loan->loan_date?->format('d/m/Y') }}</td>
                    <td>{{ $loan->due_date?->format('d/m/Y') }}</td>
                    <td>{{ $loan->return_date?->format('d/m/Y') ?? '-' }}</td>
                    <td>
                        @switch($loan->status)
                            @case('active')
                                <span class="status-active">Aktif</span>
                                @break
                            @case('returned')
                                <span class="status-returned">Dikembalikan</span>
                                @break
                            @case('overdue')
                                <span class="status-overdue">Terlambat</span>
                                @break
                            @case('lost')
                                <span class="status-lost">Hilang</span>
                                @break
                            @default
                                {{ $loan->status }}
                        @endswitch
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" style="text-align: center;">Tidak ada data peminjaman</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        Dicetak pada: {{ now()->format('d/m/Y H:i:s') }}
    </div>
</body>
</html>
