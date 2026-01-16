<?php

/**
 * Feature: textbook-flag, Property 1, 2: Textbook Flag Properties
 * 
 * Property 1: Default Textbook Value - *For any* newly created Book without explicitly setting `is_textbook`, 
 *             the `is_textbook` attribute SHALL be `false`.
 * Property 2: Textbook Persistence Round-Trip - *For any* Book saved with `is_textbook` set to `true` or `false`, 
 *             retrieving the Book from the database SHALL return the same `is_textbook` value.
 * 
 * **Validates: Requirements 1.1, 1.4**
 */

use App\Models\Book;

it('defaults is_textbook to false for newly created books - Property 1', function () {
    // Feature: textbook-flag, Property 1: Default Textbook Value
    // For any newly created Book without explicitly setting is_textbook, 
    // the is_textbook attribute SHALL be false.
    // Validates: Requirements 1.1
    
    for ($i = 0; $i < 100; $i++) {
        // Create a book without explicitly setting is_textbook
        $book = Book::factory()->create();
        
        // Refresh from database to ensure we're testing persisted value
        $book->refresh();
        
        // The is_textbook attribute should default to false
        expect($book->is_textbook)->toBeFalse();
        expect($book->is_textbook)->toBeBool();
    }
});

it('persists is_textbook value correctly through round-trip - Property 2', function () {
    // Feature: textbook-flag, Property 2: Textbook Persistence Round-Trip
    // For any Book saved with is_textbook set to true or false, 
    // retrieving the Book from the database SHALL return the same is_textbook value.
    // Validates: Requirements 1.4
    
    for ($i = 0; $i < 100; $i++) {
        // Generate a random boolean value for is_textbook
        $isTextbook = fake()->boolean();
        
        // Create a book with the random is_textbook value
        $book = Book::factory()->create([
            'is_textbook' => $isTextbook,
        ]);
        
        // Retrieve the book fresh from the database
        $retrievedBook = Book::find($book->id);
        
        // The retrieved is_textbook value should match the original
        expect($retrievedBook->is_textbook)->toBe($isTextbook);
        expect($retrievedBook->is_textbook)->toBeBool();
    }
});


it('excludes textbook loans from loan count calculation - Property 3', function () {
    // Feature: textbook-flag, Property 3: Loan Count Excludes Textbooks
    // For any Student with active loans, the loan count used for limit validation 
    // SHALL equal the count of active loans where the associated Book has is_textbook = false.
    // Validates: Requirements 2.1, 2.3, 2.4
    
    $loanService = new \App\Services\LoanService();
    
    for ($i = 0; $i < 100; $i++) {
        // Create a student with random max_loan between 3 and 6
        $maxLoan = fake()->numberBetween(3, 6);
        $student = \App\Models\Student::factory()->create(['max_loan' => $maxLoan]);
        
        // Generate random number of textbook and non-textbook loans
        $textbookLoansCount = fake()->numberBetween(0, 3);
        $nonTextbookLoansCount = fake()->numberBetween(0, min(2, $maxLoan - 1));
        
        // Create textbook loans
        for ($j = 0; $j < $textbookLoansCount; $j++) {
            $textbook = \App\Models\Book::factory()->create([
                'is_textbook' => true,
                'stock' => 1,
            ]);
            $textbookCopy = \App\Models\BookCopy::factory()->create([
                'book_id' => $textbook->id,
                'barcode' => $textbook->code . '-' . str_pad($j + 1, 3, '0', STR_PAD_LEFT),
                'status' => 'borrowed',
            ]);
            \App\Models\Loan::factory()->create([
                'student_id' => $student->id,
                'book_copy_id' => $textbookCopy->id,
                'status' => 'active',
            ]);
        }
        
        // Create non-textbook loans
        for ($j = 0; $j < $nonTextbookLoansCount; $j++) {
            $regularBook = \App\Models\Book::factory()->create([
                'is_textbook' => false,
                'stock' => 1,
            ]);
            $regularCopy = \App\Models\BookCopy::factory()->create([
                'book_id' => $regularBook->id,
                'barcode' => $regularBook->code . '-' . str_pad($j + 1, 3, '0', STR_PAD_LEFT),
                'status' => 'borrowed',
            ]);
            \App\Models\Loan::factory()->create([
                'student_id' => $student->id,
                'book_copy_id' => $regularCopy->id,
                'status' => 'active',
            ]);
        }
        
        // Refresh student to get updated relationships
        $student->refresh();
        
        // Total active loans should include both textbook and non-textbook
        $totalActiveLoans = $student->activeLoans()->count();
        expect($totalActiveLoans)->toBe($textbookLoansCount + $nonTextbookLoansCount);
        
        // Non-textbook loan count should only count non-textbook loans
        $nonTextbookCount = $loanService->getNonTextbookActiveLoansCount($student);
        expect($nonTextbookCount)->toBe($nonTextbookLoansCount);
        
        // Remaining loan slots should be based on non-textbook loans only
        $remainingSlots = $loanService->getRemainingLoanSlots($student);
        expect($remainingSlots)->toBe($maxLoan - $nonTextbookLoansCount);
        
        // canStudentBorrow should be based on non-textbook loans only
        $canBorrow = $loanService->canStudentBorrow($student);
        expect($canBorrow)->toBe($nonTextbookLoansCount < $maxLoan);
    }
});


