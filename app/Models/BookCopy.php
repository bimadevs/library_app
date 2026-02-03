<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class BookCopy extends Model
{
    use HasFactory;

    protected $fillable = [
        'book_id',
        'barcode',
        'status',
    ];

    public const STATUS_AVAILABLE = 'available';
    public const STATUS_BORROWED = 'borrowed';
    public const STATUS_LOST = 'lost';
    public const STATUS_DAMAGED = 'damaged';
    public const STATUS_REPAIR = 'repair';

    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class);
    }

    public function loans(): HasMany
    {
        return $this->hasMany(Loan::class);
    }

    public function currentLoan(): HasOne
    {
        return $this->hasOne(Loan::class)->where('status', 'active');
    }

    public function isAvailable(): bool
    {
        return $this->status === self::STATUS_AVAILABLE;
    }

    public function isBorrowed(): bool
    {
        return $this->status === self::STATUS_BORROWED;
    }

    public function isLost(): bool
    {
        return $this->status === self::STATUS_LOST;
    }

    public function isDamaged(): bool
    {
        return $this->status === self::STATUS_DAMAGED;
    }

    public function isUnderRepair(): bool
    {
        return $this->status === self::STATUS_REPAIR;
    }
}
