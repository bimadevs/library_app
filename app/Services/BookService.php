<?php

namespace App\Services;

use App\Models\Book;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Collection;

class BookService
{
    public function createBook(array $data): Book
    {
        if (isset($data['cover_image']) && $data['cover_image'] instanceof UploadedFile) {
            $data['cover_image'] = $data['cover_image']->store('books/covers', 'public');
        }

        return Book::create($data);
    }

    public function updateBook(Book $book, array $data): Book
    {
        if (isset($data['cover_image']) && $data['cover_image'] instanceof UploadedFile) {
            // Delete old image if exists
            if ($book->cover_image && Storage::disk('public')->exists($book->cover_image)) {
                Storage::disk('public')->delete($book->cover_image);
            }
            $data['cover_image'] = $data['cover_image']->store('books/covers', 'public');
        }

        $book->update($data);
        return $book;
    }

    public function deleteBook(Book $book): void
    {
        if ($book->cover_image && Storage::disk('public')->exists($book->cover_image)) {
            Storage::disk('public')->delete($book->cover_image);
        }

        $book->delete();
    }

    public function hasActiveLoans(Book $book): bool
    {
        return $book->copies()->whereHas('loans', function ($query) {
            $query->where('status', 'active');
        })->exists();
    }

    public function generateLabelPdf(array $bookIds)
    {
        $books = Book::whereIn('id', $bookIds)
            ->with(['classification', 'subClassification'])
            ->get();

        $pdf = Pdf::loadView('books.print-label', [
            'books' => $books
        ]);

        return $pdf;
    }

    public function generateImportTemplateCallback(): \Closure
    {
        $columns = [
            'kode_buku', 'judul', 'pengarang', 'penerbit', 'tempat_terbit',
            'tahun_terbit', 'isbn', 'stok', 'jumlah_halaman', 'ketebalan',
            'klasifikasi_ddc', 'sub_klasifikasi', 'kategori', 'lokasi_rak',
            'deskripsi', 'sumber', 'tanggal_masuk', 'harga', 'buku_paket'
        ];
        $example = [
            'BK001', 'Pemrograman PHP', 'John Doe', 'Gramedia', 'Jakarta',
            '2024', '978-123-456-789', '5', '250', '2 cm',
            '000', '000 - 009', 'Fiksi', 'A-01-01',
            'Buku tentang pemrograman PHP', 'Pembelian', '2024-01-15', '150000', 'Tidak'
        ];

        return function() use ($columns, $example) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF)); // UTF-8 BOM
            fputcsv($file, $columns);
            fputcsv($file, $example);
            fclose($file);
        };
    }
}
