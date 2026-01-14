<?php

/**
 * Feature: school-library, Property 1, 2, 3: Uniqueness Constraints
 * 
 * Property 1: Student NIS Uniqueness - *For any* two students in the system, their NIS values must be different.
 * Property 2: Book Code Uniqueness - *For any* two books in the system, their book codes must be different.
 * Property 3: Barcode Uniqueness - *For any* two book copies in the system, their barcode values must be different.
 * 
 * **Validates: Requirements 8.4, 9.6, 12.3, 13.8, 14.1, 14.2**
 */

use App\Models\Book;
use App\Models\BookCopy;
use App\Models\Student;

it('enforces student NIS uniqueness - Property 1', function () {
    // Feature: school-library, Property 1: Student NIS Uniqueness
    // For any two students in the system, their NIS values must be different.
    // Validates: Requirements 8.4, 9.6
    
    for ($i = 0; $i < 100; $i++) {
        $nis = fake()->unique()->numerify('##########');
        
        // Create first student with this NIS
        $student1 = Student::factory()->create(['nis' => $nis]);
        
        // Attempting to create another student with the same NIS should fail
        expect(fn () => Student::factory()->create(['nis' => $nis]))
            ->toThrow(\Illuminate\Database\QueryException::class);
        
        // Verify only one student exists with this NIS
        expect(Student::where('nis', $nis)->count())->toBe(1);
    }
});

it('enforces book code uniqueness - Property 2', function () {
    // Feature: school-library, Property 2: Book Code Uniqueness
    // For any two books in the system, their book codes must be different.
    // Validates: Requirements 12.3, 13.8
    
    for ($i = 0; $i < 100; $i++) {
        $code = strtoupper(fake()->unique()->bothify('BK-####'));
        
        // Create first book with this code
        $book1 = Book::factory()->create(['code' => $code]);
        
        // Attempting to create another book with the same code should fail
        expect(fn () => Book::factory()->create(['code' => $code]))
            ->toThrow(\Illuminate\Database\QueryException::class);
        
        // Verify only one book exists with this code
        expect(Book::where('code', $code)->count())->toBe(1);
    }
});

it('enforces barcode uniqueness - Property 3', function () {
    // Feature: school-library, Property 3: Barcode Uniqueness
    // For any two book copies in the system, their barcode values must be different.
    // Validates: Requirements 14.1, 14.2
    
    for ($i = 0; $i < 100; $i++) {
        $barcode = fake()->unique()->uuid();
        
        // Create first book copy with this barcode
        $bookCopy1 = BookCopy::factory()->create(['barcode' => $barcode]);
        
        // Attempting to create another book copy with the same barcode should fail
        expect(fn () => BookCopy::factory()->create(['barcode' => $barcode]))
            ->toThrow(\Illuminate\Database\QueryException::class);
        
        // Verify only one book copy exists with this barcode
        expect(BookCopy::where('barcode', $barcode)->count())->toBe(1);
    }
});

it('allows different students with different NIS values', function () {
    // Feature: school-library, Property 1: Student NIS Uniqueness (positive case)
    
    for ($i = 0; $i < 100; $i++) {
        $nis1 = fake()->unique()->numerify('##########');
        $nis2 = fake()->unique()->numerify('##########');
        
        $student1 = Student::factory()->create(['nis' => $nis1]);
        $student2 = Student::factory()->create(['nis' => $nis2]);
        
        expect($student1->nis)->not->toBe($student2->nis);
        expect(Student::where('nis', $nis1)->count())->toBe(1);
        expect(Student::where('nis', $nis2)->count())->toBe(1);
    }
});

it('allows different books with different codes', function () {
    // Feature: school-library, Property 2: Book Code Uniqueness (positive case)
    
    for ($i = 0; $i < 100; $i++) {
        $code1 = strtoupper(fake()->unique()->bothify('BK-####'));
        $code2 = strtoupper(fake()->unique()->bothify('BK-####'));
        
        $book1 = Book::factory()->create(['code' => $code1]);
        $book2 = Book::factory()->create(['code' => $code2]);
        
        expect($book1->code)->not->toBe($book2->code);
        expect(Book::where('code', $code1)->count())->toBe(1);
        expect(Book::where('code', $code2)->count())->toBe(1);
    }
});

it('allows different book copies with different barcodes', function () {
    // Feature: school-library, Property 3: Barcode Uniqueness (positive case)
    
    for ($i = 0; $i < 100; $i++) {
        $barcode1 = fake()->unique()->uuid();
        $barcode2 = fake()->unique()->uuid();
        
        $bookCopy1 = BookCopy::factory()->create(['barcode' => $barcode1]);
        $bookCopy2 = BookCopy::factory()->create(['barcode' => $barcode2]);
        
        expect($bookCopy1->barcode)->not->toBe($bookCopy2->barcode);
        expect(BookCopy::where('barcode', $barcode1)->count())->toBe(1);
        expect(BookCopy::where('barcode', $barcode2)->count())->toBe(1);
    }
});
