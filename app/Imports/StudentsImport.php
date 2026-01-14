<?php

namespace App\Imports;

use App\Models\AcademicYear;
use App\Models\Major;
use App\Models\SchoolClass;
use App\Models\Student;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;

class StudentsImport implements ToCollection, WithHeadingRow, SkipsEmptyRows
{
    public array $imported = [];
    public array $skipped = [];
    public array $failed = [];
    
    protected array $classMap = [];
    protected array $majorMap = [];
    protected array $academicYearMap = [];

    public function __construct()
    {
        // Pre-load mappings for performance
        $this->classMap = SchoolClass::pluck('id', 'name')->toArray();
        $this->majorMap = Major::pluck('id', 'name')->toArray();
        $this->academicYearMap = AcademicYear::pluck('id', 'name')->toArray();
    }

    public function collection(Collection $rows)
    {
        foreach ($rows as $index => $row) {
            $rowNumber = $index + 2; // +2 because of header row and 0-based index
            
            // Skip completely empty rows
            if ($this->isEmptyRow($row)) {
                continue;
            }

            // Validate the row
            $validator = Validator::make($row->toArray(), $this->rules(), $this->customValidationMessages());
            
            if ($validator->fails()) {
                $this->failed[] = [
                    'row' => $rowNumber,
                    'data' => $row->toArray(),
                    'errors' => $validator->errors()->all(),
                ];
                continue;
            }

            // Check for duplicate NIS
            $nis = trim($row['nis'] ?? '');
            if (Student::where('nis', $nis)->exists()) {
                $this->skipped[] = [
                    'row' => $rowNumber,
                    'nis' => $nis,
                    'name' => $row['nama'] ?? '',
                    'reason' => 'NIS sudah terdaftar dalam sistem',
                ];
                continue;
            }

            // Check if NIS already imported in this batch
            if (in_array($nis, array_column($this->imported, 'nis'))) {
                $this->skipped[] = [
                    'row' => $rowNumber,
                    'nis' => $nis,
                    'name' => $row['nama'] ?? '',
                    'reason' => 'NIS duplikat dalam file import',
                ];
                continue;
            }

            // Resolve foreign keys
            $classId = $this->classMap[trim($row['kelas'] ?? '')] ?? null;
            $majorId = $this->majorMap[trim($row['jurusan'] ?? '')] ?? null;
            $academicYearId = $this->academicYearMap[trim($row['tahun_ajaran'] ?? '')] ?? null;

            if (!$classId) {
                $this->failed[] = [
                    'row' => $rowNumber,
                    'data' => $row->toArray(),
                    'errors' => ['Kelas "' . ($row['kelas'] ?? '') . '" tidak ditemukan'],
                ];
                continue;
            }

            if (!$majorId) {
                $this->failed[] = [
                    'row' => $rowNumber,
                    'data' => $row->toArray(),
                    'errors' => ['Jurusan "' . ($row['jurusan'] ?? '') . '" tidak ditemukan'],
                ];
                continue;
            }

            if (!$academicYearId) {
                $this->failed[] = [
                    'row' => $rowNumber,
                    'data' => $row->toArray(),
                    'errors' => ['Tahun ajaran "' . ($row['tahun_ajaran'] ?? '') . '" tidak ditemukan'],
                ];
                continue;
            }

            // Create the student
            try {
                $student = Student::create([
                    'nis' => $nis,
                    'name' => trim($row['nama'] ?? ''),
                    'birth_place' => trim($row['tempat_lahir'] ?? ''),
                    'birth_date' => $this->parseDate($row['tanggal_lahir'] ?? ''),
                    'address' => trim($row['alamat'] ?? ''),
                    'class_id' => $classId,
                    'major_id' => $majorId,
                    'gender' => strtoupper(trim($row['jenis_kelamin'] ?? '')),
                    'academic_year_id' => $academicYearId,
                    'phone' => trim($row['telepon'] ?? ''),
                    'max_loan' => (int) ($row['maks_pinjam'] ?? 3),
                    'is_active' => true,
                ]);

                $this->imported[] = [
                    'row' => $rowNumber,
                    'nis' => $student->nis,
                    'name' => $student->name,
                ];
            } catch (\Exception $e) {
                $this->failed[] = [
                    'row' => $rowNumber,
                    'data' => $row->toArray(),
                    'errors' => ['Gagal menyimpan data: ' . $e->getMessage()],
                ];
            }
        }
    }

    protected function isEmptyRow(Collection $row): bool
    {
        return $row->filter(fn($value) => !empty(trim($value ?? '')))->isEmpty();
    }

    protected function parseDate($value): ?string
    {
        if (empty($value)) {
            return null;
        }

        // Handle Excel numeric date format
        if (is_numeric($value)) {
            return \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value)->format('Y-m-d');
        }

        // Try to parse various date formats
        $formats = ['Y-m-d', 'd/m/Y', 'd-m-Y', 'Y/m/d'];
        foreach ($formats as $format) {
            $date = \DateTime::createFromFormat($format, $value);
            if ($date !== false) {
                return $date->format('Y-m-d');
            }
        }

        return $value;
    }

    public function rules(): array
    {
        return [
            'nis' => 'required|string|max:20',
            'nama' => 'required|string|max:100',
            'tempat_lahir' => 'required|string|max:50',
            'tanggal_lahir' => 'required',
            'alamat' => 'required|string|max:255',
            'kelas' => 'required|string',
            'jurusan' => 'required|string',
            'jenis_kelamin' => 'required|in:L,P,l,p',
            'tahun_ajaran' => 'required|string',
            'telepon' => 'required|string|max:20',
        ];
    }

    public function customValidationMessages(): array
    {
        return [
            'nis.required' => 'NIS wajib diisi',
            'nama.required' => 'Nama wajib diisi',
            'tempat_lahir.required' => 'Tempat lahir wajib diisi',
            'tanggal_lahir.required' => 'Tanggal lahir wajib diisi',
            'alamat.required' => 'Alamat wajib diisi',
            'kelas.required' => 'Kelas wajib diisi',
            'jurusan.required' => 'Jurusan wajib diisi',
            'jenis_kelamin.required' => 'Jenis kelamin wajib diisi',
            'jenis_kelamin.in' => 'Jenis kelamin harus L atau P',
            'tahun_ajaran.required' => 'Tahun ajaran wajib diisi',
            'telepon.required' => 'Telepon wajib diisi',
        ];
    }

    public function getSummary(): array
    {
        return [
            'imported' => count($this->imported),
            'skipped' => count($this->skipped),
            'failed' => count($this->failed),
            'total' => count($this->imported) + count($this->skipped) + count($this->failed),
        ];
    }
}
