<?php

namespace App\Exports;

use App\Models\Fine;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class FineReportExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle
{
    protected string $startDate;
    protected string $endDate;
    protected string $paymentStatus;
    protected string $fineType;

    public function __construct(string $startDate, string $endDate, string $paymentStatus, string $fineType)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->paymentStatus = $paymentStatus;
        $this->fineType = $fineType;
    }

    public function collection()
    {
        $query = Fine::with(['student', 'loan.bookCopy.book'])
            ->whereBetween('created_at', [$this->startDate . ' 00:00:00', $this->endDate . ' 23:59:59']);

        if ($this->paymentStatus !== 'all') {
            $query->where('is_paid', $this->paymentStatus === 'paid');
        }

        if ($this->fineType !== 'all') {
            $query->where('type', $this->fineType);
        }

        return $query->orderBy('created_at', 'desc')->get();
    }

    public function headings(): array
    {
        return [
            'No',
            'NIS',
            'Nama Siswa',
            'Judul Buku',
            'Tipe Denda',
            'Hari Terlambat',
            'Jumlah (Rp)',
            'Status Bayar',
            'Tanggal Bayar',
            'Tanggal Dibuat',
        ];
    }

    public function map($fine): array
    {
        static $no = 0;
        $no++;

        $typeLabels = [
            'late' => 'Keterlambatan',
            'lost' => 'Buku Hilang',
        ];

        return [
            $no,
            $fine->student->nis ?? '-',
            $fine->student->name ?? '-',
            $fine->loan->bookCopy->book->title ?? '-',
            $typeLabels[$fine->type] ?? $fine->type,
            $fine->days_overdue ?? 0,
            number_format($fine->amount, 0, ',', '.'),
            $fine->is_paid ? 'Lunas' : 'Belum Lunas',
            $fine->paid_at?->format('d/m/Y H:i') ?? '-',
            $fine->created_at?->format('d/m/Y H:i'),
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
        return 'Laporan Denda';
    }
}
