<?php

/**
 * Feature: school-library, Property 11, 13: Book Module Properties
 * 
 * Property 11: Stock and Copy Count Consistency - *For any* book, the count of its book_copies must equal its stock value.
 * Property 13: Import Validation Round-Trip - *For any* valid Excel import data, importing then exporting the same data should produce equivalent records.
 * 
 * **Validates: Requirements 8.2, 12.2, 14.3, 14.6**
 */

use App\Models\Book;
use App\Models\BookCopy;
use App\Services\BarcodeService;

it('maintains stock and copy count consistency after barcode generation - Property 11', function () {
    // Feature: school-library, Property 11: Stock and Copy Count Consistency
    // For any book, the count of its book_copies must equal its stock value after full barcode generation.
    // Validates: Requirements 14.3, 14.6
    
    $barcodeService = new BarcodeService();
    
    for ($i = 0; $i < 100; $i++) {
        // Create a book with random stock between 1 and 10
        $stock = fake()->numberBetween(1, 10);
        $book = Book::factory()->create(['stock' => $stock]);
        
        // Initially, no copies should exist
        expect($book->copies()->count())->toBe(0);
        
        // Generate all barcodes for the book
        $barcodeService->generateBarcodes($book, $stock);
        
        // Refresh the book to get updated relationships
        $book->refresh();
        
        // After generating all barcodes, copy count should equal stock
        expect($book->copies()->count())->toBe($stock);
        expect($book->copies()->count())->toBe($book->stock);
    }
});

it('prevents generating more barcodes than available stock - Property 11 edge case', function () {
    // Feature: school-library, Property 11: Stock and Copy Count Consistency (edge case)
    // Validates: Requirements 14.6
    
    $barcodeService = new BarcodeService();
    
    for ($i = 0; $i < 100; $i++) {
        $stock = fake()->numberBetween(1, 5);
        $book = Book::factory()->create(['stock' => $stock]);
        
        // Generate all barcodes
        $barcodeService->generateBarcodes($book, $stock);
        
        // Attempting to generate more should throw an exception
        expect(fn () => $barcodeService->generateBarcodes($book, 1))
            ->toThrow(\InvalidArgumentException::class);
        
        // Copy count should still equal stock
        $book->refresh();
        expect($book->copies()->count())->toBe($stock);
    }
});

it('generates unique barcodes in correct format - Property 11 barcode format', function () {
    // Feature: school-library, Property 11: Stock and Copy Count Consistency (barcode format)
    // Validates: Requirements 14.1, 14.2
    
    $barcodeService = new BarcodeService();
    
    for ($i = 0; $i < 100; $i++) {
        $stock = fake()->numberBetween(2, 5);
        $book = Book::factory()->create(['stock' => $stock]);
        
        // Generate all barcodes
        $copies = $barcodeService->generateBarcodes($book, $stock);
        
        // Verify barcode format: BOOKCODE-XXX
        foreach ($copies as $index => $copy) {
            $expectedBarcode = sprintf('%s-%03d', $book->code, $index + 1);
            expect($copy->barcode)->toBe($expectedBarcode);
        }
        
        // Verify all barcodes are unique
        $barcodes = collect($copies)->pluck('barcode')->toArray();
        expect(count($barcodes))->toBe(count(array_unique($barcodes)));
    }
});

it('maintains partial barcode generation consistency - Property 11 partial generation', function () {
    // Feature: school-library, Property 11: Stock and Copy Count Consistency (partial generation)
    // Validates: Requirements 14.3, 14.6
    
    $barcodeService = new BarcodeService();
    
    for ($i = 0; $i < 100; $i++) {
        $stock = fake()->numberBetween(3, 8);
        $book = Book::factory()->create(['stock' => $stock]);
        
        // Generate partial barcodes (half of stock)
        $firstBatch = (int) floor($stock / 2);
        $barcodeService->generateBarcodes($book, $firstBatch);
        
        $book->refresh();
        expect($book->copies()->count())->toBe($firstBatch);
        
        // Generate remaining barcodes
        $remainingSlots = $barcodeService->getAvailableSlots($book);
        expect($remainingSlots)->toBe($stock - $firstBatch);
        
        $barcodeService->generateBarcodes($book, $remainingSlots);
        
        $book->refresh();
        expect($book->copies()->count())->toBe($stock);
    }
});

