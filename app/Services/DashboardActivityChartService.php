<?php

namespace App\Services;

use App\Domains\Wilayah\Activities\Repositories\ActivityRepositoryInterface;
use App\Domains\Wilayah\Dashboard\Repositories\DashboardDocumentCoverageRepositoryInterface;
use App\Domains\Wilayah\Enums\ScopeLevel;
use App\Domains\Wilayah\Repositories\AreaRepositoryInterface;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

class DashboardActivityChartService
{
    public function __construct(
        private readonly ActivityRepositoryInterface $activityRepository,
        private readonly AreaRepositoryInterface $areaRepository,
        private readonly DashboardDocumentCoverageRepositoryInterface $dashboardDocumentCoverageRepository
    ) {
    }

    public function buildForUser(User $user): array
    {
        $baseQuery = $this->buildScopedQuery($user);

        $monthly = $this->buildMonthlyChart((clone $baseQuery));
        $status = $this->buildStatusChart((clone $baseQuery));
        $level = $this->buildLevelChart((clone $baseQuery));
        $byDesa = $this->buildByDesaChart($user, (clone $baseQuery));

        return [
            'stats' => [
                'total' => (clone $baseQuery)->count(),
                'this_month' => (clone $baseQuery)
                    ->whereBetween('activity_date', [now()->startOfMonth()->toDateString(), now()->endOfMonth()->toDateString()])
                    ->count(),
                'published' => $status['published'],
                'draft' => $status['draft'],
            ],
            'charts' => [
                'monthly' => [
                    'labels' => $monthly['labels'],
                    'values' => $monthly['values'],
                ],
                'status' => [
                    'labels' => ['Draft', 'Published'],
                    'values' => [$status['draft'], $status['published']],
                ],
                'level' => [
                    'labels' => ['Desa', 'Kecamatan'],
                    'values' => [$level['desa'], $level['kecamatan']],
                ],
                'by_desa' => [
                    'labels' => $byDesa['labels'],
                    'values' => $byDesa['values'],
                    'books_total' => $byDesa['books_total'],
                    'books_filled' => $byDesa['books_filled'],
                ],
            ],
        ];
    }

    private function buildScopedQuery(User $user): Builder
    {
        return $this->activityRepository->queryScopedByUser($user);
    }

    private function buildMonthlyChart(Builder $query): array
    {
        $startMonth = Carbon::now()->startOfMonth()->subMonths(5);
        $labels = [];
        $keys = [];

        for ($i = 0; $i < 6; $i++) {
            $month = $startMonth->copy()->addMonths($i);
            $labels[] = $month->translatedFormat('M Y');
            $keys[] = $month->format('Y-m');
        }

        $activities = $query
            ->whereDate('activity_date', '>=', $startMonth->toDateString())
            ->get(['activity_date']);

        $rawMonthly = [];
        foreach ($activities as $activity) {
            $key = Carbon::parse($activity->activity_date)->format('Y-m');
            $rawMonthly[$key] = ($rawMonthly[$key] ?? 0) + 1;
        }

        $values = array_map(
            static fn (string $key): int => (int) ($rawMonthly[$key] ?? 0),
            $keys
        );

        return ['labels' => $labels, 'values' => $values];
    }

    private function buildStatusChart(Builder $query): array
    {
        $rawStatus = $query
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        return [
            'draft' => (int) ($rawStatus['draft'] ?? 0),
            'published' => (int) ($rawStatus['published'] ?? 0),
        ];
    }

    private function buildLevelChart(Builder $query): array
    {
        $rawLevel = $query
            ->selectRaw('level, COUNT(*) as total')
            ->groupBy('level')
            ->pluck('total', 'level');

        return [
            'desa' => (int) ($rawLevel['desa'] ?? 0),
            'kecamatan' => (int) ($rawLevel['kecamatan'] ?? 0),
        ];
    }

    private function buildByDesaChart(User $user, Builder $query): array
    {
        if (! $this->isValidKecamatanScopeUser($user)) {
            return [
                'labels' => [],
                'values' => [],
                'books_total' => [],
                'books_filled' => [],
            ];
        }

        $kecamatanAreaId = (int) $user->area_id;
        $desaAreas = $this->areaRepository
            ->getDesaByKecamatan($kecamatanAreaId)
            ->sortBy('name')
            ->values();

        if ($desaAreas->isEmpty()) {
            return [
                'labels' => [],
                'values' => [],
                'books_total' => [],
                'books_filled' => [],
            ];
        }

        $rawByDesa = $query
            ->where('level', ScopeLevel::DESA->value)
            ->selectRaw('area_id, COUNT(*) as total')
            ->groupBy('area_id')
            ->pluck('total', 'area_id');

        $moduleSlugs = $this->dashboardDocumentCoverageRepository->trackedModuleSlugs();
        $bookTotalPerDesa = count($moduleSlugs);
        $rawBooksByDesa = collect(
            $this->dashboardDocumentCoverageRepository->buildGroupBreakdownByDesa($user, $moduleSlugs)
        )
            ->mapWithKeys(
                static fn (array $item): array => [(int) ($item['desa_id'] ?? 0) => $item]
            )
            ->all();

        return [
            'labels' => $desaAreas->map(
                static fn ($desa): string => (string) ($desa->name ?? '-')
            )->all(),
            'values' => $desaAreas->map(
                static function ($desa) use ($rawByDesa): int {
                    $areaId = (int) ($desa->id ?? 0);

                    return (int) ($rawByDesa[$areaId] ?? 0);
                }
            )->all(),
            'books_total' => $desaAreas->map(
                static fn (): int => $bookTotalPerDesa
            )->all(),
            'books_filled' => $desaAreas->map(
                static function ($desa) use ($rawBooksByDesa): int {
                    $areaId = (int) ($desa->id ?? 0);
                    $perModule = $rawBooksByDesa[$areaId]['per_module'] ?? null;

                    if (! is_array($perModule)) {
                        return 0;
                    }

                    return array_reduce(
                        $perModule,
                        static fn (int $filled, mixed $total): int => $filled + ((int) $total > 0 ? 1 : 0),
                        0
                    );
                }
            )->all(),
        ];
    }

    private function isValidKecamatanScopeUser(User $user): bool
    {
        if (! $user->hasRoleForScope(ScopeLevel::KECAMATAN->value)) {
            return false;
        }

        if (! is_numeric($user->area_id)) {
            return false;
        }

        $areaId = (int) $user->area_id;
        $areaLevel = $user->relationLoaded('area')
            ? $user->area?->level
            : $this->areaRepository->getLevelById($areaId);

        return $areaLevel === ScopeLevel::KECAMATAN->value;
    }
}
