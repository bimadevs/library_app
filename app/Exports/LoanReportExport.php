<?php

namespace App\Exports;

use App\Models\Loan;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class LoanReportExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle
{
    protected string $reportType;
    protected string $date;
    protected string $month;

    public function __construct(string $reportType, string $date, string $month)
    {
        $this->reportType = $reportType;
        $this->date = $date;
        $this->month = $month;
    }

    public function collection()
    {
        $query = Loan::with(['student', 'bookCopy.book', 'fine']);

        if ($this->reportType === 'daily') {
            $query->whereDate('loan_date', $this->date);
        } else {
            $startOfMonth = Carbon::parse($this->month . '-01')->startOfMonth();
            $endOfMonth = Carbon::parse($this->month . '-01')->endOfMonth();
            $query->whereBetween('loan_date', [$startOfMonth, $endOfMonth]);
        }

        return $query->orderBy('loan_date', 'desc')->get();
    }

    public function headings(): array
    {
        return [
            'No',
            'NIS',
            'Nama Siswa',
            'Kode Buku',
            'Judul Buku',
            'Barcode',
            'Tanggal Pinjam',
            'Tanggal Jatuh Tempo',
            'Tanggal Kembali',
            'Tipe Pinjaman',
            'Status',
            'Denda (Rp)',
        ];
    }

    public function map($loan): array
    {
        static $no = 0;
        $no++;

        $statusLabels = [
            'active' => 'Aktif',
            'returned' => 'Dikembalikan',
            'overdue' => 'Terlambat',
            'lost' => 'Hilang',
        ];

        $loanTypeLabels = [
            'regular' => 'Regular',
            'semester' => 'Semester',
            'custom' => 'Custom',
        ];

        return [
            $no,
            $loan->student->nis ?? '-',
            $loan->student->name ?? '-',
            $loan->bookCopy->book->code ?? '-',
            $loan->bookCopy->book->title ?? '-',
            $loan->bookCopy->barcode ?? '-',
            $loan->loan_date?->format('d/m/Y') ?? '-',
            $loan->due_date?->format('d/m/Y') ?? '-',
            $loan->return_date?->format('d/m/Y') ?? '-',
            $loanTypeLabels[$loan->loan_type] ?? $loan->loan_type,
            $statusLabels[$loan->status] ?? $loan->status,
            $loan->fine ? number_format($loan->fine->amount, 0, ',', '.') : '0',
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }

    public function title(): string
    {
        return 'Laporan Peminjaman';
    }
}
