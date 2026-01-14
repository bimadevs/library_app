<?php

namespace App\Http\Controllers;

use App\Services\DashboardService;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(
        protected DashboardService $dashboardService
    ) {}

    public function index(): View
    {
        $statistics = $this->dashboardService->getStatistics();
        $loansDueToday = $this->dashboardService->getLoansDueToday();
        $unpaidFines = $this->dashboardService->getUnpaidFines();

        return view('dashboard', compact('statistics', 'loansDueToday', 'unpaidFines'));
    }
}
