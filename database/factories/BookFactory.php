<?php

namespace Database\Factories;

use App\Models\Book;
use App\Models\Category;
use App\Models\Classification;
use App\Models\Publisher;
use App\Models\SubClassification;
use Illuminate\Database\Eloquent\Factories\Factory;

class BookFactory extends Factory
{
    protected $model = Book::class;

    public function definition(): array
    {
        return [
            'code' => strtoupper($this->faker->unique()->bothify('BK-####')),
            'title' => $this->faker->sentence(4),
            'author' => $this->faker->name(),
            'publisher_id' => Publisher::factory(),
            'publish_place' => $this->faker->city(),
            'publish_year' => $this->faker->numberBetween(1990, 2024),
            'isbn' => $this->faker->isbn13(),
            'stock' => $this->faker->numberBetween(1, 10),
            'page_count' => $this->faker->numberBetween(50, 500),
            'thickness' => $this->faker->randomElement(['thin', 'medium', 'thick']),
            'classification_id' => Classification::factory(),
            'sub_classification_id' => null,
            'category_id' => Category::factory(),
            'shelf_location' => $this->faker->bothify('R#-S#'),
            'description' => $this->faker->paragraph(),
            'source' => $this->faker->randomElement(['purchase', 'donation', 'grant']),
            'entry_date' => $this->faker->dateTimeBetween('-2 years', 'now'),
            'price' => $this->faker->randomFloat(2, 10000, 200000),
        ];
    }
}
