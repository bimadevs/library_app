<?php

namespace App\Livewire\Dashboard;

use App\Services\DashboardService;
use Livewire\Component;

class UnpaidFinesTable extends Component
{
    public function render()
    {
        $dashboardService = app(DashboardService::class);
        $fines = $dashboardService->getUnpaidFines();
        $count = $fines->count();
        $totalAmount = $dashboardService->getTotalUnpaidFinesAmount();

        return view('livewire.dashboard.unpaid-fines-table', compact('fines', 'count', 'totalAmount'));
    }
}
