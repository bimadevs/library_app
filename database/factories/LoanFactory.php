<?php

namespace Database\Factories;

use App\Models\BookCopy;
use App\Models\Loan;
use App\Models\Student;
use Illuminate\Database\Eloquent\Factories\Factory;

class LoanFactory extends Factory
{
    protected $model = Loan::class;

    public function definition(): array
    {
        $loanDate = $this->faker->dateTimeBetween('-1 month', 'now');
        $dueDate = (clone $loanDate)->modify('+7 days');

        return [
            'student_id' => Student::factory(),
            'book_copy_id' => BookCopy::factory(),
            'loan_date' => $loanDate,
            'due_date' => $dueDate,
            'return_date' => null,
            'loan_type' => 'regular',
            'status' => 'active',
        ];
    }

    public function returned(): static
    {
        return $this->state(function (array $attributes) {
            $returnDate = $this->faker->dateTimeBetween($attributes['loan_date'], 'now');
            return [
                'return_date' => $returnDate,
                'status' => 'returned',
            ];
        });
    }

    public function overdue(): static
    {
        return $this->state(fn (array $attributes) => [
            'loan_date' => now()->subDays(14),
            'due_date' => now()->subDays(7),
            'status' => 'overdue',
        ]);
    }
}
