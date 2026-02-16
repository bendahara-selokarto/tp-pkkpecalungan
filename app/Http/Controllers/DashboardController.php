<?php

namespace App\Http\Controllers;

use App\Services\DashboardActivityChartService;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function __construct(
        private readonly DashboardActivityChartService $dashboardActivityChartService
    ) {
    }

    public function __invoke(): Response|RedirectResponse
    {
        if (auth()->user()?->hasRole('super-admin')) {
            return redirect()->route('super-admin.users.index');
        }

        $dashboardData = $this->dashboardActivityChartService->buildForUser(auth()->user());

        return Inertia::render('Dashboard', [
            'dashboardStats' => $dashboardData['stats'],
            'dashboardCharts' => $dashboardData['charts'],
        ]);
    }
}
