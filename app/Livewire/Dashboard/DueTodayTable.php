<?php

namespace App\Livewire\Dashboard;

use App\Services\DashboardService;
use Livewire\Component;

class DueTodayTable extends Component
{
    public function render()
    {
        $dashboardService = app(DashboardService::class);
        $loans = $dashboardService->getLoansDueToday();
        $count = $loans->count();

        return view('livewire.dashboard.due-today-table', compact('loans', 'count'));
    }
}
