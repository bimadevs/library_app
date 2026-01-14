<?php

/**
 * Feature: school-library, Property 4, 5, 14: Loan Module Properties
 * 
 * Property 4: Loan Limit Enforcement - *For any* student, the count of their active loans must not exceed their max_loan value.
 * Property 5: Book Copy Availability Consistency - *For any* book copy, if it has an active loan, then the book copy status must be 'borrowed'.
 * Property 14: Loan Date Ordering - *For any* loan, the loan_date must be less than or equal to due_date.
 * 
 * **Validates: Requirements 15.6, 15.7, 15.8**
 */

use App\Models\Book;
use App\Models\BookCopy;
use App\Models\Loan;
use App\Models\Student;
use App\Services\LoanService;
use Carbon\Carbon;

beforeEach(function () {
    $this->loanService = new LoanService();
});

it('enforces student loan limit - Property 4', function () {
    // Feature: school-library, Property 4: Loan Limit Enforcement
    // For any student, the count of their active loans must not exceed their max_loan value.
    // Validates: Requirements 15.8
    
    for ($i = 0; $i < 100; $i++) {
        // Create a student with random max_loan between 1 and 5
        $maxLoan = fake()->numberBetween(1, 5);
        $student = Student::factory()->create(['max_loan' => $maxLoan]);
        
        // Create book copies for loans
        $book = Book::factory()->create(['stock' => $maxLoan + 1]);
        $bookCopies = [];
        for ($j = 0; $j <= $maxLoan; $j++) {
            $bookCopies[] = BookCopy::factory()->create([
                'book_id' => $book->id,
                'barcode' => $book->code . '-' . str_pad($j + 1, 3, '0', STR_PAD_LEFT),
                'status' => 'available',
            ]);
        }
        
        // Create loans up to the limit
        for ($j = 0; $j < $maxLoan; $j++) {
            $loan = $this->loanService->createLoan(
                $student,
                $bookCopies[$j],
                LoanService::TYPE_REGULAR
            );
            
            expect($loan)->toBeInstanceOf(Loan::class);
            expect($loan->status)->toBe('active');
        }
        
        // Verify active loans count equals max_loan
        $student->refresh();
        expect($student->activeLoans()->count())->toBe($maxLoan);
        
        // Attempting to create one more loan should throw an exception
        expect(fn () => $this->loanService->createLoan(
            $student,
            $bookCopies[$maxLoan],
            LoanService::TYPE_REGULAR
        ))->toThrow(\InvalidArgumentException::class);
        
        // Active loans count should still be at max_loan
        $student->refresh();
        expect($student->activeLoans()->count())->toBe($maxLoan);
        expect($student->activeLoans()->count())->toBeLessThanOrEqual($student->max_loan);
    }
});

it('maintains book copy availability consistency - Property 5', function () {
    // Feature: school-library, Property 5: Book Copy Availability Consistency
    // For any book copy, if it has an active loan, then the book copy status must be 'borrowed'.
    // If it has no active loan, the status must be 'available' or 'lost'.
    // Validates: Requirements 15.7, 16.5
    
    for ($i = 0; $i < 100; $i++) {
        // Create student and book copy
        $student = Student::factory()->create(['max_loan' => 5]);
        $book = Book::factory()->create(['stock' => 1]);
        $bookCopy = BookCopy::factory()->create([
            'book_id' => $book->id,
            'barcode' => $book->code . '-001',
            'status' => 'available',
        ]);
        
        // Initially, book copy should be available
        expect($bookCopy->status)->toBe('available');
        expect($bookCopy->isAvailable())->toBeTrue();
        
        // Create a loan
        $loan = $this->loanService->createLoan(
            $student,
            $bookCopy,
            LoanService::TYPE_REGULAR
        );
        
        // After loan, book copy status should be 'borrowed'
        $bookCopy->refresh();
        expect($bookCopy->status)->toBe('borrowed');
        expect($bookCopy->isAvailable())->toBeFalse();
        
        // Verify the loan is active
        expect($loan->status)->toBe('active');
        
        // Verify consistency: active loan implies borrowed status
        $activeLoan = $bookCopy->currentLoan;
        expect($activeLoan)->not->toBeNull();
        expect($activeLoan->status)->toBe('active');
        expect($bookCopy->status)->toBe('borrowed');
    }
});

