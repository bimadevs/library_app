<?php

namespace App\Services;

use App\Models\Book;
use App\Models\BookCopy;
use App\Models\Fine;
use App\Models\Loan;
use App\Models\Student;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class DashboardService
{
    /**
     * Get total number of physical book copies
     */
    public function getTotalBooks(): int
    {
        return BookCopy::count();
    }

    /**
     * Get total number of book titles
     */
    public function getTotalTitles(): int
    {
        return Book::count();
    }

    /**
     * Get total number of active students
     */
    public function getActiveStudents(): int
    {
        return Student::where('is_active', true)->count();
    }

    /**
     * Get count of active loans
     */
    public function getActiveLoansCount(): int
    {
        return Loan::where('status', 'active')->count();
    }

    /**
     * Get loans that are due today
     */
    public function getLoansDueToday(): Collection
    {
        return Loan::with(['student', 'bookCopy.book'])
            ->where('status', 'active')
            ->whereDate('due_date', today())
            ->get();
    }

    /**
     * Get count of loans due today
     */
    public function getLoansDueTodayCount(): int
    {
        return Loan::where('status', 'active')
            ->whereDate('due_date', today())
            ->count();
    }

    /**
     * Get unpaid fines with student and loan details
     */
    public function getUnpaidFines(): Collection
    {
        return Fine::with(['student', 'loan.bookCopy.book'])
            ->where('is_paid', false)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Get count of unpaid fines
     */
    public function getUnpaidFinesCount(): int
    {
        return Fine::where('is_paid', false)->count();
    }

    /**
     * Get total amount of unpaid fines
     */
    public function getTotalUnpaidFinesAmount(): float
    {
        return (float) Fine::where('is_paid', false)->sum('amount');
    }

    /**
     * Get all dashboard statistics as an array
     */
    public function getStatistics(): array
    {
        return [
            'total_books' => $this->getTotalBooks(),
            'total_titles' => $this->getTotalTitles(),
            'active_students' => $this->getActiveStudents(),
            'active_loans' => $this->getActiveLoansCount(),
            'loans_due_today_count' => $this->getLoansDueTodayCount(),
            'unpaid_fines_count' => $this->getUnpaidFinesCount(),
            'total_unpaid_fines_amount' => $this->getTotalUnpaidFinesAmount(),
        ];
    }
}
