<?php

namespace App\Http\Controllers;

use App\Domains\Wilayah\Dashboard\UseCases\BuildDashboardDocumentCoverageUseCase;
use App\Domains\Wilayah\Dashboard\UseCases\BuildRoleAwareDashboardBlocksUseCase;
use App\Services\DashboardActivityChartService;
use App\Support\Pdf\PdfViewFactory;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

class DashboardController extends Controller
{
    public function __construct(
        private readonly DashboardActivityChartService $dashboardActivityChartService,
        private readonly BuildDashboardDocumentCoverageUseCase $buildDashboardDocumentCoverageUseCase,
        private readonly BuildRoleAwareDashboardBlocksUseCase $buildRoleAwareDashboardBlocksUseCase,
        private readonly PdfViewFactory $pdfViewFactory
    ) {
    }

    public function __invoke(Request $request): Response|RedirectResponse
    {
        if (auth()->user()?->hasRole('super-admin')) {
            return redirect()->route('super-admin.users.index');
        }

        $dashboardPayload = $this->buildDashboardPayload($request);

        return Inertia::render('Dashboard', [
            'dashboardStats' => $dashboardPayload['stats'],
            'dashboardCharts' => $dashboardPayload['charts'],
            'dashboardBlocks' => $dashboardPayload['blocks'],
        ]);
    }

    public function printChartPdf(Request $request): SymfonyResponse|RedirectResponse
    {
        if (auth()->user()?->hasRole('super-admin')) {
            return redirect()->route('super-admin.users.index');
        }

        $dashboardPayload = $this->buildDashboardPayload($request);
        $user = auth()->user()?->loadMissing('area');
        $pdf = $this->pdfViewFactory->loadView('pdf.dashboard_chart_report', [
            'stats' => $dashboardPayload['stats'],
            'charts' => $dashboardPayload['charts'],
            'filters' => $dashboardPayload['context'],
            'printedBy' => $user,
            'printedAt' => now(),
        ]);

        return $pdf->stream('dashboard-chart-report.pdf');
    }

    private function buildDashboardPayload(Request $request): array
    {
        $user = auth()->user();
        $section1Month = $this->resolveSection1Month($request->query('section1_month', 'all'));
        $dashboardContext = [
            'mode' => $request->query('mode', 'all'),
            'level' => $request->query('level', 'all'),
            'sub_level' => $request->query('sub_level', 'all'),
            'section2_group' => $request->query('section2_group', 'all'),
            'section3_group' => $request->query('section3_group', 'all'),
            'section1_month' => $section1Month === null ? 'all' : (string) $section1Month,
            'block' => 'documents',
        ];
        $activityData = $this->dashboardActivityChartService->buildForUser($user, $section1Month);
        $documentData = $this->buildDashboardDocumentCoverageUseCase->execute($user, $dashboardContext);
        $dashboardBlocks = $this->buildRoleAwareDashboardBlocksUseCase->execute(
            $user,
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

        return [
            'stats' => $stats,
            'charts' => $charts,
            'blocks' => $dashboardBlocks,
            'context' => $dashboardContext,
        ];
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
