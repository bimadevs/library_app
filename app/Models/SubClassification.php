<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SubClassification extends Model
{
    use HasFactory;

    protected $fillable = [
        'classification_id',
        'sub_ddc_code',
        'name',
    ];

    public function classification(): BelongsTo
    {
        return $this->belongsTo(Classification::class);
    }

    public function books(): HasMany
    {
        return $this->hasMany(Book::class);
    }
}
