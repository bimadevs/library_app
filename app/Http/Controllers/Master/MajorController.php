<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Major;
use Illuminate\Http\Request;

class MajorController extends Controller
{
    public function index()
    {
        return view('master.majors.index');
    }

    public function create()
    {
        return view('master.majors.form', [
            'major' => new Major(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
        ]);

        Major::create($validated);

        return redirect()
            ->route('master.majors.index')
            ->with('success', 'Jurusan berhasil ditambahkan.');
    }

    public function edit(Major $major)
    {
        return view('master.majors.form', [
            'major' => $major,
        ]);
    }

    public function update(Request $request, Major $major)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
        ]);

        $major->update($validated);

        return redirect()
            ->route('master.majors.index')
            ->with('success', 'Jurusan berhasil diperbarui.');
    }

    public function destroy(Major $major)
    {
        if ($major->students()->exists()) {
            return redirect()
                ->route('master.majors.index')
                ->with('error', 'Jurusan tidak dapat dihapus karena masih memiliki data siswa terkait.');
        }

        $major->delete();

        return redirect()
            ->route('master.majors.index')
            ->with('success', 'Jurusan berhasil dihapus.');
    }
}
