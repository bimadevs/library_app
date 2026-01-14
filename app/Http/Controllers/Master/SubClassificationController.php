<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Classification;
use App\Models\SubClassification;
use Illuminate\Http\Request;

class SubClassificationController extends Controller
{
    public function index()
    {
        return view('master.sub-classifications.index');
    }

    public function create()
    {
        return view('master.sub-classifications.form', [
            'subClassification' => new SubClassification(),
            'classifications' => Classification::orderBy('ddc_code')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'classification_id' => 'required|exists:classifications,id',
            'sub_ddc_code' => 'required|string|max:20|unique:sub_classifications,sub_ddc_code',
            'name' => 'required|string|max:255',
        ]);

        SubClassification::create($validated);

        return redirect()
            ->route('master.sub-classifications.index')
            ->with('success', 'Sub klasifikasi berhasil ditambahkan.');
    }

    public function edit(SubClassification $subClassification)
    {
        return view('master.sub-classifications.form', [
            'subClassification' => $subClassification,
            'classifications' => Classification::orderBy('ddc_code')->get(),
        ]);
    }

    public function update(Request $request, SubClassification $subClassification)
    {
        $validated = $request->validate([
            'classification_id' => 'required|exists:classifications,id',
            'sub_ddc_code' => 'required|string|max:20|unique:sub_classifications,sub_ddc_code,' . $subClassification->id,
            'name' => 'required|string|max:255',
        ]);

        $subClassification->update($validated);

        return redirect()
            ->route('master.sub-classifications.index')
            ->with('success', 'Sub klasifikasi berhasil diperbarui.');
    }

    public function destroy(SubClassification $subClassification)
    {
        if ($subClassification->books()->exists()) {
            return redirect()
                ->route('master.sub-classifications.index')
                ->with('error', 'Sub klasifikasi tidak dapat dihapus karena masih memiliki data buku terkait.');
        }

        $subClassification->delete();

        return redirect()
            ->route('master.sub-classifications.index')
            ->with('success', 'Sub klasifikasi berhasil dihapus.');
    }

    public function getByClassification(Classification $classification)
    {
        return response()->json(
            $classification->subClassifications()->orderBy('sub_ddc_code')->get()
        );
    }
}
