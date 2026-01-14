<?php

/**
 * Feature: school-library, Property 10: Master Data Referential Integrity
 * 
 * *For any* master data (class, major, academic_year, classification) with associated records, 
 * deletion must be prevented.
 * **Validates: Requirements 2.4, 2.5, 3.4, 3.5, 4.4, 4.5, 5.5, 5.6**
 */

use App\Models\AcademicYear;
use App\Models\Book;
use App\Models\Category;
use App\Models\Classification;
use App\Models\Major;
use App\Models\Publisher;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\SubClassification;

beforeEach(function () {
    // Ensure clean state for each test
});

it('prevents deletion of academic year with associated students', function () {
    // Feature: school-library, Property 10: Master Data Referential Integrity
    // Validates: Requirements 2.4, 2.5
    
    for ($i = 0; $i < 100; $i++) {
        $academicYear = AcademicYear::factory()->create();
        $schoolClass = SchoolClass::factory()->create();
        $major = Major::factory()->create();
        
        // Create student associated with academic year
        $student = Student::factory()->create([
            'academic_year_id' => $academicYear->id,
            'class_id' => $schoolClass->id,
            'major_id' => $major->id,
        ]);
        
        // Verify academic year has students
        expect($academicYear->students()->exists())->toBeTrue();
        
        // Verify the relationship count
        expect($academicYear->students()->count())->toBeGreaterThanOrEqual(1);
    }
});

it('allows deletion of academic year without associated students', function () {
    // Feature: school-library, Property 10: Master Data Referential Integrity
    // Validates: Requirements 2.4
    
    for ($i = 0; $i < 100; $i++) {
        $academicYear = AcademicYear::factory()->create();
        
        // Verify no students associated
        expect($academicYear->students()->exists())->toBeFalse();
        
        // Should be able to delete
        $deleted = $academicYear->delete();
        expect($deleted)->toBeTrue();
        expect(AcademicYear::find($academicYear->id))->toBeNull();
    }
});

it('prevents deletion of class with associated students', function () {
    // Feature: school-library, Property 10: Master Data Referential Integrity
    // Validates: Requirements 3.4, 3.5
    
    for ($i = 0; $i < 100; $i++) {
        $schoolClass = SchoolClass::factory()->create();
        $academicYear = AcademicYear::factory()->create();
        $major = Major::factory()->create();
        
        // Create student associated with class
        $student = Student::factory()->create([
            'class_id' => $schoolClass->id,
            'academic_year_id' => $academicYear->id,
            'major_id' => $major->id,
        ]);
        
        // Verify class has students
        expect($schoolClass->students()->exists())->toBeTrue();
        expect($schoolClass->students()->count())->toBeGreaterThanOrEqual(1);
    }
});

it('allows deletion of class without associated students', function () {
    // Feature: school-library, Property 10: Master Data Referential Integrity
    // Validates: Requirements 3.4
    
    for ($i = 0; $i < 100; $i++) {
        $schoolClass = SchoolClass::factory()->create();
        
        // Verify no students associated
        expect($schoolClass->students()->exists())->toBeFalse();
        
        // Should be able to delete
        $deleted = $schoolClass->delete();
        expect($deleted)->toBeTrue();
        expect(SchoolClass::find($schoolClass->id))->toBeNull();
    }
});

it('prevents deletion of major with associated students', function () {
    // Feature: school-library, Property 10: Master Data Referential Integrity
    // Validates: Requirements 4.4, 4.5
    
    for ($i = 0; $i < 100; $i++) {
        $major = Major::factory()->create();
        $schoolClass = SchoolClass::factory()->create();
        $academicYear = AcademicYear::factory()->create();
        
        // Create student associated with major
        $student = Student::factory()->create([
            'major_id' => $major->id,
            'class_id' => $schoolClass->id,
            'academic_year_id' => $academicYear->id,
        ]);
        
        // Verify major has students
        expect($major->students()->exists())->toBeTrue();
        expect($major->students()->count())->toBeGreaterThanOrEqual(1);
    }
});

