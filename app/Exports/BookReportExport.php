<?php

namespace App\Exports;

use App\Models\Book;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class BookReportExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle
{
    protected string $reportType;
    protected string $startDate;
    protected string $endDate;
    protected string $categoryId;
    protected int $limit;

    public function __construct(string $reportType, string $startDate, string $endDate, string $categoryId, int $limit)
    {
        $this->reportType = $reportType;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->categoryId = $categoryId;
        $this->limit = $limit;
    }

    public function collection()
    {
        if ($this->reportType === 'top_borrowed') {
            return $this->getTopBorrowedBooks();
        }
        return $this->getNeverBorrowedBooks();
    }

    protected function getTopBorrowedBooks()
    {
        $query = Book::select('books.*')
            ->selectRaw('COUNT(loans.id) as loan_count')
            ->join('book_copies', 'books.id', '=', 'book_copies.book_id')
            ->join('loans', 'book_copies.id', '=', 'loans.book_copy_id')
            ->whereBetween('loans.loan_date', [$this->startDate, $this->endDate])
            ->groupBy('books.id')
            ->orderByDesc('loan_count');

        if ($this->categoryId !== 'all') {
            $query->where('books.category_id', $this->categoryId);
        }

        return $query->limit($this->limit)->get();
    }

    protected function getNeverBorrowedBooks()
    {
        $query = Book::select('books.*')
            ->leftJoin('book_copies', 'books.id', '=', 'book_copies.book_id')
            ->leftJoin('loans', 'book_copies.id', '=', 'loans.book_copy_id')
            ->whereNull('loans.id')
            ->groupBy('books.id')
            ->orderBy('books.entry_date', 'desc');

        if ($this->categoryId !== 'all') {
            $query->where('books.category_id', $this->categoryId);
        }

        return $query->limit($this->limit)->get();
    }

    public function headings(): array
    {
        $headings = [
            'No',
            'Kode Buku',
            'Judul',
            'Pengarang',
            'Penerbit',
            'Tahun Terbit',
            'Kategori',
            'Stok',
            'Tanggal Masuk',
        ];

        if ($this->reportType === 'top_borrowed') {
            $headings[] = 'Jumlah Peminjaman';
        }

        return $headings;
    }

    public function map($book): array
    {
        static $no = 0;
        $no++;

        $row = [
            $no,
            $book->code,
            $book->title,
            $book->author,
            $book->publisher->name ?? '-',
            $book->publish_year,
            $book->category->name ?? '-',
            $book->stock,
            $book->entry_date?->format('d/m/Y') ?? '-',
        ];

        if ($this->reportType === 'top_borrowed') {
            $row[] = $book->loan_count ?? 0;
        }

        return $row;
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }

    public function title(): string
    {
        return $this->reportType === 'top_borrowed' 
            ? 'Buku Terpopuler' 
            : 'Buku Tidak Dipinjam';
    }
}
