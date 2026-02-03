<?php

use Illuminate\Contracts\Console\Kernel;

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';

$app->make(Kernel::class)->bootstrap();

$publisher = \App\Models\Publisher::factory()->create();
$classification = \App\Models\Classification::factory()->create();
$subClassification = \App\Models\SubClassification::factory()->create();
$category = \App\Models\Category::factory()->create();
$bookSource = \App\Models\BookSource::factory()->create();
$book = \App\Models\Book::factory()->create([
    'publisher_id' => $publisher->id,
    'classification_id' => $classification->id,
    'sub_classification_id' => $subClassification->id,
    'category_id' => $category->id,
    'book_source_id' => $bookSource->id,
    'title' => 'Test Book For Label',
]);
\App\Models\BookCopy::factory()->count(5)->create(['book_id' => $book->id]);

echo "Book seeded: " . $book->id . "\n";
