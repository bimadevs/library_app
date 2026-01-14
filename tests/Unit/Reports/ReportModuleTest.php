<?php

/**
 * Unit tests for Report Module
 * 
 * Tests report data accuracy and export functionality
 * 
 * **Validates: Requirements 17.x, 18.x, 19.x**
 */

use App\Models\Book;
use App\Models\BookCopy;
use App\Models\Category;
use App\Models\Fine;
use App\Models\Loan;
use App\Models\Student;
use App\Exports\LoanReportExport;
use App\Exports\FineReportExport;
use App\Exports\BookReportExport;
use Carbon\Carbon;

beforeEach(function () {
    // Set a fixed date for consistent testing
    Carbon::setTestNow(Carbon::parse('2025-01-14'));
});

afterEach(function () {
    Carbon::setTestNow();
});

describe('Loan Report', function () {
    it('returns loans filtered by daily period - Requirements 17.1, 17.2', function () {
        // Create loans on different dates
        $today = now()->format('Y-m-d');
        $yesterday = now()->subDay()->format('Y-m-d');
        
        // Create loan for today
        $loanToday = Loan::factory()->create([
            'loan_date' => $today,
            'status' => 'active',
        ]);
        
        // Create loan for yesterday
        $loanYesterday = Loan::factory()->create([
            'loan_date' => $yesterday,
            'status' => 'active',
        ]);
        
        // Query loans for today
        $loans = Loan::whereDate('loan_date', $today)->get();
        
        expect($loans)->toHaveCount(1);
        expect($loans->first()->id)->toBe($loanToday->id);
    });

    it('returns loans filtered by monthly period - Requirements 17.1, 17.3', function () {
        $thisMonth = now()->format('Y-m');
        $lastMonth = now()->subMonth()->format('Y-m');
        
        // Create loans for this month
        $loansThisMonth = Loan::factory()->count(3)->create([
            'loan_date' => now()->format('Y-m-d'),
        ]);
        
        // Create loans for last month
        $loansLastMonth = Loan::factory()->count(2)->create([
            'loan_date' => now()->subMonth()->format('Y-m-d'),
        ]);
        
        // Query loans for this month
        $startOfMonth = Carbon::parse($thisMonth . '-01')->startOfMonth();
        $endOfMonth = Carbon::parse($thisMonth . '-01')->endOfMonth();
        
        $loans = Loan::whereBetween('loan_date', [$startOfMonth, $endOfMonth])->get();
        
        expect($loans)->toHaveCount(3);
    });

    it('includes student and book details in loan report - Requirements 17.4', function () {
        $student = Student::factory()->create(['name' => 'Test Student']);
        $book = Book::factory()->create(['title' => 'Test Book']);
        $bookCopy = BookCopy::factory()->create(['book_id' => $book->id]);
        
        $loan = Loan::factory()->create([
            'student_id' => $student->id,
            'book_copy_id' => $bookCopy->id,
            'loan_date' => now(),
            'due_date' => now()->addWeek(),
            'status' => 'active',
        ]);
        
        $loanWithRelations = Loan::with(['student', 'bookCopy.book'])->find($loan->id);
        
        expect($loanWithRelations->student->name)->toBe('Test Student');
        expect($loanWithRelations->bookCopy->book->title)->toBe('Test Book');
        expect($loanWithRelations->loan_date)->not->toBeNull();
        expect($loanWithRelations->due_date)->not->toBeNull();
        expect($loanWithRelations->status)->toBe('active');
    });

    it('exports loan report to Excel - Requirements 17.5', function () {
        // Create test data
        Loan::factory()->count(5)->create([
            'loan_date' => now()->format('Y-m-d'),
        ]);
        
        $export = new LoanReportExport('daily', now()->format('Y-m-d'), now()->format('Y-m'));
        $collection = $export->collection();
        
        expect($collection)->toHaveCount(5);
        expect($export->headings())->toContain('NIS');
        expect($export->headings())->toContain('Nama Siswa');
        expect($export->headings())->toContain('Judul Buku');
        expect($export->headings())->toContain('Status');
    });
});

