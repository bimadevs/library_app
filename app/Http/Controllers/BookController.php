<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBookRequest;
use App\Http\Requests\UpdateBookRequest;
use App\Models\Book;
use App\Models\BookSource;
use App\Models\Category;
use App\Models\Classification;
use App\Models\Publisher;
use App\Models\SubClassification;
use App\Services\BookService;
use Illuminate\Http\Request;

class BookController extends Controller
{
    protected BookService $bookService;

    public function __construct(BookService $bookService)
    {
        $this->bookService = $bookService;
    }

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

    public function store(StoreBookRequest $request)
    {
        $this->bookService->createBook($request->validated());

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

    public function update(UpdateBookRequest $request, Book $book)
    {
        $this->bookService->updateBook($book, $request->validated());

        return redirect()
            ->route('books.index')
            ->with('success', 'Data buku berhasil diperbarui.');
    }

    public function destroy(Book $book)
    {
        if ($this->bookService->hasActiveLoans($book)) {
            return redirect()
                ->route('books.index')
                ->with('error', 'Buku tidak dapat dihapus karena masih ada peminjaman aktif.');
        }

        $this->bookService->deleteBook($book);

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

    public function printLabel(Request $request)
    {
        $bookIds = $request->input('books', []);
        
        if (empty($bookIds)) {
            return redirect()->back()->with('error', 'Pilih minimal satu buku untuk dicetak labelnya.');
        }

        $pdf = $this->bookService->generateLabelPdf($bookIds);

        return $pdf->stream('label-buku.pdf');
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

        return response()->stream(
            $this->bookService->generateImportTemplateCallback(),
            200,
            $headers
        );
    }
}
