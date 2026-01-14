<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\BookSource;
use App\Models\Category;
use App\Models\Classification;
use App\Models\Publisher;
use App\Models\SubClassification;
use Illuminate\Http\Request;

class BookController extends Controller
{
    public function index()
    {
        return view('books.index');
    }

    public function create()
    {
        return view('books.form', [
            'book' => new Book(['stock' => 1, 'entry_date' => now()]),
            'classifications' => Classification::orderBy('ddc_code')->get(),
            'subClassifications' => collect(),
            'categories' => Category::orderBy('name')->get(),
            'publishers' => Publisher::orderBy('name')->get(),
            'bookSources' => BookSource::orderBy('name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:20|unique:books,code',
            'title' => 'required|string|max:255',
            'author' => 'required|string|max:100',
            'publisher_id' => 'required|exists:publishers,id',
            'publish_place' => 'required|string|max:100',
            'publish_year' => 'required|integer|min:1900|max:' . (date('Y') + 1),
            'isbn' => 'nullable|string|max:20',
            'stock' => 'required|integer|min:1',
            'page_count' => 'required|integer|min:1',
            'thickness' => 'nullable|string|max:20',
            'classification_id' => 'required|exists:classifications,id',
            'sub_classification_id' => 'nullable|exists:sub_classifications,id',
            'category_id' => 'required|exists:categories,id',
            'shelf_location' => 'required|string|max:50',
            'description' => 'nullable|string',
            'book_source_id' => 'required|exists:book_sources,id',
            'entry_date' => 'required|date',
            'price' => 'nullable|numeric|min:0',
        ]);

        Book::create($validated);

        return redirect()
            ->route('books.index')
            ->with('success', 'Data buku berhasil ditambahkan.');
    }

    public function show(Book $book)
    {
        $book->load(['classification', 'subClassification', 'category', 'publisher', 'copies']);
        
        return view('books.show', [
            'book' => $book,
        ]);
    }

    public function edit(Book $book)
    {
        return view('books.form', [
            'book' => $book,
            'classifications' => Classification::orderBy('ddc_code')->get(),
            'subClassifications' => $book->classification_id 
                ? SubClassification::where('classification_id', $book->classification_id)->orderBy('sub_ddc_code')->get()
                : collect(),
            'categories' => Category::orderBy('name')->get(),
            'publishers' => Publisher::orderBy('name')->get(),
            'bookSources' => BookSource::orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, Book $book)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:20|unique:books,code,' . $book->id,
            'title' => 'required|string|max:255',
            'author' => 'required|string|max:100',
            'publisher_id' => 'required|exists:publishers,id',
            'publish_place' => 'required|string|max:100',
            'publish_year' => 'required|integer|min:1900|max:' . (date('Y') + 1),
            'isbn' => 'nullable|string|max:20',
            'stock' => 'required|integer|min:1',
            'page_count' => 'required|integer|min:1',
            'thickness' => 'nullable|string|max:20',
            'classification_id' => 'required|exists:classifications,id',
            'sub_classification_id' => 'nullable|exists:sub_classifications,id',
            'category_id' => 'required|exists:categories,id',
            'shelf_location' => 'required|string|max:50',
            'description' => 'nullable|string',
            'book_source_id' => 'required|exists:book_sources,id',
            'entry_date' => 'required|date',
            'price' => 'nullable|numeric|min:0',
        ]);

        $book->update($validated);

        return redirect()
            ->route('books.index')
            ->with('success', 'Data buku berhasil diperbarui.');
    }

    public function destroy(Book $book)
    {
        // Check for active loans on any copy
        $hasActiveLoans = $book->copies()->whereHas('loans', function ($query) {
            $query->where('status', 'active');
        })->exists();

        if ($hasActiveLoans) {
            return redirect()
                ->route('books.index')
                ->with('error', 'Buku tidak dapat dihapus karena masih ada peminjaman aktif.');
        }

        $book->delete();

        return redirect()
            ->route('books.index')
            ->with('success', 'Data buku berhasil dihapus.');
    }

    public function getSubClassifications(Classification $classification)
    {
        return response()->json(
            $classification->subClassifications()->orderBy('sub_ddc_code')->get()
        );
    }

    public function downloadTemplate()
    {
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="template_import_buku.csv"',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $columns = [
            'kode_buku', 'judul', 'pengarang', 'penerbit', 'tempat_terbit', 
            'tahun_terbit', 'isbn', 'stok', 'jumlah_halaman', 'ketebalan',
            'klasifikasi_ddc', 'sub_klasifikasi', 'kategori', 'lokasi_rak',
            'deskripsi', 'sumber', 'tanggal_masuk', 'harga'
        ];
        $example = [
            'BK001', 'Pemrograman PHP', 'John Doe', 'Gramedia', 'Jakarta',
            '2024', '978-123-456-789', '5', '250', '2 cm',
            '000', '000 - 009', 'Fiksi', 'A-01-01',
            'Buku tentang pemrograman PHP', 'Pembelian', '2024-01-15', '150000'
        ];

        $callback = function() use ($columns, $example) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF)); // UTF-8 BOM
            fputcsv($file, $columns);
            fputcsv($file, $example);
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
