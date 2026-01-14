<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FineSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'daily_fine',
        'lost_book_fine',
        'lost_fine_type',
    ];

    protected $casts = [
        'daily_fine' => 'decimal:2',
        'lost_book_fine' => 'decimal:2',
    ];
}
