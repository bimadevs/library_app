<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreStudentRequest;
use App\Http\Requests\UpdateStudentRequest;
use App\Models\AcademicYear;
use App\Models\Major;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Services\StudentService;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    protected StudentService $studentService;

    public function __construct(StudentService $studentService)
    {
        $this->studentService = $studentService;
    }

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

    public function store(StoreStudentRequest $request)
    {
        // We need to merge the boolean logic here because FormRequest validation doesn't transform data
        // Checkboxes that are unchecked are not sent.
        // But StoreStudentRequest validation rules includes 'is_active' => 'boolean'.
        // If not present, it's ignored by validated().
        // So we pass validated() to service, and service handles missing key as false.

        // However, if we want to be explicit about it being from the request:
        $data = $request->validated();
        // If checkbox is checked, it sends '1' or 'on'. Validation allows it.
        // If unchecked, nothing sent.
        // But for update, we need to know if it was unchecked.

        // Actually, for store/update, we can just merge the boolean value from request helper
        // which handles all cases correctly.
        $data['is_active'] = $request->boolean('is_active');

        $this->studentService->createStudent($data);

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

    public function update(UpdateStudentRequest $request, Student $student)
    {
        $data = $request->validated();
        $data['is_active'] = $request->boolean('is_active');

        $this->studentService->updateStudent($student, $data);

        return redirect()
            ->route('students.index')
            ->with('success', 'Data siswa berhasil diperbarui.');
    }

    public function destroy(Student $student)
    {
        if ($this->studentService->hasActiveLoans($student)) {
            return redirect()
                ->route('students.index')
                ->with('error', 'Siswa tidak dapat dihapus karena masih memiliki peminjaman aktif.');
        }

        $this->studentService->deleteStudent($student);

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

        return response()->stream(
            $this->studentService->generateImportTemplateCallback(),
            200,
            $headers
        );
    }
}
