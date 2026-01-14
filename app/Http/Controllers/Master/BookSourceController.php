<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\BookSource;
use Illuminate\Http\Request;

class BookSourceController extends Controller
{
    public function index()
    {
        return view('master.book-sources.index');
    }

    public function create()
    {
        return view('master.book-sources.form', [
            'bookSource' => new BookSource(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:book_sources,name',
            'description' => 'nullable|string|max:255',
        ]);

        BookSource::create($validated);

        return redirect()
            ->route('master.book-sources.index')
            ->with('success', 'Sumber buku berhasil ditambahkan.');
    }

    public function edit(BookSource $bookSource)
    {
        return view('master.book-sources.form', [
            'bookSource' => $bookSource,
        ]);
    }

    public function update(Request $request, BookSource $bookSource)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:book_sources,name,' . $bookSource->id,
            'description' => 'nullable|string|max:255',
        ]);

        $bookSource->update($validated);

        return redirect()
            ->route('master.book-sources.index')
            ->with('success', 'Sumber buku berhasil diperbarui.');
    }

    public function destroy(BookSource $bookSource)
    {
        // Check if book source is used by any books
        if ($bookSource->books()->exists()) {
            return redirect()
                ->route('master.book-sources.index')
                ->with('error', 'Sumber buku tidak dapat dihapus karena masih digunakan oleh buku.');
        }

        $bookSource->delete();

        return redirect()
            ->route('master.book-sources.index')
            ->with('success', 'Sumber buku berhasil dihapus.');
    }
}
