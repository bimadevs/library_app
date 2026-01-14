<?php

namespace Database\Factories;

use App\Models\AcademicYear;
use App\Models\Major;
use App\Models\SchoolClass;
use App\Models\Student;
use Illuminate\Database\Eloquent\Factories\Factory;

class StudentFactory extends Factory
{
    protected $model = Student::class;

    public function definition(): array
    {
        return [
            'nis' => $this->faker->unique()->numerify('##########'),
            'name' => $this->faker->name(),
            'birth_place' => $this->faker->city(),
            'birth_date' => $this->faker->dateTimeBetween('-20 years', '-15 years'),
            'address' => $this->faker->address(),
            'class_id' => SchoolClass::factory(),
            'major_id' => Major::factory(),
            'gender' => $this->faker->randomElement(['male', 'female']),
            'academic_year_id' => AcademicYear::factory(),
            'phone' => $this->faker->phoneNumber(),
            'max_loan' => $this->faker->numberBetween(2, 5),
            'is_active' => true,
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }
}
