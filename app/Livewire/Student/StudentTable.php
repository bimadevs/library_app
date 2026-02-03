<?php

namespace App\Livewire\Student;

use App\Models\AcademicYear;
use App\Models\Major;
use App\Models\SchoolClass;
use App\Models\Student;
use Livewire\Component;
use Livewire\WithPagination;

class StudentTable extends Component
{
    use WithPagination;

    public string $search = '';
    public string $sortField = 'name';
    public string $sortDirection = 'asc';
    public int $perPage = 10;
    public string $filterClass = '';
    public string $filterMajor = '';
    public string $filterAcademicYear = '';
    public string $filterStatus = '';

    protected $queryString = [
        'search' => ['except' => ''],
        'sortField' => ['except' => 'name'],
        'sortDirection' => ['except' => 'asc'],
        'filterClass' => ['except' => ''],
        'filterMajor' => ['except' => ''],
        'filterAcademicYear' => ['except' => ''],
        'filterStatus' => ['except' => ''],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingFilterClass()
    {
        $this->resetPage();
    }

    public function updatingFilterMajor()
    {
        $this->resetPage();
    }

    public function updatingFilterAcademicYear()
    {
        $this->resetPage();
    }

    public function updatingFilterStatus()
    {
        $this->resetPage();
    }

    public function sortBy(string $field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function resetFilters()
    {
        $this->reset(['search', 'filterClass', 'filterMajor', 'filterAcademicYear', 'filterStatus']);
        $this->resetPage();
    }

    public function delete(Student $student)
    {
        // Check if student has related records that prevent deletion
        if ($student->loans()->exists() || $student->fines()->exists()) {
            session()->flash('error', 'Siswa tidak dapat dihapus karena memiliki riwayat peminjaman atau denda.');
            return;
        }

        $student->delete();
        session()->flash('success', 'Siswa berhasil dihapus.');
    }

    public function render()
    {
        $students = Student::query()
            ->with(['class', 'major', 'academicYear'])
            ->withCount('activeLoans')
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('nis', 'like', '%' . $this->search . '%')
                      ->orWhere('name', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->filterClass, function ($query) {
                $query->where('class_id', $this->filterClass);
            })
            ->when($this->filterMajor, function ($query) {
                $query->where('major_id', $this->filterMajor);
            })
            ->when($this->filterAcademicYear, function ($query) {
                $query->where('academic_year_id', $this->filterAcademicYear);
            })
            ->when($this->filterStatus !== '', function ($query) {
                $query->where('is_active', $this->filterStatus === '1');
            })
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);

        return view('livewire.student.student-table', [
            'students' => $students,
            'classes' => SchoolClass::orderBy('name')->get(),
            'majors' => Major::orderBy('name')->get(),
            'academicYears' => AcademicYear::orderBy('name', 'desc')->get(),
        ]);
    }
}
