<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        return view('master.categories.index');
    }

    public function create()
    {
        return view('master.categories.form', [
            'category' => new Category(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:categories,name',
        ]);

        Category::create($validated);

        return redirect()
            ->route('master.categories.index')
            ->with('success', 'Kategori berhasil ditambahkan.');
    }

    public function edit(Category $category)
    {
        return view('master.categories.form', [
            'category' => $category,
        ]);
    }

    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:categories,name,' . $category->id,
        ]);

        $category->update($validated);

        return redirect()
            ->route('master.categories.index')
            ->with('success', 'Kategori berhasil diperbarui.');
    }

    public function destroy(Category $category)
    {
        if ($category->books()->exists()) {
            return redirect()
                ->route('master.categories.index')
                ->with('error', 'Kategori tidak dapat dihapus karena masih memiliki data buku terkait.');
        }

        $category->delete();

        return redirect()
            ->route('master.categories.index')
            ->with('success', 'Kategori berhasil dihapus.');
    }
}
