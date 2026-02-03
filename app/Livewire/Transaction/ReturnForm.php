<?php

namespace App\Livewire\Transaction;

use App\Models\Student;
use App\Models\Loan;
use App\Services\FineCalculatorService;
use App\Services\ReturnService;
use Livewire\Component;

class ReturnForm extends Component
{
    // Student selection
    public ?int $studentId = null;
    public ?Student $selectedStudent = null;
    public string $studentSearch = '';
    public bool $showStudentModal = false;

    // Borrowed books with selection
    public array $borrowedBooks = [];
    public array $selectedLoans = [];
    public array $lostBooks = [];
    public array $paidFines = [];

    // Barcode scanner input
    public string $barcodeInput = '';
    
    // Book Search for identifying borrower
    public string $bookSearch = '';
    public bool $showLoanSelectionModal = false;
    public $foundLoans = [];

    // POS properties
    public $cashAmount = 0;

    // Messages
    public string $successMessage = '';
    public string $errorMessage = '';
    public array $returnResults = [];

    protected ReturnService $returnService;
    protected FineCalculatorService $fineCalculator;

    public function boot(ReturnService $returnService, FineCalculatorService $fineCalculator)
    {
        $this->returnService = $returnService;
        $this->fineCalculator = $fineCalculator;
    }