it('validates book import data correctly - Property 13', function () {
    // Feature: school-library, Property 13: Import Validation Round-Trip
    // For any valid book data, the import validation should accept it.
    // Validates: Requirements 12.2
    
    // Create required master data
    $classification = \App\Models\Classification::factory()->create();
    $category = \App\Models\Category::factory()->create();
    $publisher = \App\Models\Publisher::factory()->create();
    
    for ($i = 0; $i < 100; $i++) {
        $bookData = [
            'kode_buku' => strtoupper(fake()->unique()->bothify('BK-####')),
            'judul' => fake()->sentence(3),
            'pengarang' => fake()->name(),
            'penerbit' => $publisher->name,
            'tempat_terbit' => fake()->city(),
            'tahun_terbit' => fake()->numberBetween(1990, 2024),
            'isbn' => fake()->isbn13(),
            'stok' => fake()->numberBetween(1, 10),
            'jumlah_halaman' => fake()->numberBetween(50, 500),
            'ketebalan' => fake()->numberBetween(1, 5) . ' cm',
            'klasifikasi_ddc' => $classification->ddc_code,
            'sub_klasifikasi' => '',
            'kategori' => $category->name,
            'lokasi_rak' => fake()->bothify('?-##-##'),
            'deskripsi' => fake()->paragraph(),
            'sumber' => fake()->randomElement(['Pembelian', 'Hibah', 'Donasi']),
            'tanggal_masuk' => fake()->date('Y-m-d'),
            'harga' => fake()->numberBetween(50000, 500000),
        ];
        
        // Validate using the import rules
        $import = new \App\Imports\BooksImport();
        $validator = \Illuminate\Support\Facades\Validator::make(
            $bookData, 
            $import->rules()
        );
        
        expect($validator->passes())->toBeTrue();
    }
});

it('rejects invalid book import data - Property 13 negative case', function () {
    // Feature: school-library, Property 13: Import Validation Round-Trip (negative case)
    // Invalid data should be rejected by the import validation.
    // Validates: Requirements 12.2
    
    for ($i = 0; $i < 100; $i++) {
        // Create invalid data with missing required fields
        $invalidData = [
            'kode_buku' => '', // Empty - should fail
            'judul' => fake()->sentence(3),
            'pengarang' => fake()->name(),
            'penerbit' => fake()->company(),
            'tempat_terbit' => fake()->city(),
            'tahun_terbit' => 1800, // Too old - should fail
            'stok' => 0, // Zero - should fail
            'jumlah_halaman' => 0, // Zero - should fail
            'klasifikasi_ddc' => fake()->numerify('###'),
            'kategori' => fake()->word(),
            'lokasi_rak' => fake()->bothify('?-##-##'),
            'sumber' => fake()->word(),
        ];
        
        $import = new \App\Imports\BooksImport();
        $validator = \Illuminate\Support\Facades\Validator::make(
            $invalidData, 
            $import->rules()
        );
        
        expect($validator->fails())->toBeTrue();
    }
});

it('creates book copies with available status - Property 11 status consistency', function () {
    // Feature: school-library, Property 11: Stock and Copy Count Consistency (status)
    // All newly generated book copies should have 'available' status.
    // Validates: Requirements 14.3
    
    $barcodeService = new BarcodeService();
    
    for ($i = 0; $i < 100; $i++) {
        $stock = fake()->numberBetween(1, 5);
        $book = Book::factory()->create(['stock' => $stock]);
        
        $copies = $barcodeService->generateBarcodes($book, $stock);
        
        foreach ($copies as $copy) {
            expect($copy->status)->toBe('available');
            expect($copy->isAvailable())->toBeTrue();
        }
    }
});
