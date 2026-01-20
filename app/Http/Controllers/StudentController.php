<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\Major;
use App\Models\SchoolClass;
use App\Models\Student;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    public function index()
    {
        return view('students.index');
    }

    public function create()
    {
        return view('students.form', [
            'student' => new Student(['max_loan' => 3, 'is_active' => true]),
            'classes' => SchoolClass::orderBy('name')->get(),
            'majors' => Major::orderBy('name')->get(),
            'academicYears' => AcademicYear::orderBy('name', 'desc')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nis' => 'required|string|max:20|unique:students,nis',
            'name' => 'required|string|max:100',
            'birth_place' => 'required|string|max:50',
            'birth_date' => 'required|date',
            'address' => 'required|string|max:255',
            'class_id' => 'required|exists:classes,id',
            'major_id' => 'required|exists:majors,id',
            'gender' => 'required|in:male,female',
            'academic_year_id' => 'required|exists:academic_years,id',
            'phone' => 'required|string|max:20',
            'max_loan' => 'required|integer|min:1|max:10',
            'is_active' => 'boolean',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // 2MB Max
        ]);

        if ($request->hasFile('photo')) {
            $validated['photo'] = $request->file('photo')->store('students/photos', 'public');
        }

        $validated['is_active'] = $request->boolean('is_active');

        Student::create($validated);

        return redirect()
            ->route('students.index')
            ->with('success', 'Data siswa berhasil ditambahkan.');
    }

    public function show(Student $student)
    {
        $student->load(['class', 'major', 'academicYear', 'activeLoans.bookCopy.book', 'unpaidFines']);
        
        return view('students.show', [
            'student' => $student,
        ]);
    }

    public function edit(Student $student)
    {
        return view('students.form', [
            'student' => $student,
            'classes' => SchoolClass::orderBy('name')->get(),
            'majors' => Major::orderBy('name')->get(),
            'academicYears' => AcademicYear::orderBy('name', 'desc')->get(),
        ]);
    }

    public function update(Request $request, Student $student)
    {
        $validated = $request->validate([
            'nis' => 'required|string|max:20|unique:students,nis,' . $student->id,
            'name' => 'required|string|max:100',
            'birth_place' => 'required|string|max:50',
            'birth_date' => 'required|date',
            'address' => 'required|string|max:255',
            'class_id' => 'required|exists:classes,id',
            'major_id' => 'required|exists:majors,id',
            'gender' => 'required|in:male,female',
            'academic_year_id' => 'required|exists:academic_years,id',
            'phone' => 'required|string|max:20',
            'max_loan' => 'required|integer|min:1|max:10',
            'is_active' => 'boolean',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // 2MB Max
        ]);

        if ($request->hasFile('photo')) {
            // Delete old photo if exists
            if ($student->photo && \Illuminate\Support\Facades\Storage::disk('public')->exists($student->photo)) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($student->photo);
            }
            $validated['photo'] = $request->file('photo')->store('students/photos', 'public');
        }

        $validated['is_active'] = $request->boolean('is_active');

        $student->update($validated);

        return redirect()
            ->route('students.index')
            ->with('success', 'Data siswa berhasil diperbarui.');
    }

    public function destroy(Student $student)
    {
        // Check for active loans
        if ($student->activeLoans()->exists()) {
            return redirect()
                ->route('students.index')
                ->with('error', 'Siswa tidak dapat dihapus karena masih memiliki peminjaman aktif.');
        }

        if ($student->photo && \Illuminate\Support\Facades\Storage::disk('public')->exists($student->photo)) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($student->photo);
        }

        $student->delete();

        return redirect()
            ->route('students.index')
            ->with('success', 'Data siswa berhasil dihapus.');
    }

    public function downloadTemplate()
    {
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="template_import_siswa.csv"',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $columns = ['nis', 'nama', 'tempat_lahir', 'tanggal_lahir', 'alamat', 'kelas', 'jurusan', 'jenis_kelamin', 'tahun_ajaran', 'telepon', 'maks_pinjam'];
        $example = ['1234567890', 'Nama Siswa', 'Jakarta', '2005-01-15', 'Jl. Contoh No. 123', 'X', 'TJKT', 'L', '2024/2025', '081234567890', '3'];

        $callback = function() use ($columns, $example) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF)); // UTF-8 BOM
            fputcsv($file, $columns);
            fputcsv($file, $example);
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