it('prevents borrowing unavailable book copies - Property 5 negative case', function () {
    // Feature: school-library, Property 5: Book Copy Availability Consistency (negative case)
    // Attempting to borrow an unavailable book copy should fail.
    // Validates: Requirements 15.9
    
    for ($i = 0; $i < 100; $i++) {
        $student = Student::factory()->create(['max_loan' => 5]);
        $book = Book::factory()->create(['stock' => 1]);
        
        // Create a book copy that is already borrowed
        $bookCopy = BookCopy::factory()->create([
            'book_id' => $book->id,
            'barcode' => $book->code . '-001',
            'status' => 'borrowed',
        ]);
        
        // Attempting to borrow should throw an exception
        expect(fn () => $this->loanService->createLoan(
            $student,
            $bookCopy,
            LoanService::TYPE_REGULAR
        ))->toThrow(\InvalidArgumentException::class);
        
        // Book copy status should remain unchanged
        $bookCopy->refresh();
        expect($bookCopy->status)->toBe('borrowed');
    }
});

it('enforces loan date ordering - Property 14', function () {
    // Feature: school-library, Property 14: Loan Date Ordering
    // For any loan, the loan_date must be less than or equal to due_date.
    // Validates: Requirements 15.6
    
    for ($i = 0; $i < 100; $i++) {
        $student = Student::factory()->create(['max_loan' => 10]);
        $book = Book::factory()->create(['stock' => 1]);
        $bookCopy = BookCopy::factory()->create([
            'book_id' => $book->id,
            'barcode' => $book->code . '-001',
            'status' => 'available',
        ]);
        
        // Test with different loan types
        $loanType = fake()->randomElement([
            LoanService::TYPE_REGULAR,
            LoanService::TYPE_SEMESTER,
        ]);
        
        $loan = $this->loanService->createLoan(
            $student,
            $bookCopy,
            $loanType
        );
        
        // Verify date ordering: loan_date <= due_date
        expect($loan->loan_date->lte($loan->due_date))->toBeTrue();
        
        // Verify specific durations (use absolute diff)
        if ($loanType === LoanService::TYPE_REGULAR) {
            expect((int) $loan->loan_date->diffInDays($loan->due_date))->toBe(LoanService::DURATION_REGULAR);
        } elseif ($loanType === LoanService::TYPE_SEMESTER) {
            expect((int) $loan->loan_date->diffInDays($loan->due_date))->toBe(LoanService::DURATION_SEMESTER);
        }
    }
});

it('enforces loan date ordering with custom due date - Property 14 custom', function () {
    // Feature: school-library, Property 14: Loan Date Ordering (custom due date)
    // For custom loans, the custom due_date must be >= loan_date.
    // Validates: Requirements 15.6
    
    for ($i = 0; $i < 100; $i++) {
        $student = Student::factory()->create(['max_loan' => 10]);
        $book = Book::factory()->create(['stock' => 1]);
        $bookCopy = BookCopy::factory()->create([
            'book_id' => $book->id,
            'barcode' => $book->code . '-001',
            'status' => 'available',
        ]);
        
        // Generate a valid custom due date (today or future)
        $daysInFuture = fake()->numberBetween(1, 365);
        $customDueDate = Carbon::today()->addDays($daysInFuture);
        
        $loan = $this->loanService->createLoan(
            $student,
            $bookCopy,
            LoanService::TYPE_CUSTOM,
            $customDueDate
        );
        
        // Verify date ordering: loan_date <= due_date
        expect($loan->loan_date->lte($loan->due_date))->toBeTrue();
        expect($loan->due_date->format('Y-m-d'))->toBe($customDueDate->format('Y-m-d'));
    }
});

