<?php

use App\Models\Student;
use App\Models\Book;
use App\Models\BookCopy;
use App\Models\SchoolClass;
use App\Models\Major;
use App\Models\AcademicYear;
use App\Models\Publisher;
use App\Models\Category;
use App\Models\Classification;
use App\Models\SubClassification;
use App\Models\BookSource;

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Create prerequisites
$academicYear = AcademicYear::firstOrCreate(['name' => '2025/2026'], ['status' => 'active', 'start_date' => now(), 'end_date' => now()->addYear()]);
$class = SchoolClass::firstOrCreate(['name' => 'X']);
$major = Major::firstOrCreate(['name' => 'RPL']);

$publisher = Publisher::firstOrCreate(['name' => 'Test Publisher']);
$category = Category::firstOrCreate(['name' => 'General']);
$classification = Classification::firstOrCreate(['ddc_code' => '000', 'name' => 'General']);
$sub = SubClassification::firstOrCreate(['classification_id' => $classification->id, 'sub_ddc_code' => '001', 'name' => 'Sub']);
$source = BookSource::firstOrCreate(['name' => 'Purchase']);

// Create Student
$student = Student::create([
    'nis' => 'TEST' . rand(1000, 9999),
    'name' => 'Test Student ' . rand(100, 999),
    'class_id' => $class->id,
    'major_id' => $major->id,
    'academic_year_id' => $academicYear->id,
    'max_loan' => 3,
    'is_active' => true,
    'birth_place' => 'Jakarta',
    'birth_date' => '2005-01-01',
    'gender' => 'male',
    'address' => 'Test Address',
]);

// Create Book and Copy
$book = Book::create([
    'code' => 'B' . rand(1000, 9999),
    'title' => 'Test Book ' . rand(100, 999),
    'author' => 'Test Author',
    'publisher_id' => $publisher->id,
    'category_id' => $category->id,
    'classification_id' => $classification->id,
    'sub_classification_id' => $sub->id,
    'book_source_id' => $source->id,
    'stock' => 1,
    'entry_date' => now(),
    'publish_year' => '2020',
    'publish_place' => 'Jakarta',
    'page_count' => 100,
    'thickness' => '1 cm',
    'shelf_location' => 'A1',
    'source' => 'purchase',
    'price' => 50000,
    'is_textbook' => false,
]);

$bookCopy = BookCopy::create([
    'book_id' => $book->id,
    'barcode' => 'BC' . rand(10000, 99999),
    'status' => 'available',
]);

echo json_encode([
    'student_name' => $student->name,
    'student_nis' => $student->nis,
    'book_barcode' => $bookCopy->barcode,
    'book_title' => $book->title,
]);
