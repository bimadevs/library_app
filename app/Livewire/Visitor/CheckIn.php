<?php

namespace App\Livewire\Visitor;

use App\Models\Student;
use App\Models\Visitor;
use Livewire\Component;
use Livewire\Attributes\On;

class CheckIn extends Component
{
    public $nis = '';
    public $lastVisitor = null;
    public $recentVisitors = [];
    
    // Search properties
    public $searchQuery = '';
    public $showSearchModal = false;
    public $searchResults = [];

    public function mount()
    {
        $this->loadRecentVisitors();
    }

    public function updatedSearchQuery()
    {
        if (strlen($this->searchQuery) < 2) {
            $this->searchResults = [];
            return;
        }

        $this->searchResults = Student::where('name', 'like', '%' . $this->searchQuery . '%')
            ->orWhere('nis', 'like', '%' . $this->searchQuery . '%')
            ->where('is_active', true)
            ->limit(10)
            ->get();
    }

    public function selectStudent($studentId)
    {
        $student = Student::find($studentId);
        
        if ($student) {
            $this->nis = $student->nis;
            $this->checkIn();
            $this->showSearchModal = false;
            $this->searchQuery = '';
            $this->searchResults = [];
        }
    }

    public function loadRecentVisitors()
    {
        $this->recentVisitors = Visitor::with(['student.class', 'student.major'])
            ->whereDate('date', now())
            ->latest()
            ->take(5)
            ->get();
    }

    public function checkIn()
    {
        $this->validate([
            'nis' => 'required|string|exists:students,nis',
        ]);

        $student = Student::where('nis', $this->nis)->first();

        // Check if already checked in today (optional, but good to prevent double scan spam)
        $alreadyVisited = Visitor::where('student_id', $student->id)
            ->whereDate('date', now())
            ->exists();

        if ($alreadyVisited) {
            $this->addError('nis', 'Siswa ini sudah check-in hari ini.');
            $this->nis = '';
            return;
        }

        $visitor = Visitor::create([
            'student_id' => $student->id,
            'date' => now(),
        ]);

        $this->lastVisitor = $student;
        $this->nis = '';
        $this->loadRecentVisitors();
        
        $this->dispatch('visitor-checked-in');
        $this->dispatch('play-success-sound'); // Optional: Sound effect
    }

    public function render()
    {
        return view('livewire.visitor.check-in')->layout('layouts.kiosk');
    }
}
