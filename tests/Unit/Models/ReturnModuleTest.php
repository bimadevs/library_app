<?php

/**
 * Feature: school-library, Property 6, 7: Return Module Properties
 * 
 * Property 6: Fine Calculation Correctness - *For any* late return, the fine amount must equal (days_overdue × daily_fine_rate).
 * Property 7: Lost Book Fine Calculation - *For any* lost book fine with flat price type, the amount must equal the configured lost_book_fine.
 *             For book_price type, the amount must equal the book's price.
 * 
 * **Validates: Requirements 7.1, 7.2, 7.3, 16.6, 16.7**
 */

use App\Models\Book;
use App\Models\BookCopy;
use App\Models\FineSetting;
use App\Models\Loan;
use App\Models\Student;
use App\Services\FineCalculatorService;
use App\Services\LoanService;
use App\Services\ReturnService;
use Carbon\Carbon;

beforeEach(function () {
    $this->fineCalculator = new FineCalculatorService();
    $this->returnService = new ReturnService($this->fineCalculator);
    $this->loanService = new LoanService();
});

it('calculates late fine correctly - Property 6', function () {
    // Feature: school-library, Property 6: Fine Calculation Correctness
    // For any late return, the fine amount must equal (days_overdue × daily_fine_rate).
    // Validates: Requirements 7.1, 16.6
    
    for ($i = 0; $i < 100; $i++) {
        // Create fine settings with random daily fine
        FineSetting::query()->delete();
        $dailyFine = fake()->randomFloat(2, 500, 5000);
        FineSetting::create([
            'daily_fine' => $dailyFine,
            'lost_book_fine' => 50000,
            'lost_fine_type' => 'flat',
        ]);

        // Create student and book
        $student = Student::factory()->create(['max_loan' => 5]);
        $book = Book::factory()->create(['stock' => 1]);
        $bookCopy = BookCopy::factory()->create([
            'book_id' => $book->id,
            'barcode' => $book->code . '-001',
            'status' => 'available',
        ]);

        // Create a loan
        $loan = $this->loanService->createLoan(
            $student,
            $bookCopy,
            LoanService::TYPE_REGULAR
        );

        // Generate random days overdue (1 to 30 days)
        $daysOverdue = fake()->numberBetween(1, 30);
        $returnDate = $loan->due_date->copy()->addDays($daysOverdue);

        // Calculate expected fine
        $expectedFine = $daysOverdue * $dailyFine;

        // Calculate actual fine using the service
        $fineData = $this->fineCalculator->calculateLateFine($loan, $returnDate);

        // Verify fine calculation
        expect($fineData['days_overdue'])->toBe($daysOverdue);
        expect(round($fineData['amount'], 2))->toBe(round($expectedFine, 2));
    }
});

it('returns zero fine for on-time returns - Property 6 edge case', function () {
    // Feature: school-library, Property 6: Fine Calculation Correctness (on-time return)
    // For any on-time return, the fine amount must be zero.
    // Validates: Requirements 7.1, 16.6
    
    for ($i = 0; $i < 100; $i++) {
        // Create fine settings
        FineSetting::query()->delete();
        FineSetting::create([
            'daily_fine' => fake()->randomFloat(2, 500, 5000),
            'lost_book_fine' => 50000,
            'lost_fine_type' => 'flat',
        ]);

        // Create student and book
        $student = Student::factory()->create(['max_loan' => 5]);
        $book = Book::factory()->create(['stock' => 1]);
        $bookCopy = BookCopy::factory()->create([
            'book_id' => $book->id,
            'barcode' => $book->code . '-001',
            'status' => 'available',
        ]);

        // Create a loan
        $loan = $this->loanService->createLoan(
            $student,
            $bookCopy,
            LoanService::TYPE_REGULAR
        );

        // Return on or before due date
        $daysEarly = fake()->numberBetween(0, 7);
        $returnDate = $loan->due_date->copy()->subDays($daysEarly);

        // Calculate fine
        $fineData = $this->fineCalculator->calculateLateFine($loan, $returnDate);

        // Verify no fine for on-time return
        expect($fineData['days_overdue'])->toBe(0);
        expect($fineData['amount'])->toBe(0.0);
    }
});

it('calculates lost book fine with flat type correctly - Property 7', function () {
    // Feature: school-library, Property 7: Lost Book Fine Calculation (flat type)
    // For any lost book fine with flat price type, the amount must equal the configured lost_book_fine.
    // Validates: Requirements 7.2, 16.7
    
    for ($i = 0; $i < 100; $i++) {
        // Create fine settings with flat lost fine type
        FineSetting::query()->delete();
        $lostBookFine = fake()->randomFloat(2, 10000, 100000);
        FineSetting::create([
            'daily_fine' => 1000,
            'lost_book_fine' => $lostBookFine,
            'lost_fine_type' => 'flat',
        ]);

        // Create book with random price
        $book = Book::factory()->create([
            'stock' => 1,
            'price' => fake()->randomFloat(2, 20000, 200000),
        ]);

        // Calculate lost book fine
        $calculatedFine = $this->fineCalculator->calculateLostBookFine($book);

        // Verify flat fine is used regardless of book price
        expect(round($calculatedFine, 2))->toBe(round($lostBookFine, 2));
    }
});

