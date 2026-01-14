<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Classification;
use Illuminate\Http\Request;

class ClassificationController extends Controller
{
    public function index()
    {
        return view('master.classifications.index');
    }

    public function create()
    {
        return view('master.classifications.form', [
            'classification' => new Classification(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'ddc_code' => 'required|string|max:10|unique:classifications,ddc_code',
            'name' => 'required|string|max:255',
        ]);

        Classification::create($validated);

        return redirect()
            ->route('master.classifications.index')
            ->with('success', 'Klasifikasi DDC berhasil ditambahkan.');
    }

    public function edit(Classification $classification)
    {
        return view('master.classifications.form', [
            'classification' => $classification,
        ]);
    }

    public function update(Request $request, Classification $classification)
    {
        $validated = $request->validate([
            'ddc_code' => 'required|string|max:10|unique:classifications,ddc_code,' . $classification->id,
            'name' => 'required|string|max:255',
        ]);

        $classification->update($validated);

        return redirect()
            ->route('master.classifications.index')
            ->with('success', 'Klasifikasi DDC berhasil diperbarui.');
    }

    public function destroy(Classification $classification)
    {
        if ($classification->subClassifications()->exists()) {
            return redirect()
                ->route('master.classifications.index')
                ->with('error', 'Klasifikasi tidak dapat dihapus karena masih memiliki sub klasifikasi terkait.');
        }

        if ($classification->books()->exists()) {
            return redirect()
                ->route('master.classifications.index')
                ->with('error', 'Klasifikasi tidak dapat dihapus karena masih memiliki data buku terkait.');
        }

        $classification->delete();

        return redirect()
            ->route('master.classifications.index')
            ->with('success', 'Klasifikasi DDC berhasil dihapus.');
    }
}