describe('Fine Report', function () {
    it('returns fines filtered by date range - Requirements 18.4', function () {
        $startDate = now()->subDays(7)->format('Y-m-d');
        $endDate = now()->format('Y-m-d');
        
        // Create fines within range
        $finesInRange = Fine::factory()->count(3)->create([
            'created_at' => now()->subDays(3),
        ]);
        
        // Create fines outside range
        $finesOutRange = Fine::factory()->count(2)->create([
            'created_at' => now()->subDays(10),
        ]);
        
        $fines = Fine::whereBetween('created_at', [
            $startDate . ' 00:00:00',
            $endDate . ' 23:59:59'
        ])->get();
        
        expect($fines)->toHaveCount(3);
    });

    it('filters fines by payment status - Requirements 18.4', function () {
        // Create paid fines
        Fine::factory()->count(3)->create(['is_paid' => true]);
        
        // Create unpaid fines
        Fine::factory()->count(2)->create(['is_paid' => false]);
        
        $paidFines = Fine::where('is_paid', true)->get();
        $unpaidFines = Fine::where('is_paid', false)->get();
        
        expect($paidFines)->toHaveCount(3);
        expect($unpaidFines)->toHaveCount(2);
    });

    it('separates late fines and lost book compensations - Requirements 18.3', function () {
        // Create late fines
        Fine::factory()->count(4)->create(['type' => 'late']);
        
        // Create lost book fines
        Fine::factory()->count(2)->create(['type' => 'lost']);
        
        $lateFines = Fine::where('type', 'late')->get();
        $lostFines = Fine::where('type', 'lost')->get();
        
        expect($lateFines)->toHaveCount(4);
        expect($lostFines)->toHaveCount(2);
    });

    it('calculates total fines collected and outstanding - Requirements 18.2', function () {
        // Create paid fines with known amounts
        Fine::factory()->create(['is_paid' => true, 'amount' => 10000]);
        Fine::factory()->create(['is_paid' => true, 'amount' => 15000]);
        
        // Create unpaid fines with known amounts
        Fine::factory()->create(['is_paid' => false, 'amount' => 5000]);
        Fine::factory()->create(['is_paid' => false, 'amount' => 8000]);
        
        $paidAmount = Fine::where('is_paid', true)->sum('amount');
        $unpaidAmount = Fine::where('is_paid', false)->sum('amount');
        
        expect((float) $paidAmount)->toBe(25000.0);
        expect((float) $unpaidAmount)->toBe(13000.0);
    });

    it('includes student and fine details - Requirements 18.1', function () {
        $student = Student::factory()->create(['name' => 'Fine Student']);
        $loan = Loan::factory()->create(['student_id' => $student->id]);
        
        $fine = Fine::factory()->create([
            'student_id' => $student->id,
            'loan_id' => $loan->id,
            'type' => 'late',
            'amount' => 5000,
            'is_paid' => false,
        ]);
        
        $fineWithRelations = Fine::with(['student', 'loan.bookCopy.book'])->find($fine->id);
        
        expect($fineWithRelations->student->name)->toBe('Fine Student');
        expect($fineWithRelations->type)->toBe('late');
        expect((float) $fineWithRelations->amount)->toBe(5000.0);
        expect($fineWithRelations->is_paid)->toBeFalse();
    });

    it('exports fine report to Excel - Requirements 18.5', function () {
        Fine::factory()->count(5)->create([
            'created_at' => now(),
        ]);
        
        $export = new FineReportExport(
            now()->subMonth()->format('Y-m-d'),
            now()->format('Y-m-d'),
            'all',
            'all'
        );
        $collection = $export->collection();
        
        expect($collection)->toHaveCount(5);
        expect($export->headings())->toContain('NIS');
        expect($export->headings())->toContain('Nama Siswa');
        expect($export->headings())->toContain('Tipe Denda');
        expect($export->headings())->toContain('Status Bayar');
    });
});

