<?php

namespace Database\Factories;

use App\Models\FineSetting;
use Illuminate\Database\Eloquent\Factories\Factory;

class FineSettingFactory extends Factory
{
    protected $model = FineSetting::class;

    public function definition(): array
    {
        return [
            'daily_fine' => $this->faker->randomFloat(2, 500, 5000),
            'lost_book_fine' => $this->faker->randomFloat(2, 10000, 100000),
            'lost_fine_type' => $this->faker->randomElement(['flat', 'book_price']),
        ];
    }
}
