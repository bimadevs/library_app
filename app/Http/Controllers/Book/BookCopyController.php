<?php

namespace App\Http\Controllers\Book;

use App\Http\Controllers\Controller;
use App\Models\BookCopy;
use Illuminate\Http\Request;

class BookCopyController extends Controller
{
    public function update(Request $request, BookCopy $bookCopy)
    {
        $validated = $request->validate([
            'status' => 'required|in:available,lost,damaged,repair',
        ]);

        if ($bookCopy->status === 'borrowed') {
            return back()->with('error', 'Buku sedang dipinjam. Harap proses melalui menu pengembalian.');
        }

        $bookCopy->update(['status' => $validated['status']]);

        return back()->with('success', 'Status salinan buku berhasil diperbarui.');
    }
}
