<?php

/**
 * Feature: livewire-button-fix, Loan Form Tests
 * 
 * Tests for the LoanForm Livewire component functionality.
 * 
 * **Validates: Requirements 3.1, 3.2, 3.3, 3.4, 3.5**
 */

use App\Livewire\Transaction\LoanForm;
use App\Models\Book;
use App\Models\BookCopy;
use App\Models\Student;
use Livewire\Livewire;

/**
 * Task 4.1: Test student search and selection
 * Verifies that computed property students returns matching results
 * and selectStudent() method changes state and closes modal
 * 
 * **Validates: Requirements 3.1, 3.2**
 */
describe('Student Search and Selection', function () {
    
    it('getStudentsProperty returns empty collection when search is empty', function () {
        $component = Livewire::test(LoanForm::class)
            ->assertSet('studentSearch', '');
        
        $students = $component->get('students');
        expect($students)->toBeEmpty();
    });

    it('getStudentsProperty returns students matching NIS search', function () {
        // Create students with specific NIS
        $student1 = Student::factory()->create(['nis' => '1234567890', 'is_active' => true]);
        $student2 = Student::factory()->create(['nis' => '1234500000', 'is_active' => true]);
        $student3 = Student::factory()->create(['nis' => '9999999999', 'is_active' => true]);
        
        $component = Livewire::test(LoanForm::class)
            ->set('studentSearch', '12345');
        
        $students = $component->get('students');
        
        expect($students)->toHaveCount(2);
        expect($students->pluck('id')->toArray())->toContain($student1->id);
        expect($students->pluck('id')->toArray())->toContain($student2->id);
        expect($students->pluck('id')->toArray())->not->toContain($student3->id);
    });

    it('getStudentsProperty returns students matching name search', function () {
        // Create students with specific names
        $student1 = Student::factory()->create(['name' => 'John Doe', 'is_active' => true]);
        $student2 = Student::factory()->create(['name' => 'Jane Doe', 'is_active' => true]);
        $student3 = Student::factory()->create(['name' => 'Bob Smith', 'is_active' => true]);
        
        $component = Livewire::test(LoanForm::class)
            ->set('studentSearch', 'Doe');
        
        $students = $component->get('students');
        
        expect($students)->toHaveCount(2);
        expect($students->pluck('id')->toArray())->toContain($student1->id);
        expect($students->pluck('id')->toArray())->toContain($student2->id);
        expect($students->pluck('id')->toArray())->not->toContain($student3->id);
    });

    it('getStudentsProperty only returns active students', function () {
        // Create active and inactive students
        $activeStudent = Student::factory()->create(['name' => 'Active Student', 'is_active' => true]);
        $inactiveStudent = Student::factory()->create(['name' => 'Inactive Student', 'is_active' => false]);
        
        $component = Livewire::test(LoanForm::class)
            ->set('studentSearch', 'Student');
        
        $students = $component->get('students');
        
        expect($students)->toHaveCount(1);
        expect($students->first()->id)->toBe($activeStudent->id);
    });

    it('selectStudent method updates state and closes modal', function () {
        $student = Student::factory()->create(['is_active' => true]);
        
        $component = Livewire::test(LoanForm::class)
            ->set('showStudentModal', true)
            ->set('studentSearch', 'test')
            ->call('selectStudent', $student->id)
            ->assertSet('studentId', $student->id)
            ->assertSet('showStudentModal', false)
            ->assertSet('studentSearch', '');
        
        // Verify selectedStudent is loaded
        $selectedStudent = $component->get('selectedStudent');
        expect($selectedStudent)->not->toBeNull();
        expect($selectedStudent->id)->toBe($student->id);
    });

    it('selectStudent loads student with relationships', function () {
        $student = Student::factory()->create(['is_active' => true]);
        
        $component = Livewire::test(LoanForm::class)
            ->call('selectStudent', $student->id);
        
        $selectedStudent = $component->get('selectedStudent');
        
        // Verify relationships are loaded
        expect($selectedStudent->relationLoaded('class'))->toBeTrue();
        expect($selectedStudent->relationLoaded('major'))->toBeTrue();
        expect($selectedStudent->relationLoaded('academicYear'))->toBeTrue();
    });

    it('openStudentModal opens modal and clears search', function () {
        Livewire::test(LoanForm::class)
            ->set('studentSearch', 'previous search')
            ->call('openStudentModal')
            ->assertSet('showStudentModal', true)
            ->assertSet('studentSearch', '');
    });

    it('closeStudentModal closes modal and clears search', function () {
        Livewire::test(LoanForm::class)
            ->set('showStudentModal', true)
            ->set('studentSearch', 'test')
            ->call('closeStudentModal')
            ->assertSet('showStudentModal', false)
            ->assertSet('studentSearch', '');
    });

    it('clearStudent resets student selection', function () {
        $student = Student::factory()->create(['is_active' => true]);
        
        Livewire::test(LoanForm::class)
            ->call('selectStudent', $student->id)
            ->assertSet('studentId', $student->id)
            ->call('clearStudent')
            ->assertSet('studentId', null)
            ->assertSet('selectedStudent', null);
    });
});