it('allows textbook borrowing when at loan limit - Property 4', function () {
    // Feature: textbook-flag, Property 4: Textbook Borrowing Allowed at Limit
    // For any Student who has reached their loan limit with non-textbook books, 
    // attempting to borrow a Book where is_textbook = true SHALL succeed (not throw loan limit exception).
    // Validates: Requirements 2.2
    
    $loanService = new \App\Services\LoanService();
    
    for ($i = 0; $i < 100; $i++) {
        // Create a student with random max_loan between 1 and 3
        $maxLoan = fake()->numberBetween(1, 3);
        $student = \App\Models\Student::factory()->create(['max_loan' => $maxLoan]);
        
        // Fill up the student's loan limit with non-textbook books
        for ($j = 0; $j < $maxLoan; $j++) {
            $regularBook = \App\Models\Book::factory()->create([
                'is_textbook' => false,
                'stock' => 1,
            ]);
            $regularCopy = \App\Models\BookCopy::factory()->create([
                'book_id' => $regularBook->id,
                'barcode' => $regularBook->code . '-' . str_pad($j + 1, 3, '0', STR_PAD_LEFT),
                'status' => 'available',
            ]);
            $loanService->createLoan($student, $regularCopy, \App\Services\LoanService::TYPE_REGULAR);
        }
        
        // Verify student is at loan limit for non-textbook books
        $student->refresh();
        expect($loanService->getNonTextbookActiveLoansCount($student))->toBe($maxLoan);
        expect($loanService->getRemainingLoanSlots($student))->toBe(0);
        
        // Create a textbook and try to borrow it
        $textbook = \App\Models\Book::factory()->create([
            'is_textbook' => true,
            'stock' => 1,
        ]);
        $textbookCopy = \App\Models\BookCopy::factory()->create([
            'book_id' => $textbook->id,
            'barcode' => $textbook->code . '-001',
            'status' => 'available',
        ]);
        
        // Borrowing a textbook should succeed even at loan limit
        $loan = $loanService->createLoan($student, $textbookCopy, \App\Services\LoanService::TYPE_REGULAR);
        
        expect($loan)->toBeInstanceOf(\App\Models\Loan::class);
        expect($loan->status)->toBe('active');
        
        // Verify the textbook loan was created
        $student->refresh();
        expect($student->activeLoans()->count())->toBe($maxLoan + 1);
        
        // Non-textbook count should still be at max_loan
        expect($loanService->getNonTextbookActiveLoansCount($student))->toBe($maxLoan);
        
        // Remaining slots should still be 0 (based on non-textbook loans)
        expect($loanService->getRemainingLoanSlots($student))->toBe(0);
    }
});


it('filter returns correct books based on textbook status - Property 5', function () {
    // Feature: textbook-flag, Property 5: Filter Returns Correct Books
    // For any filter selection (textbook/non-textbook), all returned Books 
    // SHALL have is_textbook value matching the filter criteria.
    // Validates: Requirements 3.2
    
    for ($i = 0; $i < 100; $i++) {
        // Create a random mix of textbook and non-textbook books
        $textbookCount = fake()->numberBetween(1, 5);
        $nonTextbookCount = fake()->numberBetween(1, 5);
        
        $textbooks = [];
        $nonTextbooks = [];
        
        // Create textbooks
        for ($j = 0; $j < $textbookCount; $j++) {
            $textbooks[] = \App\Models\Book::factory()->create([
                'is_textbook' => true,
            ]);
        }
        
        // Create non-textbooks
        for ($j = 0; $j < $nonTextbookCount; $j++) {
            $nonTextbooks[] = \App\Models\Book::factory()->create([
                'is_textbook' => false,
            ]);
        }
        
        // Test filter for textbooks only (filterTextbook = '1')
        $filteredTextbooks = \App\Models\Book::query()
            ->where('is_textbook', true)
            ->get();
        
        // All filtered results should be textbooks
        foreach ($filteredTextbooks as $book) {
            expect($book->is_textbook)->toBeTrue();
        }
        
        // Count should match created textbooks (at minimum)
        expect($filteredTextbooks->count())->toBeGreaterThanOrEqual($textbookCount);
        
        // Test filter for non-textbooks only (filterTextbook = '0')
        $filteredNonTextbooks = \App\Models\Book::query()
            ->where('is_textbook', false)
            ->get();
        
        // All filtered results should be non-textbooks
        foreach ($filteredNonTextbooks as $book) {
            expect($book->is_textbook)->toBeFalse();
        }
        
        // Count should match created non-textbooks (at minimum)
        expect($filteredNonTextbooks->count())->toBeGreaterThanOrEqual($nonTextbookCount);
        
        // Test no filter (filterTextbook = '') - should return all books
        $allBooks = \App\Models\Book::query()->get();
        expect($allBooks->count())->toBeGreaterThanOrEqual($textbookCount + $nonTextbookCount);
        
        // Clean up for next iteration
        \App\Models\Book::whereIn('id', collect($textbooks)->pluck('id'))->forceDelete();
        \App\Models\Book::whereIn('id', collect($nonTextbooks)->pluck('id'))->forceDelete();
    }
});
