<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Publisher;
use Illuminate\Http\Request;

class PublisherController extends Controller
{
    public function index()
    {
        return view('master.publishers.index');
    }

    public function create()
    {
        return view('master.publishers.form', [
            'publisher' => new Publisher(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:publishers,name',
        ]);

        Publisher::create($validated);

        return redirect()
            ->route('master.publishers.index')
            ->with('success', 'Penerbit berhasil ditambahkan.');
    }

    public function edit(Publisher $publisher)
    {
        return view('master.publishers.form', [
            'publisher' => $publisher,
        ]);
    }

    public function update(Request $request, Publisher $publisher)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:publishers,name,' . $publisher->id,
        ]);

        $publisher->update($validated);

        return redirect()
            ->route('master.publishers.index')
            ->with('success', 'Penerbit berhasil diperbarui.');
    }

    public function destroy(Publisher $publisher)
    {
        if ($publisher->books()->exists()) {
            return redirect()
                ->route('master.publishers.index')
                ->with('error', 'Penerbit tidak dapat dihapus karena masih memiliki data buku terkait.');
        }

        $publisher->delete();

        return redirect()
            ->route('master.publishers.index')
            ->with('success', 'Penerbit berhasil dihapus.');
    }
}
