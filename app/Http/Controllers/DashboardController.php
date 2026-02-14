<?php

namespace App\Http\Controllers;

use App\Services\DashboardActivityChartService;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(
        private readonly DashboardActivityChartService $dashboardActivityChartService
    ) {
    }

    public function __invoke(): View
    {
        $dashboardData = $this->dashboardActivityChartService->buildForUser(auth()->user());

        return view('dashboard', [
            'dashboardStats' => $dashboardData['stats'],
            'dashboardCharts' => $dashboardData['charts'],
        ]);
    }
}
