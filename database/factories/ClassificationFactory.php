<?php

namespace Database\Factories;

use App\Models\Classification;
use Illuminate\Database\Eloquent\Factories\Factory;

class ClassificationFactory extends Factory
{
    protected $model = Classification::class;

    public function definition(): array
    {
        return [
            'ddc_code' => $this->faker->unique()->numerify('###'),
            'name' => $this->faker->words(3, true),
        ];
    }
}
