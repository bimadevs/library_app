<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use Illuminate\Http\Request;

class AcademicYearController extends Controller
{
    public function index()
    {
        return view('master.academic-years.index');
    }

    public function create()
    {
        return view('master.academic-years.form', [
            'academicYear' => new AcademicYear(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:20|unique:academic_years,name',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active');

        AcademicYear::create($validated);

        return redirect()
            ->route('master.academic-years.index')
            ->with('success', 'Tahun ajaran berhasil ditambahkan.');
    }

    public function edit(AcademicYear $academicYear)
    {
        return view('master.academic-years.form', [
            'academicYear' => $academicYear,
        ]);
    }

    public function update(Request $request, AcademicYear $academicYear)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:20|unique:academic_years,name,' . $academicYear->id,
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active');

        $academicYear->update($validated);

        return redirect()
            ->route('master.academic-years.index')
            ->with('success', 'Tahun ajaran berhasil diperbarui.');
    }

    public function destroy(AcademicYear $academicYear)
    {
        if ($academicYear->students()->exists()) {
            return redirect()
                ->route('master.academic-years.index')
                ->with('error', 'Tahun ajaran tidak dapat dihapus karena masih memiliki data siswa terkait.');
        }

        $academicYear->delete();

        return redirect()
            ->route('master.academic-years.index')
            ->with('success', 'Tahun ajaran berhasil dihapus.');
    }
}
