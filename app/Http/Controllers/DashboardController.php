<?php

namespace App\Http\Controllers;

use App\Domains\Wilayah\Dashboard\UseCases\BuildDashboardBlockDetailWidgetUseCase;
use App\Domains\Wilayah\Dashboard\UseCases\BuildDashboardDocumentCoverageUseCase;
use App\Domains\Wilayah\Dashboard\UseCases\BuildRoleAwareDashboardBlocksUseCase;
use App\Domains\Wilayah\Services\ActiveBudgetYearContextService;
use App\Services\DashboardActivityChartService;
use App\Support\Pdf\PdfViewFactory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

class DashboardController extends Controller
{
    public function __construct(
        private readonly DashboardActivityChartService $dashboardActivityChartService,
        private readonly BuildDashboardDocumentCoverageUseCase $buildDashboardDocumentCoverageUseCase,
        private readonly BuildRoleAwareDashboardBlocksUseCase $buildRoleAwareDashboardBlocksUseCase,
        private readonly BuildDashboardBlockDetailWidgetUseCase $buildDashboardBlockDetailWidgetUseCase,
        private readonly ActiveBudgetYearContextService $activeBudgetYearContextService,
        private readonly PdfViewFactory $pdfViewFactory
    ) {}

    public function __invoke(Request $request): Response|RedirectResponse
    {
        if (auth()->user()?->hasRole('super-admin')) {
            return redirect()->route('super-admin.users.index');
        }

        return Inertia::render('Dashboard', $this->buildDashboardProps($request));
    }

    public function printChartPdf(Request $request): SymfonyResponse|RedirectResponse
    {
        if (auth()->user()?->hasRole('super-admin')) {
            return redirect()->route('super-admin.users.index');
        }

        $dashboardPayload = $this->buildDashboardChartPayload($request);
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

    public function showBlockDetail(Request $request, string $blockKey): JsonResponse
    {
        abort_if(auth()->user()?->hasRole('super-admin'), 404);

        $payload = $this->buildDashboardBlockDetailWidgetUseCase->execute(
            auth()->user(),
            trim($blockKey)
        );

        abort_if(! is_array($payload), 404);

        return response()->json($payload);
    }

    private function buildDashboardChartPayload(Request $request): array
    {
        $user = auth()->user();
        $dashboardContext = $this->buildDashboardContext($request, $user);
        $section1Month = $this->resolveSection1Month($dashboardContext['section1_month']);
        $activityData = $this->dashboardActivityChartService->buildForUser($user, $section1Month);
        $documentData = $this->buildDashboardDocumentCoverageUseCase->execute($user, $dashboardContext);

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
            'context' => $dashboardContext,
            'activityData' => $activityData,
            'documentData' => $documentData,
        ];
    }

    private function buildDashboardProps(Request $request): array
    {
        $dashboardPayload = null;
        $resolvePayload = function () use ($request, &$dashboardPayload): array {
            return $dashboardPayload ??= $this->buildDashboardChartPayload($request);
        };
        $resolveBlocks = function () use ($resolvePayload): array {
            $payload = $resolvePayload();

            return $this->attachDashboardBlockDetailEndpoints($this->buildRoleAwareDashboardBlocksUseCase->execute(
                auth()->user(),
                $payload['activityData'],
                $payload['documentData'],
                $payload['context']
            ));
        };

        return [
            'dashboardStats' => fn (): array => $resolvePayload()['stats'],
            'dashboardCharts' => fn (): array => $resolvePayload()['charts'],
            'dashboardBlocks' => Inertia::defer(fn (): array => $resolveBlocks(), 'dashboard-blocks'),
            'dashboardContext' => fn (): array => $resolvePayload()['context'],
        ];
    }

    private function buildDashboardContext(Request $request, mixed $user): array
    {
        $section1Month = $this->resolveSection1Month($request->query('section1_month', 'all'));
        $activeBudgetYear = $this->activeBudgetYearContextService->resolveForUser($user);

        return [
            'mode' => $request->query('mode', 'all'),
            'level' => $request->query('level', 'all'),
            'sub_level' => $request->query('sub_level', 'all'),
            'section2_group' => $request->query('section2_group', 'all'),
            'section3_group' => $request->query('section3_group', 'all'),
            'section1_month' => $section1Month === null ? 'all' : (string) $section1Month,
            'tahun_anggaran' => (string) $activeBudgetYear,
            'block' => 'documents',
        ];
    }

    /**
     * @param  array<int, array<string, mixed>>  $blocks
     * @return array<int, array<string, mixed>>
     */
    private function attachDashboardBlockDetailEndpoints(array $blocks): array
    {
        return collect($blocks)
            ->map(function (array $block): array {
                $key = (string) ($block['key'] ?? '');
                if (! $this->supportsDashboardBlockDetailWidget($key)) {
                    return $block;
                }

                $block['detail'] = [
                    'strategy' => 'json',
                    'endpoint' => route('dashboard.blocks.show', ['blockKey' => $key], false),
                ];

                $items = $block['charts']['coverage_per_module']['items'] ?? null;
                if (! is_array($items)) {
                    return $block;
                }

                $block['charts']['coverage_per_module']['items'] = collect($items)
                    ->map(static function (array $item): array {
                        return collect($item)
                            ->except(['per_module'])
                            ->all();
                    })
                    ->values()
                    ->all();

                return $block;
            })
            ->values()
            ->all();
    }

    private function supportsDashboardBlockDetailWidget(string $blockKey): bool
    {
        if ($blockKey === 'documents-pokja-i-desa-breakdown') {
            return true;
        }

        return (bool) preg_match('/^documents\-(pokja\-i|pokja\-ii|pokja\-iii|pokja\-iv)\-kecamatan\-desa\-breakdown$/', $blockKey);
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
