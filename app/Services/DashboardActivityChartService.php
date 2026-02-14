<?php

namespace App\Services;

use App\Domains\Wilayah\Activities\Models\Activity;
use App\Domains\Wilayah\Models\Area;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

class DashboardActivityChartService
{
    public function buildForUser(User $user): array
    {
        $baseQuery = $this->buildScopedQuery($user);

        $monthly = $this->buildMonthlyChart((clone $baseQuery));
        $status = $this->buildStatusChart((clone $baseQuery));
        $level = $this->buildLevelChart((clone $baseQuery));

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
            ],
        ];
    }

    private function buildScopedQuery(User $user): Builder
    {
        $query = Activity::query();

        if ($user->hasRole('super-admin')) {
            return $query;
        }

        $areaId = (int) $user->area_id;

        if ($user->scope === 'desa') {
            return $query->where('level', 'desa')->where('area_id', $areaId);
        }

        if ($user->scope === 'kecamatan') {
            $desaIds = Area::query()
                ->where('level', 'desa')
                ->where('parent_id', $areaId)
                ->pluck('id');

            return $query->where(function (Builder $scoped) use ($areaId, $desaIds) {
                $scoped->where(function (Builder $kecamatanScope) use ($areaId) {
                    $kecamatanScope->where('level', 'kecamatan')->where('area_id', $areaId);
                })->orWhere(function (Builder $desaScope) use ($desaIds) {
                    $desaScope->where('level', 'desa')->whereIn('area_id', $desaIds);
                });
            });
        }

        return $query->whereRaw('1 = 0');
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
}
