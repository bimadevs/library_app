<?php

namespace App\Http\Controllers;

use App\Models\Fine;
use Illuminate\Http\Request;

class FineController extends Controller
{
    public function markAsPaid(Fine $fine)
    {
        $fine->update([
            'is_paid' => true,
            'paid_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Denda berhasil dibayar.');
    }
}