it('calculates lost book fine with book_price type correctly - Property 7', function () {
    // Feature: school-library, Property 7: Lost Book Fine Calculation (book_price type)
    // For any lost book fine with book_price type, the amount must equal the book's price.
    // Validates: Requirements 7.3, 16.7
    
    for ($i = 0; $i < 100; $i++) {
        // Create fine settings with book_price lost fine type
        FineSetting::query()->delete();
        $flatFine = fake()->randomFloat(2, 10000, 100000);
        FineSetting::create([
            'daily_fine' => 1000,
            'lost_book_fine' => $flatFine,
            'lost_fine_type' => 'book_price',
        ]);

        // Create book with random price
        $bookPrice = fake()->randomFloat(2, 20000, 200000);
        $book = Book::factory()->create([
            'stock' => 1,
            'price' => $bookPrice,
        ]);

        // Calculate lost book fine
        $calculatedFine = $this->fineCalculator->calculateLostBookFine($book);

        // Verify book price is used
        expect(round($calculatedFine, 2))->toBe(round($bookPrice, 2));
    }
});

it('falls back to flat fine when book has no price - Property 7 edge case', function () {
    // Feature: school-library, Property 7: Lost Book Fine Calculation (fallback)
    // When book_price type is used but book has no price, fall back to flat fine.
    // Validates: Requirements 7.2, 7.3, 16.7
    
    for ($i = 0; $i < 100; $i++) {
        // Create fine settings with book_price lost fine type
        FineSetting::query()->delete();
        $flatFine = fake()->randomFloat(2, 10000, 100000);
        FineSetting::create([
            'daily_fine' => 1000,
            'lost_book_fine' => $flatFine,
            'lost_fine_type' => 'book_price',
        ]);

        // Create book without price
        $book = Book::factory()->create([
            'stock' => 1,
            'price' => null,
        ]);

        // Calculate lost book fine
        $calculatedFine = $this->fineCalculator->calculateLostBookFine($book);

        // Verify flat fine is used as fallback
        expect(round($calculatedFine, 2))->toBe(round($flatFine, 2));
    }
});

it('processes return and creates fine record for late returns - Property 6 integration', function () {
    // Feature: school-library, Property 6: Fine Calculation Correctness (integration)
    // Processing a late return should create a fine record with correct amount.
    // Validates: Requirements 7.1, 16.6
    
    for ($i = 0; $i < 100; $i++) {
        // Create fine settings
        FineSetting::query()->delete();
        $dailyFine = fake()->randomFloat(2, 500, 5000);
        FineSetting::create([
            'daily_fine' => $dailyFine,
            'lost_book_fine' => 50000,
            'lost_fine_type' => 'flat',
        ]);

        // Create student and book
        $student = Student::factory()->create(['max_loan' => 5]);
        $book = Book::factory()->create(['stock' => 1]);
        $bookCopy = BookCopy::factory()->create([
            'book_id' => $book->id,
            'barcode' => $book->code . '-001',
            'status' => 'available',
        ]);

        // Create a loan
        $loan = $this->loanService->createLoan(
            $student,
            $bookCopy,
            LoanService::TYPE_REGULAR
        );

        // Generate random days overdue
        $daysOverdue = fake()->numberBetween(1, 30);
        $returnDate = $loan->due_date->copy()->addDays($daysOverdue);
        $expectedFine = $daysOverdue * $dailyFine;

        // Process return
        $result = $this->returnService->processReturn($loan, false, $returnDate);

        // Verify loan status
        expect($result['loan']->status)->toBe('returned');
        expect($result['loan']->return_date->format('Y-m-d'))->toBe($returnDate->format('Y-m-d'));

        // Verify book copy status
        $bookCopy->refresh();
        expect($bookCopy->status)->toBe('available');

        // Verify fine record
        expect($result['fine'])->not->toBeNull();
        expect($result['fine']->type)->toBe('late');
        expect($result['fine']->days_overdue)->toBe($daysOverdue);
        expect(round($result['fine']->amount, 2))->toBe(round($expectedFine, 2));
        expect($result['fine']->is_paid)->toBeFalse();
    }
});