/**
 * Task 4.2: Test book search and selection
 * Verifies that computed property bookCopies returns matching results
 * and selectBookCopy() method changes state and closes modal
 * 
 * **Validates: Requirements 3.3, 3.4**
 */
describe('Book Search and Selection', function () {
    
    it('getBookCopiesProperty returns empty collection when search is empty', function () {
        $component = Livewire::test(LoanForm::class)
            ->assertSet('bookSearch', '');
        
        $bookCopies = $component->get('bookCopies');
        expect($bookCopies)->toBeEmpty();
    });

    it('getBookCopiesProperty returns book copies matching barcode search', function () {
        // Create books with copies
        $book1 = Book::factory()->create();
        $copy1 = BookCopy::factory()->create(['book_id' => $book1->id, 'barcode' => 'BC-12345-001', 'status' => 'available']);
        $copy2 = BookCopy::factory()->create(['book_id' => $book1->id, 'barcode' => 'BC-12345-002', 'status' => 'available']);
        $copy3 = BookCopy::factory()->create(['book_id' => $book1->id, 'barcode' => 'BC-99999-001', 'status' => 'available']);
        
        $component = Livewire::test(LoanForm::class)
            ->set('bookSearch', '12345');
        
        $bookCopies = $component->get('bookCopies');
        
        expect($bookCopies)->toHaveCount(2);
        expect($bookCopies->pluck('id')->toArray())->toContain($copy1->id);
        expect($bookCopies->pluck('id')->toArray())->toContain($copy2->id);
        expect($bookCopies->pluck('id')->toArray())->not->toContain($copy3->id);
    });

    it('getBookCopiesProperty returns book copies matching book title search', function () {
        // Create books with specific titles
        $book1 = Book::factory()->create(['title' => 'Laravel Programming']);
        $book2 = Book::factory()->create(['title' => 'PHP Laravel Guide']);
        $book3 = Book::factory()->create(['title' => 'Python Basics']);
        
        $copy1 = BookCopy::factory()->create(['book_id' => $book1->id, 'status' => 'available']);
        $copy2 = BookCopy::factory()->create(['book_id' => $book2->id, 'status' => 'available']);
        $copy3 = BookCopy::factory()->create(['book_id' => $book3->id, 'status' => 'available']);
        
        $component = Livewire::test(LoanForm::class)
            ->set('bookSearch', 'Laravel');
        
        $bookCopies = $component->get('bookCopies');
        
        expect($bookCopies)->toHaveCount(2);
        expect($bookCopies->pluck('id')->toArray())->toContain($copy1->id);
        expect($bookCopies->pluck('id')->toArray())->toContain($copy2->id);
        expect($bookCopies->pluck('id')->toArray())->not->toContain($copy3->id);
    });

    it('getBookCopiesProperty returns book copies matching book code search', function () {
        // Create books with specific codes
        $book1 = Book::factory()->create(['code' => 'BK-001']);
        $book2 = Book::factory()->create(['code' => 'BK-002']);
        $book3 = Book::factory()->create(['code' => 'XX-999']);
        
        $copy1 = BookCopy::factory()->create(['book_id' => $book1->id, 'status' => 'available']);
        $copy2 = BookCopy::factory()->create(['book_id' => $book2->id, 'status' => 'available']);
        $copy3 = BookCopy::factory()->create(['book_id' => $book3->id, 'status' => 'available']);
        
        $component = Livewire::test(LoanForm::class)
            ->set('bookSearch', 'BK-00');
        
        $bookCopies = $component->get('bookCopies');
        
        expect($bookCopies)->toHaveCount(2);
        expect($bookCopies->pluck('id')->toArray())->toContain($copy1->id);
        expect($bookCopies->pluck('id')->toArray())->toContain($copy2->id);
        expect($bookCopies->pluck('id')->toArray())->not->toContain($copy3->id);
    });

    it('getBookCopiesProperty only returns available book copies', function () {
        // Create book with copies of different statuses
        $book = Book::factory()->create();
        $availableCopy = BookCopy::factory()->create(['book_id' => $book->id, 'barcode' => 'TEST-001', 'status' => 'available']);
        $borrowedCopy = BookCopy::factory()->create(['book_id' => $book->id, 'barcode' => 'TEST-002', 'status' => 'borrowed']);
        $lostCopy = BookCopy::factory()->create(['book_id' => $book->id, 'barcode' => 'TEST-003', 'status' => 'lost']);
        
        $component = Livewire::test(LoanForm::class)
            ->set('bookSearch', 'TEST');
        
        $bookCopies = $component->get('bookCopies');
        
        expect($bookCopies)->toHaveCount(1);
        expect($bookCopies->first()->id)->toBe($availableCopy->id);
    });

    it('selectBookCopy method updates state and closes modal', function () {
        $book = Book::factory()->create();
        $bookCopy = BookCopy::factory()->create(['book_id' => $book->id, 'status' => 'available']);
        
        $component = Livewire::test(LoanForm::class)
            ->set('showBookModal', true)
            ->set('bookSearch', 'test')
            ->call('selectBookCopy', $bookCopy->id)
            ->assertSet('bookCopyId', $bookCopy->id)
            ->assertSet('showBookModal', false)
            ->assertSet('bookSearch', '');
        
        // Verify selectedBookCopy is loaded
        $selectedBookCopy = $component->get('selectedBookCopy');
        expect($selectedBookCopy)->not->toBeNull();
        expect($selectedBookCopy->id)->toBe($bookCopy->id);
    });

    it('selectBookCopy loads book copy with book relationship', function () {
        $book = Book::factory()->create();
        $bookCopy = BookCopy::factory()->create(['book_id' => $book->id, 'status' => 'available']);
        
        $component = Livewire::test(LoanForm::class)
            ->call('selectBookCopy', $bookCopy->id);
        
        $selectedBookCopy = $component->get('selectedBookCopy');
        
        // Verify book relationship is loaded
        expect($selectedBookCopy->relationLoaded('book'))->toBeTrue();
        expect($selectedBookCopy->book->id)->toBe($book->id);
    });

    it('selectBookCopy rejects unavailable book copies', function () {
        $book = Book::factory()->create();
        $borrowedCopy = BookCopy::factory()->create(['book_id' => $book->id, 'status' => 'borrowed']);
        
        $component = Livewire::test(LoanForm::class)
            ->call('selectBookCopy', $borrowedCopy->id)
            ->assertSet('bookCopyId', null)
            ->assertSet('selectedBookCopy', null);
        
        // Verify error message is set
        $errorMessage = $component->get('errorMessage');
        expect($errorMessage)->toContain('tidak tersedia');
    });

    it('openBookModal opens modal and clears search', function () {
        Livewire::test(LoanForm::class)
            ->set('bookSearch', 'previous search')
            ->call('openBookModal')
            ->assertSet('showBookModal', true)
            ->assertSet('bookSearch', '');
    });

    it('closeBookModal closes modal and clears search', function () {
        Livewire::test(LoanForm::class)
            ->set('showBookModal', true)
            ->set('bookSearch', 'test')
            ->call('closeBookModal')
            ->assertSet('showBookModal', false)
            ->assertSet('bookSearch', '');
    });

    it('clearBookCopy resets book selection', function () {
        $book = Book::factory()->create();
        $bookCopy = BookCopy::factory()->create(['book_id' => $book->id, 'status' => 'available']);
        
        Livewire::test(LoanForm::class)
            ->call('selectBookCopy', $bookCopy->id)
            ->assertSet('bookCopyId', $bookCopy->id)
            ->call('clearBookCopy')
            ->assertSet('bookCopyId', null)
            ->assertSet('selectedBookCopy', null);
    });
});


