<?php

namespace App\Http\Controllers;

use App\Domains\Wilayah\Dashboard\UseCases\BuildDashboardDocumentCoverageUseCase;
use App\Services\DashboardActivityChartService;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function __construct(
        private readonly DashboardActivityChartService $dashboardActivityChartService,
        private readonly BuildDashboardDocumentCoverageUseCase $buildDashboardDocumentCoverageUseCase
    ) {
    }

    public function __invoke(): Response|RedirectResponse
    {
        if (auth()->user()?->hasRole('super-admin')) {
            return redirect()->route('super-admin.users.index');
        }

        $activityData = $this->dashboardActivityChartService->buildForUser(auth()->user());
        $documentData = $this->buildDashboardDocumentCoverageUseCase->execute(auth()->user());

        $stats = array_merge(
            $activityData['stats'],
            [
                'activity' => $activityData['stats'],
                'documents' => $documentData['stats'],
            ]
        );

        $charts = array_merge(
            $activityData['charts'],
            [
                'activity' => $activityData['charts'],
                'documents' => $documentData['charts'],
            ]
        );

        return Inertia::render('Dashboard', [
            'dashboardStats' => $stats,
            'dashboardCharts' => $charts,
        ]);
    }
}
