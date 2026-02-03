<?php

namespace App\Services;

use App\Models\Student;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class StudentService
{
    public function createStudent(array $data): Student
    {
        if (isset($data['photo']) && $data['photo'] instanceof UploadedFile) {
            $data['photo'] = $data['photo']->store('students/photos', 'public');
        }

        // Handle boolean is_active, default to false if not present (checkbox behavior)
        // But wait, validation usually passes '1', '0', 'true', 'false'.
        // If it comes from $request->validated(), it preserves the value.
        // I need to cast it to boolean.
        $data['is_active'] = filter_var($data['is_active'] ?? false, FILTER_VALIDATE_BOOLEAN);

        return Student::create($data);
    }

    public function updateStudent(Student $student, array $data): Student
    {
        if (isset($data['photo']) && $data['photo'] instanceof UploadedFile) {
            // Delete old photo if exists
            if ($student->photo && Storage::disk('public')->exists($student->photo)) {
                Storage::disk('public')->delete($student->photo);
            }
            $data['photo'] = $data['photo']->store('students/photos', 'public');
        }

        $data['is_active'] = filter_var($data['is_active'] ?? false, FILTER_VALIDATE_BOOLEAN);

        $student->update($data);
        return $student;
    }

    public function deleteStudent(Student $student): void
    {
        if ($student->photo && Storage::disk('public')->exists($student->photo)) {
            Storage::disk('public')->delete($student->photo);
        }

        $student->delete();
    }

    public function hasActiveLoans(Student $student): bool
    {
        return $student->activeLoans()->exists();
    }

    public function generateImportTemplateCallback(): \Closure
    {
        $columns = ['nis', 'nama', 'tempat_lahir', 'tanggal_lahir', 'alamat', 'kelas', 'jurusan', 'jenis_kelamin', 'tahun_ajaran', 'telepon', 'maks_pinjam'];
        $example = ['1234567890', 'Nama Siswa', 'Jakarta', '2005-01-15', 'Jl. Contoh No. 123', 'X', 'TJKT', 'L', '2024/2025', '081234567890', '3'];

        return function() use ($columns, $example) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF)); // UTF-8 BOM
            fputcsv($file, $columns);
            fputcsv($file, $example);
            fclose($file);
        };
    }
}