/**
 * Task 4.3: Property Test for Search Results Matching
 * 
 * Feature: livewire-button-fix, Property 3: Search Results Match Query
 * *For any* search query string, all returned results (students or books) SHALL contain 
 * the query string in their searchable fields (NIS, name, barcode, title, or code).
 * 
 * **Validates: Requirements 3.1, 3.3**
 */
describe('Property Tests - Search Results Matching', function () {
    
    it('all student search results contain query in searchable fields - Property 3', function () {
        // Feature: livewire-button-fix, Property 3: Search Results Match Query
        // For any search query string, all returned student results SHALL contain 
        // the query string in their searchable fields (NIS or name).
        // Validates: Requirements 3.1
        
        for ($i = 0; $i < 100; $i++) {
            // Create a pool of students with random data
            $students = Student::factory()->count(fake()->numberBetween(3, 8))->create(['is_active' => true]);
            
            // Pick a random student and extract a search substring from their NIS or name
            $targetStudent = $students->random();
            $searchField = fake()->randomElement(['nis', 'name']);
            $fieldValue = $targetStudent->$searchField;
            
            // Generate a random substring from the field value (at least 2 chars)
            $startPos = fake()->numberBetween(0, max(0, strlen($fieldValue) - 3));
            $length = fake()->numberBetween(2, min(5, strlen($fieldValue) - $startPos));
            $searchQuery = substr($fieldValue, $startPos, $length);
            
            // Skip if search query is too short
            if (strlen($searchQuery) < 2) {
                continue;
            }
            
            // Perform the search
            $component = Livewire::test(LoanForm::class)
                ->set('studentSearch', $searchQuery);
            
            $results = $component->get('students');
            
            // Verify all results contain the query in searchable fields
            foreach ($results as $student) {
                $containsInNis = stripos($student->nis, $searchQuery) !== false;
                $containsInName = stripos($student->name, $searchQuery) !== false;
                
                expect($containsInNis || $containsInName)->toBeTrue(
                    "Student {$student->id} (NIS: {$student->nis}, Name: {$student->name}) " .
                    "does not contain search query '{$searchQuery}' in searchable fields"
                );
            }
        }
    });

    it('all book copy search results contain query in searchable fields - Property 3', function () {
        // Feature: livewire-button-fix, Property 3: Search Results Match Query
        // For any search query string, all returned book copy results SHALL contain 
        // the query string in their searchable fields (barcode, title, or code).
        // Validates: Requirements 3.3
        
        for ($i = 0; $i < 100; $i++) {
            // Create a pool of books with copies
            $books = Book::factory()->count(fake()->numberBetween(3, 6))->create();
            $bookCopies = collect();
            
            foreach ($books as $book) {
                $copies = BookCopy::factory()
                    ->count(fake()->numberBetween(1, 3))
                    ->create(['book_id' => $book->id, 'status' => 'available']);
                $bookCopies = $bookCopies->merge($copies);
            }
            
            // Pick a random book copy and extract a search substring
            $targetCopy = $bookCopies->random();
            $targetBook = $targetCopy->book;
            
            // Choose a random searchable field
            $searchField = fake()->randomElement(['barcode', 'title', 'code']);
            $fieldValue = $searchField === 'barcode' ? $targetCopy->barcode : $targetBook->$searchField;
            
            // Generate a random substring from the field value (at least 2 chars)
            $startPos = fake()->numberBetween(0, max(0, strlen($fieldValue) - 3));
            $length = fake()->numberBetween(2, min(5, strlen($fieldValue) - $startPos));
            $searchQuery = substr($fieldValue, $startPos, $length);
            
            // Skip if search query is too short
            if (strlen($searchQuery) < 2) {
                continue;
            }
            
            // Perform the search
            $component = Livewire::test(LoanForm::class)
                ->set('bookSearch', $searchQuery);
            
            $results = $component->get('bookCopies');
            
            // Verify all results contain the query in searchable fields
            foreach ($results as $bookCopy) {
                $containsInBarcode = stripos($bookCopy->barcode, $searchQuery) !== false;
                $containsInTitle = stripos($bookCopy->book->title, $searchQuery) !== false;
                $containsInCode = stripos($bookCopy->book->code, $searchQuery) !== false;
                
                expect($containsInBarcode || $containsInTitle || $containsInCode)->toBeTrue(
                    "BookCopy {$bookCopy->id} (Barcode: {$bookCopy->barcode}, " .
                    "Title: {$bookCopy->book->title}, Code: {$bookCopy->book->code}) " .
                    "does not contain search query '{$searchQuery}' in searchable fields"
                );
            }
        }
    });
});


