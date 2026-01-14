<?php

/**
 * Feature: school-library, Property 15: Dashboard Statistics Accuracy
 * 
 * *For any* dashboard view, the total books count must equal the sum of all book copies,
 * total titles must equal the count of distinct books, and active students must equal
 * students where is_active=true.
 * 
 * **Validates: Requirements 1.1, 1.2, 1.3**
 */

use App\Models\Book;
use App\Models\BookCopy;
use App\Models\Fine;
use App\Models\Loan;
use App\Models\Student;
use App\Services\DashboardService;

beforeEach(function () {
    $this->dashboardService = new DashboardService();
});

it('returns accurate total books count equal to book copies count - Property 15', function () {
    // Feature: school-library, Property 15: Dashboard Statistics Accuracy
    // For any set of book copies, getTotalBooks() must equal BookCopy::count()
    // Validates: Requirements 1.1
    
    for ($i = 0; $i < 100; $i++) {
        // Create random number of books with random copies
        $numBooks = fake()->numberBetween(0, 5);
        
        for ($j = 0; $j < $numBooks; $j++) {
            $book = Book::factory()->create();
            $numCopies = fake()->numberBetween(1, 5);
            
            for ($k = 0; $k < $numCopies; $k++) {
                BookCopy::factory()->create(['book_id' => $book->id]);
            }
        }
        
        // Verify dashboard service returns correct count
        $dashboardCount = $this->dashboardService->getTotalBooks();
        $actualCount = BookCopy::count();
        
        expect($dashboardCount)->toBe($actualCount);
        
        // Clean up for next iteration
        BookCopy::query()->delete();
        Book::query()->forceDelete();
    }
});

it('returns accurate total titles count equal to books count - Property 15', function () {
    // Feature: school-library, Property 15: Dashboard Statistics Accuracy
    // For any set of books, getTotalTitles() must equal Book::count()
    // Validates: Requirements 1.2
    
    for ($i = 0; $i < 100; $i++) {
        // Create random number of books
        $numBooks = fake()->numberBetween(0, 10);
        Book::factory()->count($numBooks)->create();
        
        // Verify dashboard service returns correct count
        $dashboardCount = $this->dashboardService->getTotalTitles();
        $actualCount = Book::count();
        
        expect($dashboardCount)->toBe($actualCount);
        
        // Clean up for next iteration
        Book::query()->forceDelete();
    }
});

it('returns accurate active students count - Property 15', function () {
    // Feature: school-library, Property 15: Dashboard Statistics Accuracy
    // For any set of students, getActiveStudents() must equal Student::where('is_active', true)->count()
    // Validates: Requirements 1.3
    
    for ($i = 0; $i < 100; $i++) {
        // Create random mix of active and inactive students
        $numActive = fake()->numberBetween(0, 5);
        $numInactive = fake()->numberBetween(0, 5);
        
        Student::factory()->count($numActive)->create(['is_active' => true]);
        Student::factory()->count($numInactive)->create(['is_active' => false]);
        
        // Verify dashboard service returns correct count
        $dashboardCount = $this->dashboardService->getActiveStudents();
        $actualCount = Student::where('is_active', true)->count();
        
        expect($dashboardCount)->toBe($actualCount);
        expect($dashboardCount)->toBe($numActive);
        
        // Clean up for next iteration
        Student::query()->forceDelete();
    }
});

it('returns accurate active loans count - Property 15 extension', function () {
    // Feature: school-library, Property 15: Dashboard Statistics Accuracy (extension)
    // For any set of loans, getActiveLoansCount() must equal Loan::where('status', 'active')->count()
    
    for ($i = 0; $i < 100; $i++) {
        // Create random mix of active and returned loans
        $numActive = fake()->numberBetween(0, 5);
        $numReturned = fake()->numberBetween(0, 5);
        
        for ($j = 0; $j < $numActive; $j++) {
            Loan::factory()->create(['status' => 'active']);
        }
        
        for ($j = 0; $j < $numReturned; $j++) {
            Loan::factory()->create(['status' => 'returned']);
        }
        
        // Verify dashboard service returns correct count
        $dashboardCount = $this->dashboardService->getActiveLoansCount();
        $actualCount = Loan::where('status', 'active')->count();
        
        expect($dashboardCount)->toBe($actualCount);
        expect($dashboardCount)->toBe($numActive);
        
        // Clean up for next iteration
        Loan::query()->delete();
        BookCopy::query()->delete();
        Book::query()->forceDelete();
        Student::query()->forceDelete();
    }
});

