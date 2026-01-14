<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Book extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'code',
        'title',
        'author',
        'publisher_id',
        'publish_place',
        'publish_year',
        'isbn',
        'stock',
        'page_count',
        'thickness',
        'classification_id',
        'sub_classification_id',
        'category_id',
        'shelf_location',
        'description',
        'source',
        'book_source_id',
        'entry_date',
        'price',
    ];

    protected $casts = [
        'entry_date' => 'date',
        'stock' => 'integer',
        'price' => 'decimal:2',
    ];

    public function publisher(): BelongsTo
    {
        return $this->belongsTo(Publisher::class);
    }

    public function classification(): BelongsTo
    {
        return $this->belongsTo(Classification::class);
    }

    public function subClassification(): BelongsTo
    {
        return $this->belongsTo(SubClassification::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function bookSource(): BelongsTo
    {
        return $this->belongsTo(BookSource::class);
    }

    public function copies(): HasMany
    {
        return $this->hasMany(BookCopy::class);
    }

    public function availableCopies(): HasMany
    {
        return $this->hasMany(BookCopy::class)->where('status', 'available');
    }
}
