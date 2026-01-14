<?php

namespace Database\Factories;

use App\Models\SchoolClass;
use Illuminate\Database\Eloquent\Factories\Factory;

class SchoolClassFactory extends Factory
{
    protected $model = SchoolClass::class;

    public function definition(): array
    {
        $levels = ['X', 'XI', 'XII'];
        $level = $this->faker->randomElement($levels);
        
        return [
            'name' => $level . ' ' . $this->faker->randomElement(['A', 'B', 'C', 'D']),
            'level' => $level,
        ];
    }
}
