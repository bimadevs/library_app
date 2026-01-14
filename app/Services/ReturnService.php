<?php

namespace App\Services;

use App\Models\BookCopy;
use App\Models\Fine;
use App\Models\Loan;
use App\Models\Student;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ReturnService
{
    protected FineCalculatorService $fineCalculator;

    public function __construct(FineCalculatorService $fineCalculator)
    {
        $this->fineCalculator = $fineCalculator;
    }

    /**
     * Process a book return
     *
     * @param Loan $loan
     * @param bool $isLost Whether the book is marked as lost
     * @param Carbon|null $returnDate The actual return date (defaults to today)
     * @return array{loan: Loan, fine: Fine|null}
     */
    public function processReturn(Loan $loan, bool $isLost = false, ?Carbon $returnDate = null): array
    {
        $returnDate = $returnDate ?? Carbon::today();

        return DB::transaction(function () use ($loan, $isLost, $returnDate) {
            // Calculate fine if applicable
            $fineData = $this->fineCalculator->calculateFine($loan, $isLost, $returnDate);
            $fine = null;

            // Update loan status and return date
            $loan->update([
                'return_date' => $returnDate,
                'status' => $isLost ? 'lost' : 'returned',
            ]);

            // Update book copy status
            $bookCopy = $loan->bookCopy;
            $bookCopy->update([
                'status' => $isLost ? 'lost' : 'available',
            ]);

            // Create fine record if there's a fine amount
            if ($fineData['amount'] > 0) {
                $fine = Fine::create([
                    'loan_id' => $loan->id,
                    'student_id' => $loan->student_id,
                    'type' => $fineData['type'],
                    'amount' => $fineData['amount'],
                    'days_overdue' => $fineData['days_overdue'],
                    'is_paid' => false,
                ]);
            }

            return [
                'loan' => $loan->fresh(),
                'fine' => $fine,
            ];
        });
    }

    /**
     * Process multiple book returns at once
     *
     * @param array $returns Array of ['loan_id' => int, 'is_lost' => bool]
     * @param Carbon|null $returnDate The actual return date
     * @return Collection
     */
    public function processMultipleReturns(array $returns, ?Carbon $returnDate = null): Collection
    {
        $results = collect();

        foreach ($returns as $returnData) {
            $loan = Loan::find($returnData['loan_id']);
            
            if ($loan && $loan->status === 'active') {
                $result = $this->processReturn(
                    $loan,
                    $returnData['is_lost'] ?? false,
                    $returnDate
                );
                $results->push($result);
            }
        }

        return $results;
    }

    /**
     * Get all active loans for a student
     *
     * @param Student $student
     * @return Collection
     */
    public function getActiveLoans(Student $student): Collection
    {
        return $student->activeLoans()
            ->with(['bookCopy.book'])
            ->get();
    }

    /**
     * Get active loans with calculated fines preview
     *
     * @param Student $student
     * @param Carbon|null $returnDate The date to calculate fines for
     * @return Collection
     */
    public function getActiveLoansWithFinePreview(Student $student, ?Carbon $returnDate = null): Collection
    {
        $returnDate = $returnDate ?? Carbon::today();
        $loans = $this->getActiveLoans($student);

        return $loans->map(function ($loan) use ($returnDate) {
            $fineData = $this->fineCalculator->calculateFine($loan, false, $returnDate);
            
            return [
                'loan' => $loan,
                'is_overdue' => $this->fineCalculator->isOverdue($loan, $returnDate),
                'days_overdue' => $fineData['days_overdue'],
                'fine_amount' => $fineData['amount'],
            ];
        });
    }

    /**
     * Find a loan by book copy barcode
     *
     * @param string $barcode
     * @return Loan|null
     */
    public function findActiveLoanByBarcode(string $barcode): ?Loan
    {
        $bookCopy = BookCopy::where('barcode', $barcode)->first();

        if (!$bookCopy) {
            return null;
        }

        return $bookCopy->currentLoan;
    }

    /**
     * Check if a loan can be returned
     *
     * @param Loan $loan
     * @return bool
     */
    public function canReturn(Loan $loan): bool
    {
        return $loan->status === 'active';
    }

    /**
     * Get return summary for display
     *
     * @param Loan $loan
     * @param Fine|null $fine
     * @return array
     */
    public function getReturnSummary(Loan $loan, ?Fine $fine): array
    {
        $bookCopy = $loan->bookCopy;
        $book = $bookCopy->book;
        $student = $loan->student;

        return [
            'student_name' => $student->name,
            'student_nis' => $student->nis,
            'book_title' => $book->title,
            'book_code' => $book->code,
            'barcode' => $bookCopy->barcode,
            'loan_date' => $loan->loan_date->format('d/m/Y'),
            'due_date' => $loan->due_date->format('d/m/Y'),
            'return_date' => $loan->return_date->format('d/m/Y'),
            'status' => $loan->status,
            'has_fine' => $fine !== null,
            'fine_amount' => $fine ? $fine->amount : 0,
            'fine_type' => $fine ? $fine->type : null,
            'days_overdue' => $fine ? $fine->days_overdue : 0,
        ];
    }
}
