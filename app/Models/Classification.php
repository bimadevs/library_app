<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Classification extends Model
{
    use HasFactory;

    protected $fillable = [
        'ddc_code',
        'name',
    ];

    public function subClassifications(): HasMany
    {
        return $this->hasMany(SubClassification::class);
    }

    public function books(): HasMany
    {
        return $this->hasMany(Book::class);
    }
}
