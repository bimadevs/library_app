<?php

namespace App\Services;

use App\Models\Book;
use App\Models\FineSetting;
use App\Models\Loan;
use Carbon\Carbon;

class FineCalculatorService
{
    /**
     * Fine types
     */
    public const TYPE_LATE = 'late';
    public const TYPE_LOST = 'lost';

    /**
     * Lost fine calculation types
     */
    public const LOST_FINE_FLAT = 'flat';
    public const LOST_FINE_BOOK_PRICE = 'book_price';

    /**
     * Get current fine settings
     *
     * @return FineSetting
     */
    public function getSettings(): FineSetting
    {
        return FineSetting::first() ?? new FineSetting([
            'daily_fine' => 0,
            'lost_book_fine' => 0,
            'lost_fine_type' => self::LOST_FINE_FLAT,
        ]);
    }

    /**
     * Calculate late fine for a loan
     *
     * @param Loan $loan
     * @param Carbon|null $returnDate The actual return date (defaults to today)
     * @return array{amount: float, days_overdue: int}
     */
    public function calculateLateFine(Loan $loan, ?Carbon $returnDate = null): array
    {
        $returnDate = $returnDate ?? Carbon::today();
        $dueDate = $loan->due_date;

        // If returned on or before due date, no fine
        if ($returnDate->lte($dueDate)) {
            return [
                'amount' => 0.0,
                'days_overdue' => 0,
            ];
        }

        $settings = $this->getSettings();
        // Use absolute value to ensure positive days
        $daysOverdue = (int) abs($returnDate->diffInDays($dueDate));
        $amount = $daysOverdue * (float) $settings->daily_fine;

        return [
            'amount' => $amount,
            'days_overdue' => $daysOverdue,
        ];
    }

    /**
     * Calculate lost book fine
     *
     * @param Book $book
     * @return float
     */
    public function calculateLostBookFine(Book $book): float
    {
        $settings = $this->getSettings();

        if ($settings->lost_fine_type === self::LOST_FINE_BOOK_PRICE) {
            // Use book price if available, otherwise use flat fine
            return $book->price ? (float) $book->price : (float) $settings->lost_book_fine;
        }

        // Flat fine
        return (float) $settings->lost_book_fine;
    }

    /**
     * Calculate fine for a loan based on return status
     *
     * @param Loan $loan
     * @param bool $isLost Whether the book is marked as lost
     * @param Carbon|null $returnDate The actual return date
     * @return array{type: string, amount: float, days_overdue: int}
     */
    public function calculateFine(Loan $loan, bool $isLost = false, ?Carbon $returnDate = null): array
    {
        if ($isLost) {
            $book = $loan->bookCopy->book;
            return [
                'type' => self::TYPE_LOST,
                'amount' => $this->calculateLostBookFine($book),
                'days_overdue' => 0,
            ];
        }

        $lateFine = $this->calculateLateFine($loan, $returnDate);
        
        return [
            'type' => self::TYPE_LATE,
            'amount' => $lateFine['amount'],
            'days_overdue' => $lateFine['days_overdue'],
        ];
    }

    /**
     * Check if a loan is overdue
     *
     * @param Loan $loan
     * @param Carbon|null $checkDate The date to check against (defaults to today)
     * @return bool
     */
    public function isOverdue(Loan $loan, ?Carbon $checkDate = null): bool
    {
        $checkDate = $checkDate ?? Carbon::today();
        return $checkDate->gt($loan->due_date);
    }

    /**
     * Get days overdue for a loan
     *
     * @param Loan $loan
     * @param Carbon|null $checkDate The date to check against (defaults to today)
     * @return int
     */
    public function getDaysOverdue(Loan $loan, ?Carbon $checkDate = null): int
    {
        $checkDate = $checkDate ?? Carbon::today();
        
        if (!$this->isOverdue($loan, $checkDate)) {
            return 0;
        }

        return (int) abs($checkDate->diffInDays($loan->due_date));
    }
}
