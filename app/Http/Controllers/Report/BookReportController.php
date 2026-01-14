<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\Category;
use App\Models\Loan;
use App\Exports\BookReportExport;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class BookReportController extends Controller
{
    public function index(Request $request): View
    {
        $reportType = $request->get('report_type', 'top_borrowed');
        $startDate = $request->get('start_date', now()->startOfYear()->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->format('Y-m-d'));
        $categoryId = $request->get('category_id', 'all');
        $limit = $request->get('limit', 20);

        $categories = Category::orderBy('name')->get();

        if ($reportType === 'top_borrowed') {
            $books = $this->getTopBorrowedBooks($startDate, $endDate, $categoryId, $limit);
        } else {
            $books = $this->getNeverBorrowedBooks($categoryId, $limit);
        }

        $periodLabel = \Carbon\Carbon::parse($startDate)->format('d/m/Y') . ' - ' . \Carbon\Carbon::parse($endDate)->format('d/m/Y');

        return view('reports.books.index', compact(
            'books',
            'reportType',
            'startDate',
            'endDate',
            'categoryId',
            'limit',
            'categories',
            'periodLabel'
        ));
    }

    protected function getTopBorrowedBooks(string $startDate, string $endDate, string $categoryId, int $limit)
    {
        $query = Book::select('books.*')
            ->selectRaw('COUNT(loans.id) as loan_count')
            ->join('book_copies', 'books.id', '=', 'book_copies.book_id')
            ->join('loans', 'book_copies.id', '=', 'loans.book_copy_id')
            ->whereBetween('loans.loan_date', [$startDate, $endDate])
            ->groupBy('books.id')
            ->orderByDesc('loan_count');

        if ($categoryId !== 'all') {
            $query->where('books.category_id', $categoryId);
        }

        return $query->limit($limit)->get();
    }

    protected function getNeverBorrowedBooks(string $categoryId, int $limit)
    {
        $query = Book::select('books.*')
            ->leftJoin('book_copies', 'books.id', '=', 'book_copies.book_id')
            ->leftJoin('loans', 'book_copies.id', '=', 'loans.book_copy_id')
            ->whereNull('loans.id')
            ->groupBy('books.id')
            ->orderBy('books.entry_date', 'desc');

        if ($categoryId !== 'all') {
            $query->where('books.category_id', $categoryId);
        }

        return $query->limit($limit)->get();
    }

    public function exportExcel(Request $request): BinaryFileResponse
    {
        $reportType = $request->get('report_type', 'top_borrowed');
        $startDate = $request->get('start_date', now()->startOfYear()->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->format('Y-m-d'));
        $categoryId = $request->get('category_id', 'all');
        $limit = $request->get('limit', 20);

        $filename = $reportType === 'top_borrowed'
            ? 'laporan-buku-terpopuler-' . $startDate . '-' . $endDate . '.xlsx'
            : 'laporan-buku-tidak-dipinjam.xlsx';

        return Excel::download(
            new BookReportExport($reportType, $startDate, $endDate, $categoryId, $limit),
            $filename
        );
    }

    public function exportPdf(Request $request)
    {
        $reportType = $request->get('report_type', 'top_borrowed');
        $startDate = $request->get('start_date', now()->startOfYear()->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->format('Y-m-d'));
        $categoryId = $request->get('category_id', 'all');
        $limit = $request->get('limit', 20);

        if ($reportType === 'top_borrowed') {
            $books = $this->getTopBorrowedBooks($startDate, $endDate, $categoryId, $limit);
            $title = 'LAPORAN BUKU TERPOPULER';
        } else {
            $books = $this->getNeverBorrowedBooks($categoryId, $limit);
            $title = 'LAPORAN BUKU TIDAK PERNAH DIPINJAM';
        }

        $periodLabel = \Carbon\Carbon::parse($startDate)->format('d/m/Y') . ' - ' . \Carbon\Carbon::parse($endDate)->format('d/m/Y');

        $pdf = Pdf::loadView('reports.books.pdf', compact(
            'books',
            'reportType',
            'periodLabel',
            'title'
        ));

        $filename = $reportType === 'top_borrowed'
            ? 'laporan-buku-terpopuler-' . $startDate . '-' . $endDate . '.pdf'
            : 'laporan-buku-tidak-dipinjam.pdf';

        return $pdf->download($filename);
    }
}
