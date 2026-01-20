<?php

namespace App\Services;

use App\Models\BookCopy;
use App\Models\Loan;
use App\Models\Student;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class LoanService
{
    /**
     * Loan duration presets in days
     */
    public const DURATION_REGULAR = 7;           // 1 week
    public const DURATION_SEMESTER = 180;        // 6 months
    public const DURATION_YEAR = 365;            // 1 year

    /**
     * Loan types
     */
    public const TYPE_REGULAR = 'regular';
    public const TYPE_SEMESTER = 'semester';
    public const TYPE_CUSTOM = 'custom';

    /**
     * Create a new loan
     *
     * @param Student $student
     * @param BookCopy $bookCopy
     * @param string $loanType
     * @param Carbon|null $dueDate Custom due date (required for custom type)
     * @return Loan
     * @throws \InvalidArgumentException
     */
    public function createLoan(
        Student $student,
        BookCopy $bookCopy,
        string $loanType = self::TYPE_REGULAR,
        ?Carbon $dueDate = null
    ): Loan {
        // Validate student loan limit only for non-textbook books
        // Textbooks are exempt from loan limit
        if (!$bookCopy->book->is_textbook) {
            $this->validateLoanLimit($student);
        }

        // Validate unpaid fines
        if ($this->hasUnpaidFines($student)) {
            $amount = number_format($this->getUnpaidFinesAmount($student), 0, ',', '.');
            throw new \InvalidArgumentException(
                "Siswa memiliki tunggakan denda sebesar Rp {$amount}. Harap lunasi denda terlebih dahulu sebelum meminjam buku baru."
            );
        }

        // Validate book copy availability
        $this->validateBookCopyAvailability($bookCopy);

        // Calculate due date based on loan type
        $loanDate = Carbon::today();
        $calculatedDueDate = $this->calculateDueDate($loanType, $dueDate, $loanDate);

        // Validate date ordering
        $this->validateDateOrdering($loanDate, $calculatedDueDate);

        return DB::transaction(function () use ($student, $bookCopy, $loanDate, $calculatedDueDate, $loanType) {
            // Create the loan
            $loan = Loan::create([
                'student_id' => $student->id,
                'book_copy_id' => $bookCopy->id,
                'loan_date' => $loanDate,
                'due_date' => $calculatedDueDate,
                'loan_type' => $loanType,
                'status' => 'active',
            ]);

            // Update book copy status
            $bookCopy->update(['status' => 'borrowed']);

            return $loan;
        });
    }

    /**
     * Get count of active loans excluding textbooks
     *
     * @param Student $student
     * @return int
     */
    public function getNonTextbookActiveLoansCount(Student $student): int
    {
        return $student->activeLoans()
            ->whereHas('bookCopy.book', function ($query) {
                $query->where('is_textbook', false);
            })
            ->count();
    }

    /**
     * Validate that student has not exceeded their loan limit
     *
     * @param Student $student
     * @throws \InvalidArgumentException
     */
    public function validateLoanLimit(Student $student): void
    {
        $activeLoansCount = $this->getNonTextbookActiveLoansCount($student);

        if ($activeLoansCount >= $student->max_loan) {
            throw new \InvalidArgumentException(
                "Siswa telah mencapai batas maksimal peminjaman ({$student->max_loan} buku)"
            );
        }
    }

    /**
     * Validate that book copy is available for loan
     *
     * @param BookCopy $bookCopy
     * @throws \InvalidArgumentException
     */
    public function validateBookCopyAvailability(BookCopy $bookCopy): void
    {
        if (!$bookCopy->isAvailable()) {
            throw new \InvalidArgumentException(
                "Buku dengan barcode {$bookCopy->barcode} tidak tersedia (status: {$bookCopy->status})"
            );
        }
    }

    /**
     * Validate date ordering (loan_date <= due_date)
     *
     * @param Carbon $loanDate
     * @param Carbon $dueDate
     * @throws \InvalidArgumentException
     */
    public function validateDateOrdering(Carbon $loanDate, Carbon $dueDate): void
    {
        if ($loanDate->gt($dueDate)) {
            throw new \InvalidArgumentException(
                "Tanggal pinjam tidak boleh lebih besar dari tanggal jatuh tempo"
            );
        }
    }

    /**
     * Calculate due date based on loan type
     *
     * @param string $loanType
     * @param Carbon|null $customDueDate
     * @param Carbon $loanDate
     * @return Carbon
     * @throws \InvalidArgumentException
     */
    public function calculateDueDate(string $loanType, ?Carbon $customDueDate, Carbon $loanDate): Carbon
    {
        return match ($loanType) {
            self::TYPE_REGULAR => $loanDate->copy()->addDays(self::DURATION_REGULAR),
            self::TYPE_SEMESTER => $loanDate->copy()->addDays(self::DURATION_SEMESTER),
            self::TYPE_CUSTOM => $this->validateCustomDueDate($customDueDate, $loanDate),
            default => throw new \InvalidArgumentException("Tipe peminjaman tidak valid: {$loanType}"),
        };
    }

    /**
     * Validate and return custom due date
     *
     * @param Carbon|null $customDueDate
     * @param Carbon $loanDate
     * @return Carbon
     * @throws \InvalidArgumentException
     */
    protected function validateCustomDueDate(?Carbon $customDueDate, Carbon $loanDate): Carbon
    {
        if ($customDueDate === null) {
            throw new \InvalidArgumentException(
                "Tanggal jatuh tempo harus diisi untuk tipe peminjaman custom"
            );
        }

        if ($customDueDate->lt($loanDate)) {
            throw new \InvalidArgumentException(
                "Tanggal jatuh tempo tidak boleh kurang dari tanggal pinjam"
            );
        }

        return $customDueDate;
    }

    /**
     * Check if student can borrow more books
     *
     * @param Student $student
     * @return bool
     */
    public function canStudentBorrow(Student $student): bool
    {
        return $this->getNonTextbookActiveLoansCount($student) < $student->max_loan;
    }

    /**
     * Get remaining loan slots for a student
     *
     * @param Student $student
     * @return int
     */
    public function getRemainingLoanSlots(Student $student): int
    {
        return max(0, $student->max_loan - $this->getNonTextbookActiveLoansCount($student));
    }

    /**
     * Check if student has unpaid fines
     *
     * @param Student $student
     * @return bool
     */
    public function hasUnpaidFines(Student $student): bool
    {
        return $student->unpaidFines()->exists();
    }

    /**
     * Get total unpaid fines amount for a student
     *
     * @param Student $student
     * @return float
     */
    public function getUnpaidFinesAmount(Student $student): float
    {
        return (float) $student->unpaidFines()->sum('amount');
    }

    /**
     * Get loan type options for dropdown
     *
     * @return array
     */
    public static function getLoanTypeOptions(): array
    {
        return [
            self::TYPE_REGULAR => 'Regular (1 Minggu)',
            self::TYPE_SEMESTER => 'Paket Semester (6 Bulan)',
            self::TYPE_CUSTOM => 'Custom (Tentukan Tanggal)',
        ];
    }
}