it('returns accurate unpaid fines count - Property 15 extension', function () {
    // Feature: school-library, Property 15: Dashboard Statistics Accuracy (extension)
    // For any set of fines, getUnpaidFinesCount() must equal Fine::where('is_paid', false)->count()
    
    for ($i = 0; $i < 100; $i++) {
        // Create random mix of paid and unpaid fines
        $numUnpaid = fake()->numberBetween(0, 5);
        $numPaid = fake()->numberBetween(0, 5);
        
        for ($j = 0; $j < $numUnpaid; $j++) {
            Fine::factory()->create(['is_paid' => false]);
        }
        
        for ($j = 0; $j < $numPaid; $j++) {
            Fine::factory()->paid()->create();
        }
        
        // Verify dashboard service returns correct count
        $dashboardCount = $this->dashboardService->getUnpaidFinesCount();
        $actualCount = Fine::where('is_paid', false)->count();
        
        expect($dashboardCount)->toBe($actualCount);
        expect($dashboardCount)->toBe($numUnpaid);
        
        // Clean up for next iteration
        Fine::query()->delete();
        Loan::query()->delete();
        BookCopy::query()->delete();
        Book::query()->forceDelete();
        Student::query()->forceDelete();
    }
});

it('returns accurate total unpaid fines amount - Property 15 extension', function () {
    // Feature: school-library, Property 15: Dashboard Statistics Accuracy (extension)
    // For any set of fines, getTotalUnpaidFinesAmount() must equal sum of unpaid fine amounts
    
    for ($i = 0; $i < 100; $i++) {
        $expectedTotal = 0.0;
        
        // Create random unpaid fines with known amounts
        $numUnpaid = fake()->numberBetween(1, 5);
        for ($j = 0; $j < $numUnpaid; $j++) {
            $amount = fake()->randomFloat(2, 1000, 50000);
            Fine::factory()->create([
                'is_paid' => false,
                'amount' => $amount,
            ]);
            $expectedTotal += $amount;
        }
        
        // Create some paid fines (should not be counted)
        $numPaid = fake()->numberBetween(0, 3);
        for ($j = 0; $j < $numPaid; $j++) {
            Fine::factory()->paid()->create([
                'amount' => fake()->randomFloat(2, 1000, 50000),
            ]);
        }
        
        // Verify dashboard service returns correct amount
        $dashboardAmount = $this->dashboardService->getTotalUnpaidFinesAmount();
        $actualAmount = (float) Fine::where('is_paid', false)->sum('amount');
        
        expect($dashboardAmount)->toBe($actualAmount);
        expect(round($dashboardAmount, 2))->toBe(round($expectedTotal, 2));
        
        // Clean up for next iteration
        Fine::query()->delete();
        Loan::query()->delete();
        BookCopy::query()->delete();
        Book::query()->forceDelete();
        Student::query()->forceDelete();
    }
});

it('returns consistent statistics array - Property 15 aggregate', function () {
    // Feature: school-library, Property 15: Dashboard Statistics Accuracy (aggregate)
    // The getStatistics() method must return consistent values with individual methods
    
    for ($i = 0; $i < 100; $i++) {
        // Create random data
        $numBooks = fake()->numberBetween(1, 3);
        $numStudents = fake()->numberBetween(1, 3);
        
        for ($j = 0; $j < $numBooks; $j++) {
            $book = Book::factory()->create();
            BookCopy::factory()->count(fake()->numberBetween(1, 3))->create(['book_id' => $book->id]);
        }
        
        Student::factory()->count($numStudents)->create(['is_active' => true]);
        
        // Create some loans and fines
        Loan::factory()->count(fake()->numberBetween(0, 2))->create(['status' => 'active']);
        Fine::factory()->count(fake()->numberBetween(0, 2))->create(['is_paid' => false]);
        
        // Get statistics array
        $stats = $this->dashboardService->getStatistics();
        
        // Verify all values match individual method calls
        expect($stats['total_books'])->toBe($this->dashboardService->getTotalBooks());
        expect($stats['total_titles'])->toBe($this->dashboardService->getTotalTitles());
        expect($stats['active_students'])->toBe($this->dashboardService->getActiveStudents());
        expect($stats['active_loans'])->toBe($this->dashboardService->getActiveLoansCount());
        expect($stats['unpaid_fines_count'])->toBe($this->dashboardService->getUnpaidFinesCount());
        expect($stats['total_unpaid_fines_amount'])->toBe($this->dashboardService->getTotalUnpaidFinesAmount());
        
        // Clean up for next iteration
        Fine::query()->delete();
        Loan::query()->delete();
        BookCopy::query()->delete();
        Book::query()->forceDelete();
        Student::query()->forceDelete();
    }
});
