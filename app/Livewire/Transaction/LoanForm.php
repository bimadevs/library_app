<?php

namespace App\Livewire\Transaction;

use App\Models\BookCopy;
use App\Models\Student;
use App\Services\LoanService;
use Carbon\Carbon;
use Livewire\Component;

class LoanForm extends Component
{
    // Student selection
    public ?int $studentId = null;
    public ?Student $selectedStudent = null;
    public string $studentSearch = '';
    public bool $showStudentModal = false;

    // Book selection
    public ?int $bookCopyId = null;
    public ?BookCopy $selectedBookCopy = null;
    public string $bookSearch = '';
    public bool $showBookModal = false;

    // Loan details
    public string $loanType = 'regular';
    public ?string $customDueDate = null;

    // Barcode scanner input
    public string $barcodeInput = '';

    // Messages
    public string $successMessage = '';
    public string $errorMessage = '';
    public string $warningMessage = '';

    protected LoanService $loanService;

    public function boot(LoanService $loanService)
    {
        $this->loanService = $loanService;
    }

    public function mount()
    {
        if (request()->has('student_nis')) {
            $student = Student::where('nis', request('student_nis'))->first();
            if ($student) {
                $this->selectStudent($student->id);
            }
        }
        
        if (request()->has('book_id')) {
            // Find first available copy for this book
            $bookCopy = BookCopy::where('book_id', request('book_id'))
                ->where('status', 'available')
                ->first();
                
            if ($bookCopy) {
                $this->selectBookCopy($bookCopy->id);
            }
        }
    }

    protected $rules = [
        'studentId' => 'required|exists:students,id',
        'bookCopyId' => 'required|exists:book_copies,id',
        'loanType' => 'required|in:regular,semester,custom',
        'customDueDate' => 'required_if:loanType,custom|nullable|date|after_or_equal:today',
    ];

    protected $messages = [
        'studentId.required' => 'Siswa harus dipilih',
        'studentId.exists' => 'Siswa tidak ditemukan',
        'bookCopyId.required' => 'Buku harus dipilih',
        'bookCopyId.exists' => 'Buku tidak ditemukan',
        'loanType.required' => 'Tipe peminjaman harus dipilih',
        'loanType.in' => 'Tipe peminjaman tidak valid',
        'customDueDate.required_if' => 'Tanggal jatuh tempo harus diisi untuk tipe custom',
        'customDueDate.date' => 'Format tanggal tidak valid',
        'customDueDate.after_or_equal' => 'Tanggal jatuh tempo tidak boleh kurang dari hari ini',
    ];

    // Student selection methods
    public function openStudentModal()
    {
        $this->showStudentModal = true;
        $this->studentSearch = '';
    }

    public function closeStudentModal()
    {
        $this->showStudentModal = false;
        $this->studentSearch = '';
    }

    public function selectStudent(int $studentId)
    {
        $this->studentId = $studentId;
        $this->selectedStudent = Student::with(['class', 'major', 'academicYear'])
            ->withCount('activeLoans')
            ->find($studentId);
        
        $this->closeStudentModal();
        $this->clearMessages();

        // Check for warnings
        $this->checkStudentWarnings();
    }

    public function clearStudent()
    {
        $this->studentId = null;
        $this->selectedStudent = null;
        $this->clearMessages();
    }

    protected function checkStudentWarnings()
    {
        if ($this->selectedStudent) {
            $warnings = [];

            // Check loan limit
            if (!$this->loanService->canStudentBorrow($this->selectedStudent)) {
                $this->errorMessage = "Siswa telah mencapai batas maksimal peminjaman ({$this->selectedStudent->max_loan} buku)";
                return;
            }

            // Check unpaid fines
            if ($this->loanService->hasUnpaidFines($this->selectedStudent)) {
                $amount = number_format($this->loanService->getUnpaidFinesAmount($this->selectedStudent), 0, ',', '.');
                $warnings[] = "Siswa memiliki denda belum lunas sebesar Rp {$amount}";
            }

            if (!empty($warnings)) {
                $this->warningMessage = implode('. ', $warnings);
            }
        }
    }

    // Book selection methods
    public function openBookModal()
    {
        $this->showBookModal = true;
        $this->bookSearch = '';
    }

    public function closeBookModal()
    {
        $this->showBookModal = false;
        $this->bookSearch = '';
    }

