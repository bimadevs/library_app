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
    public $selectedBookCopies; // Collection of BookCopy models
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
        $this->selectedBookCopies = collect();

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
                $this->addBookCopy($bookCopy->id);
            }
        }
    }

    protected $rules = [
        'studentId' => 'required|exists:students,id',
        'selectedBookCopies' => 'required|min:1',
        'loanType' => 'required|in:regular,semester,custom',
        'customDueDate' => 'required_if:loanType,custom|nullable|date|after_or_equal:today',
    ];

    protected $messages = [
        'studentId.required' => 'Siswa harus dipilih',
        'studentId.exists' => 'Siswa tidak ditemukan',
        'selectedBookCopies.required' => 'Minimal satu buku harus dipilih',
        'selectedBookCopies.min' => 'Minimal satu buku harus dipilih',
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
            ->withCount(['activeLoans', 'activeLoans as non_textbook_loans_count' => function ($query) {
                $query->whereHas('bookCopy.book', function ($q) {
                    $q->where('is_textbook', false);
                });
            }])
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

            // Check loan limit (initial check without new books)
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

    public function addBookCopy(int $bookCopyId)
    {
        // Check if book is already in cart
        if ($this->selectedBookCopies->contains('id', $bookCopyId)) {
            $this->errorMessage = 'Buku ini sudah ada dalam daftar peminjaman';
            return;
        }

        $bookCopy = BookCopy::with('book')->find($bookCopyId);
        
        if (!$bookCopy) {
            $this->errorMessage = 'Buku tidak ditemukan';
            return;
        }

        if (!$bookCopy->isAvailable()) {
            $this->errorMessage = "Buku dengan barcode {$bookCopy->barcode} tidak tersedia (status: {$bookCopy->status})";
            return;
        }

        // Add to collection
        $this->selectedBookCopies->push($bookCopy);
        
        $this->closeBookModal();
        $this->clearMessages();
        
        // Reset barcode input for next scan
        $this->barcodeInput = '';
    }

    public function removeBookCopy(int $bookCopyId)
    {
        $this->selectedBookCopies = $this->selectedBookCopies->reject(function ($book) use ($bookCopyId) {
            return $book->id === $bookCopyId;
        });
        $this->clearMessages();
    }

    public function clearBookCopies()
    {
        $this->selectedBookCopies = collect();
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
        
        // Check duplication
        if ($this->selectedBookCopies->contains('id', $bookCopy->id)) {
            $this->errorMessage = "Buku dengan barcode {$barcode} sudah ada di daftar";
            $this->barcodeInput = '';
            return;
        }

        $this->addBookCopy($bookCopy->id);
    }

    // Form submission
    public function submit()
    {
        $this->clearMessages();
        $this->validate();

        if ($this->selectedBookCopies->isEmpty()) {
            $this->errorMessage = 'Pilih minimal satu buku untuk dipinjam';
            return;
        }

        try {
            $student = Student::findOrFail($this->studentId);
            
            // Check quota for non-textbook books
            $newNonTextbookCount = $this->selectedBookCopies->filter(fn($copy) => !$copy->book->is_textbook)->count();
            $currentNonTextbookCount = $student->non_textbook_loans_count; // activeLoans count filtered by is_textbook=false
            $maxLoan = $student->max_loan;

            if (($currentNonTextbookCount + $newNonTextbookCount) > $maxLoan) {
                $remaining = max(0, $maxLoan - $currentNonTextbookCount);
                $this->errorMessage = "Kuota peminjaman tidak mencukupi. Sisa kuota: {$remaining}, akan meminjam: {$newNonTextbookCount} buku biasa.";
                return;
            }

            $dueDate = $this->loanType === 'custom' && $this->customDueDate
                ? Carbon::parse($this->customDueDate)
                : null;

            $borrowedTitles = [];

            foreach ($this->selectedBookCopies as $bookCopy) {
                // Re-verify availability
                if (!$bookCopy->fresh()->isAvailable()) {
                    throw new \Exception("Buku '{$bookCopy->book->title}' baru saja dipinjam orang lain.");
                }

                $this->loanService->createLoan(
                    $student,
                    $bookCopy,
                    $this->loanType,
                    $dueDate
                );
                
                $borrowedTitles[] = $bookCopy->book->title;
            }

            $count = count($borrowedTitles);
            $titlesStr = implode(', ', array_slice($borrowedTitles, 0, 2));
            if ($count > 2) $titlesStr .= " dan " . ($count - 2) . " lainnya";

            $this->successMessage = "Peminjaman berhasil! {$count} buku ({$titlesStr}) dipinjam oleh {$student->name}.";

            // Full Reset for new transaction
            $this->resetForm();
            // Preserve success message
            $this->successMessage = "Peminjaman berhasil! {$count} buku ({$titlesStr}) dipinjam oleh {$student->name}.";

        } catch (\InvalidArgumentException $e) {
            $this->errorMessage = $e->getMessage();
        } catch (\Exception $e) {
            $this->errorMessage = 'Terjadi kesalahan: ' . $e->getMessage();
        }
    }

    protected function resetBookSelection()
    {
        $this->selectedBookCopies = collect();
        $this->barcodeInput = '';
        
        // Refresh student data to update loan count
        if ($this->studentId) {
            $this->selectedStudent = Student::with(['class', 'major', 'academicYear'])
                ->withCount(['activeLoans', 'activeLoans as non_textbook_loans_count' => function ($query) {
                    $query->whereHas('bookCopy.book', function ($q) {
                        $q->where('is_textbook', false);
                    });
                }])
                ->find($this->studentId);
            $this->checkStudentWarnings();
        }
    }

    public function resetForm()
    {
        $this->reset([
            'studentId', 'selectedStudent', 'studentSearch',
            'bookSearch', 'barcodeInput',
            'loanType', 'customDueDate',
            'successMessage', 'errorMessage', 'warningMessage'
        ]);
        $this->selectedBookCopies = collect();
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
            ->withCount(['activeLoans', 'activeLoans as non_textbook_loans_count' => function ($query) {
                $query->whereHas('bookCopy.book', function ($q) {
                    $q->where('is_textbook', false);
                });
            }])
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