it('processes return and creates fine record for lost books - Property 7 integration', function () {
    // Feature: school-library, Property 7: Lost Book Fine Calculation (integration)
    // Processing a lost book return should create a fine record with correct amount.
    // Validates: Requirements 7.2, 7.3, 16.7
    
    for ($i = 0; $i < 100; $i++) {
        // Randomly choose flat or book_price type
        $lostFineType = fake()->randomElement(['flat', 'book_price']);
        
        // Create fine settings
        FineSetting::query()->delete();
        $flatFine = fake()->randomFloat(2, 10000, 100000);
        FineSetting::create([
            'daily_fine' => 1000,
            'lost_book_fine' => $flatFine,
            'lost_fine_type' => $lostFineType,
        ]);

        // Create student and book
        $student = Student::factory()->create(['max_loan' => 5]);
        $bookPrice = fake()->randomFloat(2, 20000, 200000);
        $book = Book::factory()->create([
            'stock' => 1,
            'price' => $bookPrice,
        ]);
        $bookCopy = BookCopy::factory()->create([
            'book_id' => $book->id,
            'barcode' => $book->code . '-001',
            'status' => 'available',
        ]);

        // Create a loan
        $loan = $this->loanService->createLoan(
            $student,
            $bookCopy,
            LoanService::TYPE_REGULAR
        );

        // Calculate expected fine
        $expectedFine = $lostFineType === 'book_price' ? $bookPrice : $flatFine;

        // Process return as lost
        $result = $this->returnService->processReturn($loan, true);

        // Verify loan status
        expect($result['loan']->status)->toBe('lost');

        // Verify book copy status
        $bookCopy->refresh();
        expect($bookCopy->status)->toBe('lost');

        // Verify fine record
        expect($result['fine'])->not->toBeNull();
        expect($result['fine']->type)->toBe('lost');
        expect(round($result['fine']->amount, 2))->toBe(round($expectedFine, 2));
        expect($result['fine']->is_paid)->toBeFalse();
    }
});

it('does not create fine for on-time returns - Property 6 no fine case', function () {
    // Feature: school-library, Property 6: Fine Calculation Correctness (no fine)
    // On-time returns should not create a fine record.
    // Validates: Requirements 7.1, 16.6
    
    for ($i = 0; $i < 100; $i++) {
        // Create fine settings
        FineSetting::query()->delete();
        FineSetting::create([
            'daily_fine' => fake()->randomFloat(2, 500, 5000),
            'lost_book_fine' => 50000,
            'lost_fine_type' => 'flat',
        ]);

        // Create student and book
        $student = Student::factory()->create(['max_loan' => 5]);
        $book = Book::factory()->create(['stock' => 1]);
        $bookCopy = BookCopy::factory()->create([
            'book_id' => $book->id,
            'barcode' => $book->code . '-001',
            'status' => 'available',
        ]);

        // Create a loan
        $loan = $this->loanService->createLoan(
            $student,
            $bookCopy,
            LoanService::TYPE_REGULAR
        );

        // Return on or before due date
        $daysEarly = fake()->numberBetween(0, 7);
        $returnDate = $loan->due_date->copy()->subDays($daysEarly);

        // Process return
        $result = $this->returnService->processReturn($loan, false, $returnDate);

        // Verify loan status
        expect($result['loan']->status)->toBe('returned');

        // Verify book copy status
        $bookCopy->refresh();
        expect($bookCopy->status)->toBe('available');

        // Verify no fine record
        expect($result['fine'])->toBeNull();
    }
});

it('maintains book copy status consistency after return - Property 5 related', function () {
    // Feature: school-library, Property 5: Book Copy Availability Consistency (return)
    // After return, book copy status should be 'available' (or 'lost' if marked lost).
    // Validates: Requirements 16.5
    
    for ($i = 0; $i < 100; $i++) {
        // Create fine settings
        FineSetting::query()->delete();
        FineSetting::create([
            'daily_fine' => 1000,
            'lost_book_fine' => 50000,
            'lost_fine_type' => 'flat',
        ]);

        // Create student and book
        $student = Student::factory()->create(['max_loan' => 5]);
        $book = Book::factory()->create(['stock' => 1]);
        $bookCopy = BookCopy::factory()->create([
            'book_id' => $book->id,
            'barcode' => $book->code . '-001',
            'status' => 'available',
        ]);

        // Create a loan
        $loan = $this->loanService->createLoan(
            $student,
            $bookCopy,
            LoanService::TYPE_REGULAR
        );

        // Verify borrowed status
        $bookCopy->refresh();
        expect($bookCopy->status)->toBe('borrowed');

        // Randomly decide if book is lost
        $isLost = fake()->boolean(20); // 20% chance of lost

        // Process return
        $result = $this->returnService->processReturn($loan, $isLost);

        // Verify book copy status
        $bookCopy->refresh();
        if ($isLost) {
            expect($bookCopy->status)->toBe('lost');
            expect($result['loan']->status)->toBe('lost');
        } else {
            expect($bookCopy->status)->toBe('available');
            expect($result['loan']->status)->toBe('returned');
        }

        // Verify no active loan exists
        expect($bookCopy->currentLoan)->toBeNull();
    }
});
