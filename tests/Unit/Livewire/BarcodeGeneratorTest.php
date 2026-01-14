<?php

/**
 * Feature: livewire-button-fix, Barcode Generator Tests
 * 
 * Tests for the BarcodeGenerator Livewire component functionality.
 * 
 * **Validates: Requirements 2.1, 2.2, 2.3**
 */

use App\Livewire\Book\BarcodeGenerator;
use App\Models\Book;
use App\Models\BookCopy;
use Livewire\Livewire;

/**
 * Task 3.1: Test selectBook method works correctly
 * Verifies that selectBook() method changes the selectedBook state
 * 
 * **Validates: Requirements 2.1**
 */
it('selectBook method updates selectedBook state correctly', function () {
    // Create a book with some stock
    $book = Book::factory()->create(['stock' => 5]);
    
    // Test the Livewire component
    $component = Livewire::test(BarcodeGenerator::class)
        ->assertSet('selectedBookId', null)
        ->assertSet('selectedBook', null)
        ->call('selectBook', $book->id)
        ->assertSet('selectedBookId', $book->id)
        ->assertSet('quantity', 1)
        ->assertSet('generatedBarcodes', [])
        ->assertSet('selectedForPrint', []);
    
    // Verify selectedBook is not null
    expect($component->get('selectedBook'))->not->toBeNull();
    expect($component->get('selectedBook')->id)->toBe($book->id);
});

it('selectBook method loads book with copies relationship', function () {
    // Create a book with existing copies
    $book = Book::factory()->create(['stock' => 5]);
    BookCopy::factory()->count(2)->create(['book_id' => $book->id]);
    
    $component = Livewire::test(BarcodeGenerator::class)
        ->call('selectBook', $book->id);
    
    // Verify the book is loaded with copies
    $selectedBook = $component->get('selectedBook');
    expect($selectedBook)->not->toBeNull();
    expect($selectedBook->id)->toBe($book->id);
    expect($selectedBook->copies)->toHaveCount(2);
});

it('selectBook method resets state when selecting a different book', function () {
    // Create two books
    $book1 = Book::factory()->create(['stock' => 5]);
    $book2 = Book::factory()->create(['stock' => 3]);
    
    $component = Livewire::test(BarcodeGenerator::class)
        ->call('selectBook', $book1->id)
        ->assertSet('selectedBookId', $book1->id);
    
    // Select a different book
    $component->call('selectBook', $book2->id)
        ->assertSet('selectedBookId', $book2->id)
        ->assertSet('quantity', 1)
        ->assertSet('generatedBarcodes', [])
        ->assertSet('selectedForPrint', []);
});


/**
 * Task 3.2: Property Test for Toggle Selection Idempotence
 * 
 * Feature: livewire-button-fix, Property 1: Barcode Selection Toggle Idempotence
 * *For any* barcode and initial selection state, toggling the selection twice SHALL return to the original state.
 * 
 * **Validates: Requirements 2.3**
 */
it('togglePrintSelection is idempotent - toggling twice returns to original state - Property 1', function () {
    // Feature: livewire-button-fix, Property 1: Barcode Selection Toggle Idempotence
    // For any barcode and initial selection state, toggling the selection twice SHALL return to the original state.
    // Validates: Requirements 2.3
    
    for ($i = 0; $i < 100; $i++) {
        // Create a book with copies
        $stock = fake()->numberBetween(2, 5);
        $book = Book::factory()->create(['stock' => $stock]);
        
        // Generate barcodes for the book
        $barcodeService = new \App\Services\BarcodeService();
        $copies = $barcodeService->generateBarcodes($book, $stock);
        
        // Pick a random barcode to test
        $randomCopy = fake()->randomElement($copies);
        $barcode = $randomCopy->barcode;
        
        // Test with initially unselected state
        $component = Livewire::test(BarcodeGenerator::class)
            ->call('selectBook', $book->id);
        
        // Get initial state (empty selection)
        $initialState = $component->get('selectedForPrint');
        expect($initialState)->toBe([]);
        
        // Toggle once - should add to selection
        $component->call('togglePrintSelection', $barcode);
        $afterFirstToggle = $component->get('selectedForPrint');
        expect($afterFirstToggle)->toContain($barcode);
        
        // Toggle twice - should return to original state (empty)
        $component->call('togglePrintSelection', $barcode);
        $afterSecondToggle = $component->get('selectedForPrint');
        expect($afterSecondToggle)->not->toContain($barcode);
        expect(count($afterSecondToggle))->toBe(count($initialState));
    }
});

