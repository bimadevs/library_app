<?php

/**
 * Feature: school-library, Property 8: Classification Hierarchy Integrity
 * 
 * *For any* sub-classification, its parent classification_id must reference an existing classification.
 * **Validates: Requirements 6.1**
 */

use App\Models\Classification;
use App\Models\SubClassification;

it('ensures sub-classification always references existing classification', function () {
    // Feature: school-library, Property 8: Classification Hierarchy Integrity
    // For any sub-classification, its parent classification_id must reference an existing classification.
    
    for ($i = 0; $i < 100; $i++) {
        // Create a classification first
        $classification = Classification::factory()->create();
        
        // Create a sub-classification linked to it
        $subClassification = SubClassification::factory()->create([
            'classification_id' => $classification->id,
        ]);
        
        // Verify the relationship exists and is valid
        expect($subClassification->classification)->not->toBeNull();
        expect($subClassification->classification->id)->toBe($classification->id);
        expect($subClassification->classification_id)->toBe($classification->id);
        
        // Verify the parent classification exists in database
        expect(Classification::find($subClassification->classification_id))->not->toBeNull();
    }
});

it('prevents creating sub-classification with non-existent classification', function () {
    // Feature: school-library, Property 8: Classification Hierarchy Integrity
    // Validates that foreign key constraint prevents orphan sub-classifications
    
    $nonExistentId = 99999;
    
    expect(fn () => SubClassification::factory()->create([
        'classification_id' => $nonExistentId,
    ]))->toThrow(\Illuminate\Database\QueryException::class);
});

it('maintains hierarchy integrity when accessing parent from sub-classification', function () {
    // Feature: school-library, Property 8: Classification Hierarchy Integrity
    
    for ($i = 0; $i < 100; $i++) {
        $classification = Classification::factory()->create();
        $subClassifications = SubClassification::factory()->count(rand(1, 5))->create([
            'classification_id' => $classification->id,
        ]);
        
        foreach ($subClassifications as $subClassification) {
            // Each sub-classification must have a valid parent
            expect($subClassification->classification)->toBeInstanceOf(Classification::class);
            expect($subClassification->classification->id)->toBe($classification->id);
        }
        
        // Parent classification should have all sub-classifications
        $classification->refresh();
        expect($classification->subClassifications->count())->toBe($subClassifications->count());
    }
});
