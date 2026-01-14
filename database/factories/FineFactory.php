<?php

namespace Database\Factories;

use App\Models\Fine;
use App\Models\Loan;
use App\Models\Student;
use Illuminate\Database\Eloquent\Factories\Factory;

class FineFactory extends Factory
{
    protected $model = Fine::class;

    public function definition(): array
    {
        return [
            'loan_id' => Loan::factory(),
            'student_id' => Student::factory(),
            'type' => $this->faker->randomElement(['late', 'lost']),
            'amount' => $this->faker->randomFloat(2, 1000, 50000),
            'days_overdue' => $this->faker->numberBetween(1, 30),
            'is_paid' => false,
            'paid_at' => null,
        ];
    }

    public function paid(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_paid' => true,
            'paid_at' => now(),
        ]);
    }

    public function late(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'late',
        ]);
    }

    public function lost(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'lost',
        ]);
    }
}