it('togglePrintSelection is idempotent when starting with selected state - Property 1', function () {
    // Feature: livewire-button-fix, Property 1: Barcode Selection Toggle Idempotence
    // For any barcode that is already selected, toggling twice SHALL return to selected state.
    // Validates: Requirements 2.3
    
    for ($i = 0; $i < 100; $i++) {
        // Create a book with copies
        $stock = fake()->numberBetween(2, 5);
        $book = Book::factory()->create(['stock' => $stock]);
        
        // Generate barcodes for the book
        $barcodeService = new \App\Services\BarcodeService();
        $copies = $barcodeService->generateBarcodes($book, $stock);
        
        // Pick a random barcode to test
        $randomCopy = fake()->randomElement($copies);
        $barcode = $randomCopy->barcode;
        
        // Start with the barcode already selected
        $component = Livewire::test(BarcodeGenerator::class)
            ->call('selectBook', $book->id)
            ->call('togglePrintSelection', $barcode);
        
        // Get initial state (barcode is selected)
        $initialState = $component->get('selectedForPrint');
        expect($initialState)->toContain($barcode);
        
        // Toggle once - should remove from selection
        $component->call('togglePrintSelection', $barcode);
        $afterFirstToggle = $component->get('selectedForPrint');
        expect($afterFirstToggle)->not->toContain($barcode);
        
        // Toggle twice - should return to original state (selected)
        $component->call('togglePrintSelection', $barcode);
        $afterSecondToggle = $component->get('selectedForPrint');
        expect($afterSecondToggle)->toContain($barcode);
    }
});


/**
 * Task 3.3: Property Test for Barcode Generation Count Correctness
 * 
 * Feature: livewire-button-fix, Property 2: Barcode Generation Count Correctness
 * *For any* book with available slots and valid quantity n, generating barcodes SHALL create exactly n new book copies.
 * 
 * **Validates: Requirements 2.2**
 */
it('generateBarcodes creates exactly n new book copies - Property 2', function () {
    // Feature: livewire-button-fix, Property 2: Barcode Generation Count Correctness
    // For any book with available slots and valid quantity n, generating barcodes SHALL create exactly n new book copies.
    // Validates: Requirements 2.2
    
    for ($i = 0; $i < 100; $i++) {
        // Create a book with random stock
        $stock = fake()->numberBetween(2, 10);
        $book = Book::factory()->create(['stock' => $stock]);
        
        // Get initial copy count
        $initialCopyCount = $book->copies()->count();
        expect($initialCopyCount)->toBe(0);
        
        // Generate a random valid quantity (between 1 and available slots)
        $quantity = fake()->numberBetween(1, $stock);
        
        // Test the Livewire component
        $component = Livewire::test(BarcodeGenerator::class)
            ->call('selectBook', $book->id)
            ->set('quantity', $quantity)
            ->call('generateBarcodes');
        
        // Refresh the book to get updated copy count
        $book->refresh();
        
        // Verify exactly n new copies were created
        $newCopyCount = $book->copies()->count();
        expect($newCopyCount)->toBe($quantity);
        expect($newCopyCount - $initialCopyCount)->toBe($quantity);
        
        // Verify the generated barcodes array has the correct count
        $generatedBarcodes = $component->get('generatedBarcodes');
        expect(count($generatedBarcodes))->toBe($quantity);
    }
});

it('generateBarcodes respects available slots constraint - Property 2 edge case', function () {
    // Feature: livewire-button-fix, Property 2: Barcode Generation Count Correctness (edge case)
    // For any book, generating barcodes should not exceed available slots.
    // Validates: Requirements 2.2
    
    for ($i = 0; $i < 100; $i++) {
        // Create a book with random stock
        $stock = fake()->numberBetween(2, 5);
        $book = Book::factory()->create(['stock' => $stock]);
        
        // Pre-generate some copies to reduce available slots
        $existingCopies = fake()->numberBetween(0, $stock - 1);
        if ($existingCopies > 0) {
            $barcodeService = new \App\Services\BarcodeService();
            $barcodeService->generateBarcodes($book, $existingCopies);
        }
        
        $book->refresh();
        $availableSlots = $stock - $book->copies()->count();
        
        // Only test if there are available slots
        if ($availableSlots > 0) {
            $quantity = fake()->numberBetween(1, $availableSlots);
            $initialCopyCount = $book->copies()->count();
            
            // Test the Livewire component
            $component = Livewire::test(BarcodeGenerator::class)
                ->call('selectBook', $book->id)
                ->set('quantity', $quantity)
                ->call('generateBarcodes');
            
            // Refresh and verify
            $book->refresh();
            $newCopyCount = $book->copies()->count();
            
            // Verify exactly n new copies were created
            expect($newCopyCount - $initialCopyCount)->toBe($quantity);
            
            // Verify total copies don't exceed stock
            expect($newCopyCount)->toBeLessThanOrEqual($stock);
        }
    }
});
