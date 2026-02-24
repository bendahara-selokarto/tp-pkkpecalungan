<?php

namespace App\Http\Controllers;

use App\Domains\Wilayah\Dashboard\UseCases\BuildDashboardDocumentCoverageUseCase;
use App\Domains\Wilayah\Dashboard\UseCases\BuildRoleAwareDashboardBlocksUseCase;
use App\Services\DashboardActivityChartService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function __construct(
        private readonly DashboardActivityChartService $dashboardActivityChartService,
        private readonly BuildDashboardDocumentCoverageUseCase $buildDashboardDocumentCoverageUseCase,
        private readonly BuildRoleAwareDashboardBlocksUseCase $buildRoleAwareDashboardBlocksUseCase
    ) {
    }

    public function __invoke(Request $request): Response|RedirectResponse
    {
        if (auth()->user()?->hasRole('super-admin')) {
            return redirect()->route('super-admin.users.index');
        }

        $section1Month = $this->resolveSection1Month($request->query('section1_month', 'all'));
        $activityData = $this->dashboardActivityChartService->buildForUser(auth()->user(), $section1Month);
        $dashboardContext = [
            'mode' => $request->query('mode', 'all'),
            'level' => $request->query('level', 'all'),
            'sub_level' => $request->query('sub_level', 'all'),
            'section2_group' => $request->query('section2_group', 'all'),
            'section3_group' => $request->query('section3_group', 'all'),
            'section1_month' => $section1Month === null ? 'all' : (string) $section1Month,
            'block' => 'documents',
        ];
        $documentData = $this->buildDashboardDocumentCoverageUseCase->execute(
            auth()->user(),
            $dashboardContext
        );
        $dashboardBlocks = $this->buildRoleAwareDashboardBlocksUseCase->execute(
            auth()->user(),
            $activityData,
            $documentData,
            $dashboardContext
        );

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
            'dashboardBlocks' => $dashboardBlocks,
        ]);
    }

    private function resolveSection1Month(mixed $rawMonth): ?int
    {
        if (! is_scalar($rawMonth)) {
            return null;
        }

        $normalized = strtolower(trim((string) $rawMonth));
        if ($normalized === '' || $normalized === 'all') {
            return null;
        }

        if (! ctype_digit($normalized)) {
            return null;
        }

        $month = (int) $normalized;

        return $month >= 1 && $month <= 12 ? $month : null;
    }
}
