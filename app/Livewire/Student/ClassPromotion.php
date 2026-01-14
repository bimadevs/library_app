<?php

namespace App\Livewire\Student;

use App\Models\AcademicYear;
use App\Models\SchoolClass;
use App\Models\Student;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class ClassPromotion extends Component
{
    public string $sourceClassId = '';
    public string $targetClassId = '';
    public string $targetAcademicYearId = '';
    public array $selectedStudents = [];
    public bool $selectAll = false;
    public bool $showConfirmation = false;
    public bool $showResults = false;
    public array $promotionResults = [];

    protected $rules = [
        'sourceClassId' => 'required|exists:classes,id',
        'targetClassId' => 'required|exists:classes,id|different:sourceClassId',
        'targetAcademicYearId' => 'required|exists:academic_years,id',
        'selectedStudents' => 'required|array|min:1',
    ];

    protected $messages = [
        'sourceClassId.required' => 'Kelas asal wajib dipilih',
        'targetClassId.required' => 'Kelas tujuan wajib dipilih',
        'targetClassId.different' => 'Kelas tujuan harus berbeda dengan kelas asal',
        'targetAcademicYearId.required' => 'Tahun ajaran tujuan wajib dipilih',
        'selectedStudents.required' => 'Pilih minimal satu siswa untuk dinaikkan',
        'selectedStudents.min' => 'Pilih minimal satu siswa untuk dinaikkan',
    ];

    public function updatedSourceClassId()
    {
        $this->reset(['selectedStudents', 'selectAll', 'showConfirmation', 'showResults']);
    }

    public function updatedSelectAll($value)
    {
        if ($value && $this->sourceClassId) {
            $this->selectedStudents = Student::where('class_id', $this->sourceClassId)
                ->where('is_active', true)
                ->pluck('id')
                ->map(fn($id) => (string) $id)
                ->toArray();
        } else {
            $this->selectedStudents = [];
        }
    }

    public function confirmPromotion()
    {
        $this->validate();
        $this->showConfirmation = true;
    }

    public function cancelPromotion()
    {
        $this->showConfirmation = false;
    }

    public function executePromotion()
    {
        $this->validate();

        $promoted = [];
        $skipped = [];

        DB::beginTransaction();

        try {
            $students = Student::whereIn('id', $this->selectedStudents)->get();
            $targetClass = SchoolClass::find($this->targetClassId);
            $targetAcademicYear = AcademicYear::find($this->targetAcademicYearId);

            foreach ($students as $student) {
                // Check if student is in final year (XII)
                if ($this->isFinalYear($student->class)) {
                    $skipped[] = [
                        'student' => $student,
                        'reason' => 'Siswa sudah di kelas akhir (XII)',
                    ];
                    continue;
                }

                // Update student class and academic year
                $oldClass = $student->class->name ?? '-';
                $student->update([
                    'class_id' => $this->targetClassId,
                    'academic_year_id' => $this->targetAcademicYearId,
                ]);

                $promoted[] = [
                    'student' => $student,
                    'from_class' => $oldClass,
                    'to_class' => $targetClass->name,
                ];
            }

            DB::commit();

            $this->promotionResults = [
                'promoted' => $promoted,
                'skipped' => $skipped,
                'target_class' => $targetClass->name,
                'target_academic_year' => $targetAcademicYear->name,
            ];

            $this->showConfirmation = false;
            $this->showResults = true;

            if (count($promoted) > 0) {
                session()->flash('success', "Berhasil menaikkan " . count($promoted) . " siswa ke kelas {$targetClass->name}.");
            }

        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Gagal melakukan kenaikan kelas: ' . $e->getMessage());
        }
    }

    protected function isFinalYear(?SchoolClass $class): bool
    {
        if (!$class) {
            return false;
        }

        // Check if class name starts with XII (final year in Indonesian high school)
        return str_starts_with(strtoupper($class->name), 'XII');
    }

    public function resetPromotion()
    {
        $this->reset([
            'sourceClassId', 'targetClassId', 'targetAcademicYearId',
            'selectedStudents', 'selectAll', 'showConfirmation', 'showResults', 'promotionResults'
        ]);
    }

    public function render()
    {
        $students = collect();
        if ($this->sourceClassId && $this->sourceClassId !== '') {
            $students = Student::where('class_id', (int) $this->sourceClassId)
                ->where('is_active', true)
                ->with(['major'])
                ->orderBy('name')
                ->get();
        }

        return view('livewire.student.class-promotion', [
            'classes' => SchoolClass::orderBy('name')->get(),
            'academicYears' => AcademicYear::orderBy('name', 'desc')->get(),
            'students' => $students,
            'selectedCount' => count($this->selectedStudents),
        ]);
    }
}
