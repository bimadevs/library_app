<?php

use App\Models\Student;
use App\Models\Book;
use App\Models\BookCopy;
use App\Models\Master\ClassModel;
use App\Models\Master\Major;
use App\Models\Master\AcademicYear;

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Create prerequisites
$academicYear = AcademicYear::firstOrCreate(['name' => '2025/2026'], ['status' => 'active']);
$class = ClassModel::firstOrCreate(['name' => 'X']);
$major = Major::firstOrCreate(['name' => 'RPL']);

// Create Student
$student = Student::create([
    'nis' => 'TEST' . rand(1000, 9999),
    'name' => 'Test Student ' . rand(100, 999),
    'class_id' => $class->id,
    'major_id' => $major->id,
    'academic_year_id' => $academicYear->id,
    'max_loan' => 3,
    'is_active' => true,
]);

// Create Book and Copy
$book = Book::create([
    'code' => 'B' . rand(1000, 9999),
    'title' => 'Test Book ' . rand(100, 999),
    'author' => 'Test Author',
    'publisher_id' => 1, // Assuming publisher exists or nullable, checking schema later if needed
    'category_id' => 1,
]);

$bookCopy = BookCopy::create([
    'book_id' => $book->id,
    'barcode' => 'BC' . rand(10000, 99999),
    'status' => 'available',
    'condition' => 'good',
    'source' => 'purchase',
]);

echo json_encode([
    'student_name' => $student->name,
    'student_nis' => $student->nis,
    'book_barcode' => $bookCopy->barcode,
    'book_title' => $book->title,
]);
