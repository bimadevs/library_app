<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\SchoolClass;
use Illuminate\Http\Request;

class ClassController extends Controller
{
    public function index()
    {
        return view('master.classes.index');
    }

    public function create()
    {
        return view('master.classes.form', [
            'class' => new SchoolClass(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:50|unique:classes,name',
        ]);

        SchoolClass::create($validated);

        return redirect()
            ->route('master.classes.index')
            ->with('success', 'Kelas berhasil ditambahkan.');
    }

    public function edit(SchoolClass $class)
    {
        return view('master.classes.form', [
            'class' => $class,
        ]);
    }

    public function update(Request $request, SchoolClass $class)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:50|unique:classes,name,' . $class->id,
        ]);

        $class->update($validated);

        return redirect()
            ->route('master.classes.index')
            ->with('success', 'Kelas berhasil diperbarui.');
    }

    public function destroy(SchoolClass $class)
    {
        if ($class->students()->exists()) {
            return redirect()
                ->route('master.classes.index')
                ->with('error', 'Kelas tidak dapat dihapus karena masih memiliki data siswa terkait.');
        }

        $class->delete();

        return redirect()
            ->route('master.classes.index')
            ->with('success', 'Kelas berhasil dihapus.');
    }
}
