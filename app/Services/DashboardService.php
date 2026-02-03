<?php

namespace App\Services;

use App\Models\Book;
use App\Models\BookCopy;
use App\Models\Category;
use App\Models\Fine;
use App\Models\Loan;
use App\Models\SchoolClass;
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
     * Get monthly loan stats for the current year
     */
    public function getMonthlyLoanStats(): array
    {
        // Safe cross-database approach: fetch dates and aggregate in PHP
        // Assuming volume is manageable for a school library
        $loans = Loan::select('loan_date')
            ->whereYear('loan_date', date('Y'))
            ->get();

        $stats = array_fill(1, 12, 0);

        foreach ($loans as $loan) {
            $month = (int) $loan->loan_date->format('m');
            $stats[$month]++;
        }

        return array_values($stats); // 0-indexed array for Chart.js [Jan, Feb, ...]
    }

    /**
     * Get popular categories
     */
    public function getPopularCategories(int $limit = 5): Collection
    {
        return Category::withCount(['books as loans_count' => function ($query) {
            $query->join('book_copies', 'books.id', '=', 'book_copies.book_id')
                  ->join('loans', 'book_copies.id', '=', 'loans.book_copy_id')
                  ->select(DB::raw('count(loans.id)'));
        }])
        ->orderByDesc('loans_count')
        ->limit($limit)
        ->get();
    }

    /**
     * Get top classes
     */
    public function getTopClasses(int $limit = 5): Collection
    {
        return SchoolClass::withCount(['students as loans_count' => function ($query) {
            $query->join('loans', 'students.id', '=', 'loans.student_id')
                  ->select(DB::raw('count(loans.id)'));
        }])
        ->orderByDesc('loans_count')
        ->limit($limit)
        ->get();
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
            'monthly_loans' => $this->getMonthlyLoanStats(),
            'popular_categories' => $this->getPopularCategories(),
            'top_classes' => $this->getTopClasses(),
        ];
    }
}
