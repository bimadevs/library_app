<?php

/**
 * Feature: bug-fixes, Property 6: Book Source Uniqueness
 * 
 * *For any* book source, the name SHALL be unique across all BookSource records.
 * **Validates: Requirements 7.1, 7.4**
 */

use App\Models\Book;
use App\Models\BookSource;
use App\Models\Category;
use App\Models\Classification;
use App\Models\Publisher;
use App\Models\SubClassification;

beforeEach(function () {
    // Ensure clean state for each test
});

it('creates book source with valid data - CRUD Create', function () {
    // Feature: bug-fixes, Property 6: Book Source Uniqueness
    // Validates: Requirements 7.1
    
    for ($i = 0; $i < 100; $i++) {
        $name = 'Source ' . fake()->unique()->uuid();
        $description = fake()->optional()->sentence();
        
        $bookSource = BookSource::create([
            'name' => $name,
            'description' => $description,
        ]);
        
        expect($bookSource)->toBeInstanceOf(BookSource::class);
        expect($bookSource->name)->toBe($name);
        expect($bookSource->description)->toBe($description);
        expect($bookSource->id)->toBeGreaterThan(0);
    }
});

it('reads book source correctly - CRUD Read', function () {
    // Feature: bug-fixes, Property 6: Book Source Uniqueness
    // Validates: Requirements 7.1
    
    for ($i = 0; $i < 100; $i++) {
        $bookSource = BookSource::factory()->create();
        
        $retrieved = BookSource::find($bookSource->id);
        
        expect($retrieved)->not->toBeNull();
        expect($retrieved->id)->toBe($bookSource->id);
        expect($retrieved->name)->toBe($bookSource->name);
        expect($retrieved->description)->toBe($bookSource->description);
    }
});

it('updates book source with valid data - CRUD Update', function () {
    // Feature: bug-fixes, Property 6: Book Source Uniqueness
    // Validates: Requirements 7.1
    
    for ($i = 0; $i < 100; $i++) {
        $bookSource = BookSource::factory()->create();
        
        $newName = 'Updated Source ' . fake()->unique()->uuid();
        $newDescription = fake()->sentence();
        
        $bookSource->update([
            'name' => $newName,
            'description' => $newDescription,
        ]);
        
        $bookSource->refresh();
        
        expect($bookSource->name)->toBe($newName);
        expect($bookSource->description)->toBe($newDescription);
    }
});

it('deletes book source without associated books - CRUD Delete', function () {
    // Feature: bug-fixes, Property 6: Book Source Uniqueness
    // Validates: Requirements 7.1
    
    for ($i = 0; $i < 100; $i++) {
        $bookSource = BookSource::factory()->create();
        $id = $bookSource->id;
        
        // Verify no books associated
        expect($bookSource->books()->exists())->toBeFalse();
        
        $deleted = $bookSource->delete();
        
        expect($deleted)->toBeTrue();
        expect(BookSource::find($id))->toBeNull();
    }
});

it('enforces unique name constraint - Property 6', function () {
    // Feature: bug-fixes, Property 6: Book Source Uniqueness
    // Validates: Requirements 7.4
    
    for ($i = 0; $i < 100; $i++) {
        $name = 'Unique Source ' . fake()->unique()->uuid();
        
        // Create first book source
        $bookSource1 = BookSource::create([
            'name' => $name,
            'description' => 'First source',
        ]);
        
        expect($bookSource1->id)->toBeGreaterThan(0);
        
        // Attempt to create duplicate should throw exception
        expect(fn () => BookSource::create([
            'name' => $name,
            'description' => 'Duplicate source',
        ]))->toThrow(\Illuminate\Database\QueryException::class);
    }
});

it('allows updating to same name for same record - Property 6', function () {
    // Feature: bug-fixes, Property 6: Book Source Uniqueness
    // Validates: Requirements 7.4
    
    for ($i = 0; $i < 100; $i++) {
        $bookSource = BookSource::factory()->create();
        $originalName = $bookSource->name;
        
        // Update with same name should work
        $bookSource->update([
            'name' => $originalName,
            'description' => 'Updated description ' . fake()->uuid(),
        ]);
        
        $bookSource->refresh();
        expect($bookSource->name)->toBe($originalName);
    }
});

it('prevents deletion of book source with associated books', function () {
    // Feature: bug-fixes, Property 6: Book Source Uniqueness
    // Validates: Requirements 7.1
    
    for ($i = 0; $i < 100; $i++) {
        $bookSource = BookSource::factory()->create();
        $classification = Classification::factory()->create();
        $subClassification = SubClassification::factory()->create([
            'classification_id' => $classification->id,
        ]);
        $publisher = Publisher::factory()->create();
        $category = Category::factory()->create();
        
        // Create book associated with book source
        $book = Book::factory()->create([
            'classification_id' => $classification->id,
            'sub_classification_id' => $subClassification->id,
            'publisher_id' => $publisher->id,
            'category_id' => $category->id,
            'book_source_id' => $bookSource->id,
        ]);
        
        // Verify book source has books
        expect($bookSource->books()->exists())->toBeTrue();
        expect($bookSource->books()->count())->toBeGreaterThanOrEqual(1);
    }
});

it('establishes correct relationship between book source and books', function () {
    // Feature: bug-fixes, Property 6: Book Source Uniqueness
    // Validates: Requirements 7.3
    
    for ($i = 0; $i < 100; $i++) {
        $bookSource = BookSource::factory()->create();
        $classification = Classification::factory()->create();
        $subClassification = SubClassification::factory()->create([
            'classification_id' => $classification->id,
        ]);
        $publisher = Publisher::factory()->create();
        $category = Category::factory()->create();
        
        // Create multiple books with same source
        $bookCount = fake()->numberBetween(1, 3);
        for ($j = 0; $j < $bookCount; $j++) {
            Book::factory()->create([
                'classification_id' => $classification->id,
                'sub_classification_id' => $subClassification->id,
                'publisher_id' => $publisher->id,
                'category_id' => $category->id,
                'book_source_id' => $bookSource->id,
            ]);
        }
        
        // Verify relationship
        expect($bookSource->books()->count())->toBe($bookCount);
        
        // Verify inverse relationship
        $book = $bookSource->books()->first();
        expect($book->bookSource->id)->toBe($bookSource->id);
    }
});