    public function selectBookCopy(int $bookCopyId)
    {
        $bookCopy = BookCopy::with('book')->find($bookCopyId);
        
        if (!$bookCopy) {
            $this->errorMessage = 'Buku tidak ditemukan';
            return;
        }

        if (!$bookCopy->isAvailable()) {
            $this->errorMessage = "Buku dengan barcode {$bookCopy->barcode} tidak tersedia (status: {$bookCopy->status})";
            return;
        }

        $this->bookCopyId = $bookCopyId;
        $this->selectedBookCopy = $bookCopy;
        $this->closeBookModal();
        $this->clearMessages();
    }

    public function clearBookCopy()
    {
        $this->bookCopyId = null;
        $this->selectedBookCopy = null;
        $this->clearMessages();
    }

    // Barcode scanner support
    public function scanBarcode()
    {
        if (empty($this->barcodeInput)) {
            return;
        }

        $barcode = trim($this->barcodeInput);
        $bookCopy = BookCopy::with('book')->where('barcode', $barcode)->first();

        if (!$bookCopy) {
            $this->errorMessage = "Buku dengan barcode {$barcode} tidak ditemukan";
            $this->barcodeInput = '';
            return;
        }

        if (!$bookCopy->isAvailable()) {
            $this->errorMessage = "Buku dengan barcode {$barcode} tidak tersedia (status: {$bookCopy->status})";
            $this->barcodeInput = '';
            return;
        }

        $this->bookCopyId = $bookCopy->id;
        $this->selectedBookCopy = $bookCopy;
        $this->barcodeInput = '';
        $this->clearMessages();
    }

    // Form submission
    public function submit()
    {
        $this->clearMessages();
        $this->validate();

        try {
            $student = Student::findOrFail($this->studentId);
            $bookCopy = BookCopy::findOrFail($this->bookCopyId);

            $dueDate = $this->loanType === 'custom' && $this->customDueDate
                ? Carbon::parse($this->customDueDate)
                : null;

            $loan = $this->loanService->createLoan(
                $student,
                $bookCopy,
                $this->loanType,
                $dueDate
            );

            $this->successMessage = "Peminjaman berhasil! Buku '{$bookCopy->book->title}' dipinjam oleh {$student->name}. Jatuh tempo: {$loan->due_date->format('d/m/Y')}";

            // Full Reset for new transaction
            $this->resetForm();
            // But preserve the success message
            $this->successMessage = "Peminjaman berhasil! Buku '{$bookCopy->book->title}' dipinjam oleh {$student->name}. Jatuh tempo: {$loan->due_date->format('d/m/Y')}";

        } catch (\InvalidArgumentException $e) {
            $this->errorMessage = $e->getMessage();
        } catch (\Exception $e) {
            $this->errorMessage = 'Terjadi kesalahan: ' . $e->getMessage();
        }
    }

    protected function resetBookSelection()
    {
        $this->bookCopyId = null;
        $this->selectedBookCopy = null;
        $this->barcodeInput = '';
        
        // Refresh student data to update loan count
        if ($this->studentId) {
            $this->selectedStudent = Student::with(['class', 'major', 'academicYear'])
                ->withCount('activeLoans')
                ->find($this->studentId);
            $this->checkStudentWarnings();
        }
    }

    public function resetForm()
    {
        $this->reset([
            'studentId', 'selectedStudent', 'studentSearch',
            'bookCopyId', 'selectedBookCopy', 'bookSearch', 'barcodeInput',
            'loanType', 'customDueDate',
            'successMessage', 'errorMessage', 'warningMessage'
        ]);
    }

    protected function clearMessages()
    {
        $this->successMessage = '';
        $this->errorMessage = '';
        $this->warningMessage = '';
    }

    public function getStudentsProperty()
    {
        if (empty($this->studentSearch)) {
            return collect();
        }

        return Student::query()
            ->with(['class', 'major'])
            ->where('is_active', true)
            ->where(function ($query) {
                $query->where('nis', 'like', '%' . $this->studentSearch . '%')
                    ->orWhere('name', 'like', '%' . $this->studentSearch . '%');
            })
            ->withCount('activeLoans')
            ->limit(10)
            ->get();
    }

    public function getBookCopiesProperty()
    {
        if (empty($this->bookSearch)) {
            return collect();
        }

        return BookCopy::query()
            ->with('book')
            ->where('status', 'available')
            ->where(function ($query) {
                $query->where('barcode', 'like', '%' . $this->bookSearch . '%')
                    ->orWhereHas('book', function ($q) {
                        $q->where('title', 'like', '%' . $this->bookSearch . '%')
                            ->orWhere('code', 'like', '%' . $this->bookSearch . '%');
                    });
            })
            ->limit(10)
            ->get();
    }

    public function render()
    {
        return view('livewire.transaction.loan-form', [
            'loanTypeOptions' => LoanService::getLoanTypeOptions(),
        ]);
    }
}
