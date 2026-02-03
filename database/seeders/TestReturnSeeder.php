<?php

namespace Database\Seeders;

use App\Models\Book;
use App\Models\BookCopy;
use App\Models\Loan;
use App\Models\Student;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class TestReturnSeeder extends Seeder
{
    public function run(): void
    {
        User::firstOrCreate([
            'email' => 'test@example.com',
        ], [
            'name' => 'Test User',
            'password' => Hash::make('password'),
        ]);

        $student = Student::factory()->create([
            'name' => 'Budi Santoso',
            'nis' => '12345',
        ]);

        $book1 = Book::factory()->create(['title' => 'Belajar Laravel']);
        $copy1 = BookCopy::factory()->create(['book_id' => $book1->id, 'barcode' => 'B001']);
        
        $book2 = Book::factory()->create(['title' => 'Belajar VueJS']);
        $copy2 = BookCopy::factory()->create(['book_id' => $book2->id, 'barcode' => 'B002']);

        Loan::create([
            'student_id' => $student->id,
            'book_copy_id' => $copy1->id,
            'loan_date' => now()->subDays(3),
            'due_date' => now()->addDays(4),
            'status' => 'active',
        ]);

        Loan::create([
            'student_id' => $student->id,
            'book_copy_id' => $copy2->id,
            'loan_date' => now()->subDays(5),
            'due_date' => now()->addDays(2),
            'status' => 'active',
        ]);
    }
}