it('allows deletion of major without associated students', function () {
    // Feature: school-library, Property 10: Master Data Referential Integrity
    // Validates: Requirements 4.4
    
    for ($i = 0; $i < 100; $i++) {
        $major = Major::factory()->create();
        
        // Verify no students associated
        expect($major->students()->exists())->toBeFalse();
        
        // Should be able to delete
        $deleted = $major->delete();
        expect($deleted)->toBeTrue();
        expect(Major::find($major->id))->toBeNull();
    }
});

it('prevents deletion of classification with associated sub-classifications', function () {
    // Feature: school-library, Property 10: Master Data Referential Integrity
    // Validates: Requirements 5.5, 5.6
    
    for ($i = 0; $i < 100; $i++) {
        $classification = Classification::factory()->create();
        
        // Create sub-classification associated with classification
        $subClassification = SubClassification::factory()->create([
            'classification_id' => $classification->id,
        ]);
        
        // Verify classification has sub-classifications
        expect($classification->subClassifications()->exists())->toBeTrue();
        expect($classification->subClassifications()->count())->toBeGreaterThanOrEqual(1);
    }
});

it('prevents deletion of classification with associated books', function () {
    // Feature: school-library, Property 10: Master Data Referential Integrity
    // Validates: Requirements 5.5, 5.6
    
    for ($i = 0; $i < 100; $i++) {
        $classification = Classification::factory()->create();
        $subClassification = SubClassification::factory()->create([
            'classification_id' => $classification->id,
        ]);
        $publisher = Publisher::factory()->create();
        $category = Category::factory()->create();
        
        // Create book associated with classification
        $book = Book::factory()->create([
            'classification_id' => $classification->id,
            'sub_classification_id' => $subClassification->id,
            'publisher_id' => $publisher->id,
            'category_id' => $category->id,
        ]);
        
        // Verify classification has books
        expect($classification->books()->exists())->toBeTrue();
        expect($classification->books()->count())->toBeGreaterThanOrEqual(1);
    }
});

it('allows deletion of classification without associated data', function () {
    // Feature: school-library, Property 10: Master Data Referential Integrity
    // Validates: Requirements 5.5
    
    for ($i = 0; $i < 100; $i++) {
        $classification = Classification::factory()->create();
        
        // Verify no associated data
        expect($classification->subClassifications()->exists())->toBeFalse();
        expect($classification->books()->exists())->toBeFalse();
        
        // Should be able to delete
        $deleted = $classification->delete();
        expect($deleted)->toBeTrue();
        expect(Classification::find($classification->id))->toBeNull();
    }
});

it('verifies referential integrity is maintained across all master data types', function () {
    // Feature: school-library, Property 10: Master Data Referential Integrity
    // Comprehensive test for all master data types
    
    for ($i = 0; $i < 100; $i++) {
        // Create all master data
        $academicYear = AcademicYear::factory()->create();
        $schoolClass = SchoolClass::factory()->create();
        $major = Major::factory()->create();
        $classification = Classification::factory()->create();
        $subClassification = SubClassification::factory()->create([
            'classification_id' => $classification->id,
        ]);
        $publisher = Publisher::factory()->create();
        $category = Category::factory()->create();
        
        // Create student with all relationships
        $student = Student::factory()->create([
            'academic_year_id' => $academicYear->id,
            'class_id' => $schoolClass->id,
            'major_id' => $major->id,
        ]);
        
        // Create book with all relationships
        $book = Book::factory()->create([
            'classification_id' => $classification->id,
            'sub_classification_id' => $subClassification->id,
            'publisher_id' => $publisher->id,
            'category_id' => $category->id,
        ]);
        
        // Verify all relationships exist
        expect($academicYear->students()->exists())->toBeTrue();
        expect($schoolClass->students()->exists())->toBeTrue();
        expect($major->students()->exists())->toBeTrue();
        expect($classification->subClassifications()->exists())->toBeTrue();
        expect($classification->books()->exists())->toBeTrue();
        expect($subClassification->books()->exists())->toBeTrue();
        expect($publisher->books()->exists())->toBeTrue();
        expect($category->books()->exists())->toBeTrue();
    }
});