/**
 * Task 4.4: Property Test for Barcode Scan
 * 
 * Feature: livewire-button-fix, Property 5: Barcode Scan Selects Correct Book
 * *For any* valid barcode string that exists in the database with status 'available', 
 * scanning SHALL select the book copy with that exact barcode.
 * 
 * **Validates: Requirements 3.5**
 */
describe('Property Tests - Barcode Scan', function () {
    
    it('scanning valid available barcode selects correct book copy - Property 5', function () {
        // Feature: livewire-button-fix, Property 5: Barcode Scan Selects Correct Book
        // For any valid barcode string that exists in the database with status 'available', 
        // scanning SHALL select the book copy with that exact barcode.
        // Validates: Requirements 3.5
        
        for ($i = 0; $i < 100; $i++) {
            // Create a pool of books with copies
            $books = Book::factory()->count(fake()->numberBetween(2, 5))->create();
            $availableCopies = collect();
            
            foreach ($books as $book) {
                // Create some available and some unavailable copies
                $available = BookCopy::factory()
                    ->count(fake()->numberBetween(1, 3))
                    ->create(['book_id' => $book->id, 'status' => 'available']);
                $availableCopies = $availableCopies->merge($available);
                
                // Also create some borrowed copies to ensure we're selecting the right one
                BookCopy::factory()
                    ->count(fake()->numberBetween(0, 2))
                    ->create(['book_id' => $book->id, 'status' => 'borrowed']);
            }
            
            // Pick a random available copy to scan
            $targetCopy = $availableCopies->random();
            $barcode = $targetCopy->barcode;
            
            // Perform the barcode scan
            $component = Livewire::test(LoanForm::class)
                ->set('barcodeInput', $barcode)
                ->call('scanBarcode');
            
            // Verify the correct book copy is selected
            $selectedBookCopyId = $component->get('bookCopyId');
            $selectedBookCopy = $component->get('selectedBookCopy');
            
            expect($selectedBookCopyId)->toBe($targetCopy->id);
            expect($selectedBookCopy)->not->toBeNull();
            expect($selectedBookCopy->id)->toBe($targetCopy->id);
            expect($selectedBookCopy->barcode)->toBe($barcode);
            
            // Verify barcode input is cleared after scan
            expect($component->get('barcodeInput'))->toBe('');
        }
    });

    it('scanning unavailable barcode does not select book copy - Property 5 edge case', function () {
        // Feature: livewire-button-fix, Property 5: Barcode Scan Selects Correct Book (edge case)
        // For any barcode with status other than 'available', scanning SHALL NOT select the book copy.
        // Validates: Requirements 3.5
        
        for ($i = 0; $i < 100; $i++) {
            // Create a book with unavailable copies
            $book = Book::factory()->create();
            $status = fake()->randomElement(['borrowed', 'lost']);
            $unavailableCopy = BookCopy::factory()->create([
                'book_id' => $book->id, 
                'status' => $status
            ]);
            
            $barcode = $unavailableCopy->barcode;
            
            // Perform the barcode scan
            $component = Livewire::test(LoanForm::class)
                ->set('barcodeInput', $barcode)
                ->call('scanBarcode');
            
            // Verify no book copy is selected
            expect($component->get('bookCopyId'))->toBeNull();
            expect($component->get('selectedBookCopy'))->toBeNull();
            
            // Verify error message is set
            $errorMessage = $component->get('errorMessage');
            expect($errorMessage)->toContain('tidak tersedia');
            
            // Verify barcode input is cleared
            expect($component->get('barcodeInput'))->toBe('');
        }
    });

    it('scanning non-existent barcode does not select book copy - Property 5 edge case', function () {
        // Feature: livewire-button-fix, Property 5: Barcode Scan Selects Correct Book (edge case)
        // For any barcode that does not exist in the database, scanning SHALL NOT select any book copy.
        // Validates: Requirements 3.5
        
        for ($i = 0; $i < 100; $i++) {
            // Generate a random barcode that doesn't exist
            $nonExistentBarcode = 'NONEXISTENT-' . fake()->uuid();
            
            // Perform the barcode scan
            $component = Livewire::test(LoanForm::class)
                ->set('barcodeInput', $nonExistentBarcode)
                ->call('scanBarcode');
            
            // Verify no book copy is selected
            expect($component->get('bookCopyId'))->toBeNull();
            expect($component->get('selectedBookCopy'))->toBeNull();
            
            // Verify error message is set
            $errorMessage = $component->get('errorMessage');
            expect($errorMessage)->toContain('tidak ditemukan');
            
            // Verify barcode input is cleared
            expect($component->get('barcodeInput'))->toBe('');
        }
    });
});
