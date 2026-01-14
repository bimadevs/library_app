<?php

namespace Database\Factories;

use App\Models\Classification;
use App\Models\SubClassification;
use Illuminate\Database\Eloquent\Factories\Factory;

class SubClassificationFactory extends Factory
{
    protected $model = SubClassification::class;

    public function definition(): array
    {
        return [
            'classification_id' => Classification::factory(),
            'sub_ddc_code' => $this->faker->unique()->numerify('### - ###'),
            'name' => $this->faker->words(3, true),
        ];
    }
}
