<?php

namespace Database\Factories;

use App\Models\BookSource;
use Illuminate\Database\Eloquent\Factories\Factory;

class BookSourceFactory extends Factory
{
    protected $model = BookSource::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->randomElement([
                'Pembelian',
                'Hibah',
                'Donasi',
                'Sumbangan',
                'Bantuan Pemerintah',
                'Kerjasama',
                'Hadiah',
            ]) . ' ' . $this->faker->uuid(),
            'description' => $this->faker->optional()->sentence(),
        ];
    }
}
