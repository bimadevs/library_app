<?php

use App\Http\Controllers\BookController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\Master\AcademicYearController;
use App\Http\Controllers\Master\CategoryController;
use App\Http\Controllers\Master\ClassController;
use App\Http\Controllers\Master\ClassificationController;
use App\Http\Controllers\Master\FineSettingController;
use App\Http\Controllers\Master\MajorController;
use App\Http\Controllers\Master\PublisherController;
use App\Http\Controllers\Master\SubClassificationController;
use App\Http\Controllers\Master\BookSourceController;
use App\Http\Controllers\Report\LoanReportController;
use App\Http\Controllers\Report\FineReportController;
use App\Http\Controllers\FineController;
use App\Http\Controllers\Report\BookReportController;
use App\Http\Controllers\Report\VisitorReportController;
use App\Livewire\Visitor\CheckIn;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    // Visitor Route
    Route::get('/visitors/check-in', CheckIn::class)->name('visitors.check-in');

    // Profile Routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Backup Route
    Route::get('/backup/download', [App\Http\Controllers\BackupController::class, 'download'])->name('backup.download');

    // Master Data Routes
    Route::prefix('master')->name('master.')->group(function () {
        // Academic Years
        Route::resource('academic-years', AcademicYearController::class)->except(['show']);
        
        // Classes
        Route::resource('classes', ClassController::class)->except(['show']);
        
        // Majors
        Route::resource('majors', MajorController::class)->except(['show']);
        
        // Classifications
        Route::resource('classifications', ClassificationController::class)->except(['show']);
        
        // Sub Classifications
        Route::resource('sub-classifications', SubClassificationController::class)->except(['show']);
        Route::get('/sub-classifications/by-classification/{classification}', [SubClassificationController::class, 'getByClassification'])
            ->name('sub-classifications.by-classification');
        
        // Publishers
        Route::resource('publishers', PublisherController::class)->except(['show']);
        
        // Categories
        Route::resource('categories', CategoryController::class)->except(['show']);
        
        // Book Sources
        Route::resource('book-sources', BookSourceController::class)->except(['show']);
        
        // Fine Settings
        Route::get('/fine-settings', [FineSettingController::class, 'index'])->name('fine-settings.index');
        Route::put('/fine-settings', [FineSettingController::class, 'update'])->name('fine-settings.update');
    });

    // Student Routes
    Route::resource('students', StudentController::class);
    Route::get('/students-import', fn() => view('students.import'))->name('students.import');
    Route::get('/students-import/template', [StudentController::class, 'downloadTemplate'])->name('students.import.template');
    Route::get('/students-promotion', fn() => view('students.promotion'))->name('students.promotion');

    // Book Routes
    Route::post('/books/print-label', [BookController::class, 'printLabel'])->name('books.print-label');
    Route::get('/books-label', [App\Http\Controllers\Book\BookLabelController::class, 'index'])->name('books.label');
    Route::resource('books', BookController::class);
    Route::get('/books-import', fn() => view('books.import'))->name('books.import');
    Route::get('/books-import/template', [BookController::class, 'downloadTemplate'])->name('books.import.template');
    Route::get('/books-barcode', fn() => view('books.barcode'))->name('books.barcode');
    Route::get('/books/sub-classifications/{classification}', [BookController::class, 'getSubClassifications'])
        ->name('books.sub-classifications');

    // Book Copy Route
    Route::put('/book-copies/{bookCopy}', [App\Http\Controllers\Book\BookCopyController::class, 'update'])->name('book-copies.update');

    // Transaction Routes
    Route::prefix('transactions')->name('transactions.')->group(function () {
        Route::get('/loans/create', fn() => view('transactions.loans.create'))->name('loans.create');
        Route::get('/returns/create', fn() => view('transactions.returns.create'))->name('returns.create');
    });

    // Pay Fine Route
    Route::post('/fines/{fine}/pay', [FineController::class, 'markAsPaid'])->name('fines.pay');

    // Report Routes
    Route::prefix('reports')->name('reports.')->group(function () {
        // Visitor Reports
        Route::get('/visitors', [VisitorReportController::class, 'index'])->name('visitors');

        // Loan Reports
        Route::get('/loans', [LoanReportController::class, 'index'])->name('loans');
        Route::get('/loans/export-excel', [LoanReportController::class, 'exportExcel'])->name('loans.export-excel');
        Route::get('/loans/export-pdf', [LoanReportController::class, 'exportPdf'])->name('loans.export-pdf');
        
        // Fine Reports
        Route::get('/fines', [FineReportController::class, 'index'])->name('fines');
        Route::get('/fines/export-excel', [FineReportController::class, 'exportExcel'])->name('fines.export-excel');
        Route::get('/fines/export-pdf', [FineReportController::class, 'exportPdf'])->name('fines.export-pdf');
        
        // Book Reports
        Route::get('/books', [BookReportController::class, 'index'])->name('books');
        Route::get('/books/export-excel', [BookReportController::class, 'exportExcel'])->name('books.export-excel');
        Route::get('/books/export-pdf', [BookReportController::class, 'exportPdf'])->name('books.export-pdf');
    });
});

require __DIR__.'/auth.php';