it('rejects invalid custom due date - Property 14 negative case', function () {
    // Feature: school-library, Property 14: Loan Date Ordering (negative case)
    // Custom due date in the past should be rejected.
    // Validates: Requirements 15.6
    
    for ($i = 0; $i < 100; $i++) {
        $student = Student::factory()->create(['max_loan' => 10]);
        $book = Book::factory()->create(['stock' => 1]);
        $bookCopy = BookCopy::factory()->create([
            'book_id' => $book->id,
            'barcode' => $book->code . '-001',
            'status' => 'available',
        ]);
        
        // Generate an invalid custom due date (in the past)
        $daysInPast = fake()->numberBetween(1, 30);
        $invalidDueDate = Carbon::today()->subDays($daysInPast);
        
        // Attempting to create loan with past due date should throw an exception
        expect(fn () => $this->loanService->createLoan(
            $student,
            $bookCopy,
            LoanService::TYPE_CUSTOM,
            $invalidDueDate
        ))->toThrow(\InvalidArgumentException::class);
        
        // Book copy should remain available
        $bookCopy->refresh();
        expect($bookCopy->status)->toBe('available');
    }
});

it('validates remaining loan slots calculation - Property 4 helper', function () {
    // Feature: school-library, Property 4: Loan Limit Enforcement (helper validation)
    // The remaining loan slots should always be accurate.
    // Validates: Requirements 15.8
    
    for ($i = 0; $i < 100; $i++) {
        $maxLoan = fake()->numberBetween(2, 5);
        $student = Student::factory()->create(['max_loan' => $maxLoan]);
        
        // Initially, remaining slots should equal max_loan
        expect($this->loanService->getRemainingLoanSlots($student))->toBe($maxLoan);
        expect($this->loanService->canStudentBorrow($student))->toBeTrue();
        
        // Create some loans
        $loansToCreate = fake()->numberBetween(1, $maxLoan - 1);
        $book = Book::factory()->create(['stock' => $loansToCreate]);
        
        for ($j = 0; $j < $loansToCreate; $j++) {
            $bookCopy = BookCopy::factory()->create([
                'book_id' => $book->id,
                'barcode' => $book->code . '-' . str_pad($j + 1, 3, '0', STR_PAD_LEFT),
                'status' => 'available',
            ]);
            
            $this->loanService->createLoan($student, $bookCopy, LoanService::TYPE_REGULAR);
        }
        
        // Verify remaining slots
        $student->refresh();
        $expectedRemaining = $maxLoan - $loansToCreate;
        expect($this->loanService->getRemainingLoanSlots($student))->toBe($expectedRemaining);
        expect($this->loanService->canStudentBorrow($student))->toBe($expectedRemaining > 0);
    }
});

it('maintains consistency across multiple loans - Property 4 and 5 combined', function () {
    // Feature: school-library, Property 4 & 5: Combined consistency test
    // Multiple loans should maintain both loan limit and availability consistency.
    // Validates: Requirements 15.7, 15.8
    
    for ($i = 0; $i < 100; $i++) {
        $maxLoan = fake()->numberBetween(2, 4);
        $student = Student::factory()->create(['max_loan' => $maxLoan]);
        $book = Book::factory()->create(['stock' => $maxLoan]);
        
        $bookCopies = [];
        for ($j = 0; $j < $maxLoan; $j++) {
            $bookCopies[] = BookCopy::factory()->create([
                'book_id' => $book->id,
                'barcode' => $book->code . '-' . str_pad($j + 1, 3, '0', STR_PAD_LEFT),
                'status' => 'available',
            ]);
        }
        
        // Create all possible loans
        $loans = [];
        for ($j = 0; $j < $maxLoan; $j++) {
            $loans[] = $this->loanService->createLoan(
                $student,
                $bookCopies[$j],
                LoanService::TYPE_REGULAR
            );
        }
        
        // Verify all constraints
        $student->refresh();
        
        // Property 4: Active loans count should equal max_loan
        expect($student->activeLoans()->count())->toBe($maxLoan);
        expect($student->activeLoans()->count())->toBeLessThanOrEqual($student->max_loan);
        
        // Property 5: All borrowed book copies should have 'borrowed' status
        foreach ($bookCopies as $bookCopy) {
            $bookCopy->refresh();
            expect($bookCopy->status)->toBe('borrowed');
            expect($bookCopy->currentLoan)->not->toBeNull();
        }
        
        // Property 14: All loans should have valid date ordering
        foreach ($loans as $loan) {
            expect($loan->loan_date->lte($loan->due_date))->toBeTrue();
        }
    }
});