    // --- Computed Properties ---

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
            ->having('active_loans_count', '>', 0)
            ->limit(10)
            ->get();
    }

    public function getTotalFineProperty()
    {
        $total = 0;
        foreach ($this->borrowedBooks as $book) {
            if (in_array($book['loan_id'], $this->selectedLoans)) {
                if (in_array($book['loan_id'], $this->lostBooks)) {
                    // Calculate lost book fine
                    $loan = \App\Models\Loan::with('bookCopy.book')->find($book['loan_id']);
                    if ($loan) {
                        $total += $this->fineCalculator->calculateLostBookFine($loan->bookCopy->book);
                    }
                } else {
                    $total += $book['fine_amount'];
                }
            }
        }
        return $total;
    }

    public function getTotalPayableProperty()
    {
        $total = 0;
        foreach ($this->borrowedBooks as $book) {
            if (in_array($book['loan_id'], $this->selectedLoans) && in_array($book['loan_id'], $this->paidFines)) {
                if (in_array($book['loan_id'], $this->lostBooks)) {
                    // Calculate lost book fine
                    $loan = \App\Models\Loan::with('bookCopy.book')->find($book['loan_id']);
                    if ($loan) {
                        $total += $this->fineCalculator->calculateLostBookFine($loan->bookCopy->book);
                    }
                } else {
                    $total += $book['fine_amount'];
                }
            }
        }
        return $total;
    }

    public function getChangeAmountProperty()
    {
        $cash = is_numeric($this->cashAmount) ? (float) $this->cashAmount : 0;
        return max(0, $cash - $this->getTotalPayableProperty());
    }

    // --- Student Selection Actions ---

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
            ->find($studentId);
        
        $this->closeStudentModal();
        $this->clearMessages();
        $this->loadBorrowedBooks();
    }

    public function clearStudent()
    {
        $this->studentId = null;
        $this->selectedStudent = null;
        $this->borrowedBooks = [];
        $this->selectedLoans = [];
        $this->lostBooks = [];
        $this->paidFines = [];
        $this->clearMessages();
    }

    // --- Selection & Toggle Actions ---

    public function selectAll()
    {
        $this->selectedLoans = array_column($this->borrowedBooks, 'loan_id');
    }

    public function deselectAll()
    {
        $this->selectedLoans = [];
        $this->lostBooks = [];
        $this->paidFines = [];
    }

    public function toggleLoanSelection(int $loanId)
    {
        if (in_array($loanId, $this->selectedLoans)) {
            $this->selectedLoans = array_values(array_diff($this->selectedLoans, [$loanId]));
        } else {
            $this->selectedLoans[] = $loanId;
        }
    }

    public function toggleLostBook(int $loanId)
    {
        if (in_array($loanId, $this->lostBooks)) {
            $this->lostBooks = array_values(array_diff($this->lostBooks, [$loanId]));
        } else {
            $this->lostBooks[] = $loanId;
        }
    }

    public function togglePaidFine(int $loanId)
    {
        if (in_array($loanId, $this->paidFines)) {
            $this->paidFines = array_values(array_diff($this->paidFines, [$loanId]));
        } else {
            $this->paidFines[] = $loanId;
        }
    }

    // --- Borrower Search Actions ---
    
    public function searchBorrower()
    {
        $this->validate([
            'bookSearch' => 'required|min:3',
        ]);

        $search = $this->bookSearch;

        // Search active loans (not returned) matching barcode or book title
        $loans = Loan::query()
            ->with(['student.class', 'student.major', 'bookCopy.book'])
            ->whereNull('return_date')
            ->whereHas('bookCopy', function ($q) use ($search) {
                $q->where('barcode', $search)
                  ->orWhereHas('book', function ($q2) use ($search) {
                      $q2->where('title', 'like', '%' . $search . '%');
                  });
            })
            ->get();

        if ($loans->isEmpty()) {
            $this->errorMessage = "Tidak ditemukan peminjaman aktif untuk buku dengan kata kunci/barcode: {$search}";
            return;
        }

        if ($loans->count() === 1) {
            // Perfect match
            $loan = $loans->first();
            $this->selectLoanFromSearch($loan->id);
        } else {
            // Multiple matches
            $this->foundLoans = $loans;
            $this->showLoanSelectionModal = true;
        }
        
        $this->bookSearch = '';
    }

    public function closeLoanSelectionModal()
    {
        $this->showLoanSelectionModal = false;
        $this->foundLoans = [];
    }

    public function selectLoanFromSearch($loanId)
    {
        $loan = Loan::with('student')->find($loanId);
        
        if (!$loan) {
            $this->errorMessage = "Data peminjaman tidak ditemukan.";
            return;
        }
        
        // Select the student
        $this->selectStudent($loan->student_id);
        
        // Add specific book to selection
        // We use the boolean 'true' to indicate forcing selection if not present
        if (!in_array($loanId, $this->selectedLoans)) {
            $this->selectedLoans[] = $loanId;
        }
        
        $this->successMessage = "Buku ditemukan! Peminjam: {$loan->student->name}. Buku telah dipilih otomatis.";
        $this->closeLoanSelectionModal();
    }

    // --- Core Logic ---

    public function scanBarcode()
    {
        if (empty($this->barcodeInput)) {
            return;
        }

        $barcode = trim($this->barcodeInput);
        $this->barcodeInput = '';

        $found = false;
        foreach ($this->borrowedBooks as $book) {
            if ($book['barcode'] === $barcode) {
                if (in_array($book['loan_id'], $this->selectedLoans)) {
                    $this->selectedLoans = array_values(array_diff($this->selectedLoans, [$book['loan_id']]));
                } else {
                    $this->selectedLoans[] = $book['loan_id'];
                }
                $found = true;
                break;
            }
        }

        if (!$found) {
            $this->errorMessage = "Buku dengan barcode {$barcode} tidak ditemukan dalam daftar pinjaman siswa ini";
        }
    }

    public function submit()
    {
        $this->clearMessages();
        $this->returnResults = [];

        if (empty($this->selectedLoans)) {
            $this->errorMessage = 'Pilih minimal satu buku untuk dikembalikan';
            return;
        }

        try {
            $returns = [];
            foreach ($this->selectedLoans as $loanId) {
                $returns[] = [
                    'loan_id' => $loanId,
                    'is_lost' => in_array($loanId, $this->lostBooks),
                    'is_paid' => in_array($loanId, $this->paidFines),
                ];
            }

            $results = $this->returnService->processMultipleReturns($returns);

            $totalFines = 0;
            foreach ($results as $result) {
                $summary = $this->returnService->getReturnSummary($result['loan'], $result['fine']);
                $this->returnResults[] = $summary;
                $totalFines += $summary['fine_amount'];
            }

            $returnedCount = count($results);
            $this->successMessage = "Berhasil mengembalikan {$returnedCount} buku.";
            
            if ($totalFines > 0) {
                $formattedFine = number_format($totalFines, 0, ',', '.');
                $paidAmount = 0;
                
                foreach ($results as $result) {
                    if ($result['fine'] && $result['fine']->is_paid) {
                        $paidAmount += $result['fine']->amount;
                    }
                }
                
                $this->successMessage .= " Total denda: Rp {$formattedFine}.";
                if ($paidAmount > 0) {
                    $this->successMessage .= " Dibayar: Rp " . number_format($paidAmount, 0, ',', '.');
                } else {
                    $this->successMessage .= " (Belum dibayar/Hutang)";
                }
            }

            $this->loadBorrowedBooks();

        } catch (\Exception $e) {
            $this->errorMessage = 'Terjadi kesalahan: ' . $e->getMessage();
        }
    }

    public function resetForm()
    {
        $this->reset([
            'studentId', 'selectedStudent', 'studentSearch',
            'borrowedBooks', 'selectedLoans', 'lostBooks', 'paidFines', 'barcodeInput',
            'successMessage', 'errorMessage', 'returnResults', 'cashAmount'
        ]);
    }

    // --- Helpers ---

    protected function loadBorrowedBooks()
    {
        if (!$this->selectedStudent) {
            $this->borrowedBooks = [];
            return;
        }

        $loansWithFines = $this->returnService->getActiveLoansWithFinePreview($this->selectedStudent);
        
        $this->borrowedBooks = $loansWithFines->map(function ($item) {
            $loan = $item['loan'];
            return [
                'loan_id' => $loan->id,
                'barcode' => $loan->bookCopy->barcode,
                'book_title' => $loan->bookCopy->book->title,
                'book_code' => $loan->bookCopy->book->code,
                'loan_date' => $loan->loan_date->format('d/m/Y'),
                'due_date' => $loan->due_date->format('d/m/Y'),
                'is_overdue' => $item['is_overdue'],
                'days_overdue' => $item['days_overdue'],
                'fine_amount' => $item['fine_amount'],
            ];
        })->toArray();

        $this->selectedLoans = [];
        $this->lostBooks = [];
        $this->paidFines = [];
    }

    protected function clearMessages()
    {
        $this->successMessage = '';
        $this->errorMessage = '';
        $this->returnResults = [];
    }

    public function render()
    {
        return view('livewire.transaction.return-form', [
            'totalFine' => $this->getTotalFineProperty(),
        ]);
    }
}
