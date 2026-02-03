<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class LibrarySetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'school_name',
        'school_address',
        'school_logo',
    ];

    public function getLogoUrlAttribute(): ?string
    {
        if ($this->school_logo && Storage::disk('public')->exists($this->school_logo)) {
            return Storage::url($this->school_logo);
        }

        return null;
    }
}