describe('Book Report', function () {
    it('returns top borrowed books with loan count - Requirements 19.1, 19.3', function () {
        // Create books with different loan counts
        $book1 = Book::factory()->create(['title' => 'Popular Book']);
        $book2 = Book::factory()->create(['title' => 'Less Popular Book']);
        
        $copy1 = BookCopy::factory()->create(['book_id' => $book1->id]);
        $copy2 = BookCopy::factory()->create(['book_id' => $book2->id]);
        
        // Create more loans for book1
        Loan::factory()->count(5)->create([
            'book_copy_id' => $copy1->id,
            'loan_date' => now(),
        ]);
        
        // Create fewer loans for book2
        Loan::factory()->count(2)->create([
            'book_copy_id' => $copy2->id,
            'loan_date' => now(),
        ]);
        
        $topBooks = Book::select('books.*')
            ->selectRaw('COUNT(loans.id) as loan_count')
            ->join('book_copies', 'books.id', '=', 'book_copies.book_id')
            ->join('loans', 'book_copies.id', '=', 'loans.book_copy_id')
            ->groupBy('books.id')
            ->orderByDesc('loan_count')
            ->get();
        
        expect($topBooks->first()->title)->toBe('Popular Book');
        expect($topBooks->first()->loan_count)->toBe(5);
    });

    it('returns books that have never been borrowed - Requirements 19.2, 19.4', function () {
        // Create book with loans
        $borrowedBook = Book::factory()->create(['title' => 'Borrowed Book']);
        $borrowedCopy = BookCopy::factory()->create(['book_id' => $borrowedBook->id]);
        Loan::factory()->create(['book_copy_id' => $borrowedCopy->id]);
        
        // Create book without loans
        $neverBorrowedBook = Book::factory()->create(['title' => 'Never Borrowed']);
        BookCopy::factory()->create(['book_id' => $neverBorrowedBook->id]);
        
        $neverBorrowed = Book::select('books.*')
            ->leftJoin('book_copies', 'books.id', '=', 'book_copies.book_id')
            ->leftJoin('loans', 'book_copies.id', '=', 'loans.book_copy_id')
            ->whereNull('loans.id')
            ->groupBy('books.id')
            ->get();
        
        expect($neverBorrowed)->toHaveCount(1);
        expect($neverBorrowed->first()->title)->toBe('Never Borrowed');
    });

    it('filters books by category - Requirements 19.5', function () {
        $category1 = Category::factory()->create(['name' => 'Fiction']);
        $category2 = Category::factory()->create(['name' => 'Non-Fiction']);
        
        Book::factory()->count(3)->create(['category_id' => $category1->id]);
        Book::factory()->count(2)->create(['category_id' => $category2->id]);
        
        $fictionBooks = Book::where('category_id', $category1->id)->get();
        $nonFictionBooks = Book::where('category_id', $category2->id)->get();
        
        expect($fictionBooks)->toHaveCount(3);
        expect($nonFictionBooks)->toHaveCount(2);
    });

    it('filters books by date range - Requirements 19.5', function () {
        $startDate = now()->subDays(7)->format('Y-m-d');
        $endDate = now()->format('Y-m-d');
        
        // Create book with loans in range
        $book = Book::factory()->create();
        $copy = BookCopy::factory()->create(['book_id' => $book->id]);
        Loan::factory()->create([
            'book_copy_id' => $copy->id,
            'loan_date' => now()->subDays(3),
        ]);
        
        // Create book with loans outside range
        $oldBook = Book::factory()->create();
        $oldCopy = BookCopy::factory()->create(['book_id' => $oldBook->id]);
        Loan::factory()->create([
            'book_copy_id' => $oldCopy->id,
            'loan_date' => now()->subDays(10),
        ]);
        
        $booksInRange = Book::select('books.*')
            ->selectRaw('COUNT(loans.id) as loan_count')
            ->join('book_copies', 'books.id', '=', 'book_copies.book_id')
            ->join('loans', 'book_copies.id', '=', 'loans.book_copy_id')
            ->whereBetween('loans.loan_date', [$startDate, $endDate])
            ->groupBy('books.id')
            ->get();
        
        expect($booksInRange)->toHaveCount(1);
    });

    it('exports book report to Excel - Requirements 19.6', function () {
        $book = Book::factory()->create();
        $copy = BookCopy::factory()->create(['book_id' => $book->id]);
        
        // Create loans with explicit date within range
        $loanDate = now()->format('Y-m-d');
        Loan::factory()->count(3)->create([
            'book_copy_id' => $copy->id,
            'loan_date' => $loanDate,
        ]);
        
        $export = new BookReportExport(
            'top_borrowed',
            now()->subYear()->format('Y-m-d'),
            now()->addDay()->format('Y-m-d'),
            'all',
            20
        );
        $collection = $export->collection();
        
        expect($collection)->toHaveCount(1);
        expect($export->headings())->toContain('Kode Buku');
        expect($export->headings())->toContain('Judul');
        expect($export->headings())->toContain('Pengarang');
        expect($export->headings())->toContain('Jumlah Peminjaman');
    });
});
