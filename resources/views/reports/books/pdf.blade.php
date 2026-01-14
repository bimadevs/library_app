<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $title }}</title>
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
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
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
        .loan-count {
            text-align: center;
            font-weight: bold;
            color: #3182ce;
        }
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
        <h1>{{ $title }}</h1>
        @if($reportType === 'top_borrowed')
            <p>Periode: {{ $periodLabel }}</p>
        @endif
    </div>

    <table>
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
                    <td>{{ $book->code }}</td>
                    <td>{{ $book->title }}</td>
                    <td>{{ $book->author }}</td>
                    <td>{{ $book->category->name ?? '-' }}</td>
                    <td>{{ $book->stock }}</td>
                    <td>{{ $book->entry_date?->format('d/m/Y') ?? '-' }}</td>
                    @if($reportType === 'top_borrowed')
                        <td class="loan-count">{{ $book->loan_count ?? 0 }}</td>
                    @endif
                </tr>
            @empty
                <tr>
                    <td colspan="{{ $reportType === 'top_borrowed' ? 8 : 7 }}" style="text-align: center;">
                        Tidak ada data buku
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        Dicetak pada: {{ now()->format('d/m/Y H:i:s') }}
    </div>
</body>
</html>
