<?php

namespace App\Domains\Wilayah\Dashboard\UseCases;

use App\Domains\Wilayah\Posyandu\Models\Posyandu;
use App\Domains\Wilayah\Enums\ScopeLevel;
use App\Models\User;
use Illuminate\Support\Collection;

class BuildPosyanduChartPayloadUseCase
{
    /**
     * @return array<string, array{title: string, labels: list<string>, series: array<string, list<int>>}>
     */
    public function execute(User $user): array
    {
        $scope = $user->scope;
        $areaId = (int) $user->area_id;

        $query = Posyandu::query()
            ->where('tahun_anggaran', $user->active_budget_year);

        if ($scope === ScopeLevel::KECAMATAN->value) {
            $query->where(function ($q) use ($areaId) {
                $q->where('area_id', $areaId)
                  ->where('level', 'kecamatan')
                  ->orWhereIn('area_id', function ($q) use ($areaId) {
                      $q->select('id')->from('areas')->where('parent_id', $areaId);
                  });
            });
            $data = $query->with('area')->get();
            $grouped = $data->groupBy(fn ($item) => $item->level === 'kecamatan' ? 'Kecamatan' : ($item->area?->name ?? 'Desa Unknown'));
            $labelKey = 'Wilayah';
        } elseif ($scope === ScopeLevel::DESA->value) {
            $query->where('area_id', $areaId)->where('level', 'desa');
            $data = $query->get();
            $grouped = $data->groupBy(fn ($item) => $item->nama_dusun_lingkungan ?? 'Dusun Unknown');
            $labelKey = 'Dusun';
        } else {
            return [];
        }

        if ($data->isEmpty()) {
            return [];
        }

        $labels = $grouped->keys()->map(fn ($l) => (string) $l)->all();

        return [
            'posyandu_distribution' => [
                'title' => 'Distribusi Jenis Posyandu per ' . $labelKey,
                'labels' => $labels,
                'series' => [
                    'Pratama' => $grouped->map(fn ($items) => $items->where('jenis_posyandu', 'Pratama')->count())->values()->all(),
                    'Madya' => $grouped->map(fn ($items) => $items->where('jenis_posyandu', 'Madya')->count())->values()->all(),
                    'Purnama' => $grouped->map(fn ($items) => $items->where('jenis_posyandu', 'Purnama')->count())->values()->all(),
                    'Mandiri' => $grouped->map(fn ($items) => $items->where('jenis_posyandu', 'Mandiri')->count())->values()->all(),
                ],
            ],
            'posyandu_stats' => [
                'title' => 'Statistik Pengunjung & Petugas per ' . $labelKey,
                'labels' => $labels,
                'series' => [
                    'Pengunjung L' => $grouped->map(fn ($items) => (int) $items->sum('jumlah_pengunjung_l'))->values()->all(),
                    'Pengunjung P' => $grouped->map(fn ($items) => (int) $items->sum('jumlah_pengunjung_p'))->values()->all(),
                    'Petugas L' => $grouped->map(fn ($items) => (int) $items->sum('jumlah_petugas_l'))->values()->all(),
                    'Petugas P' => $grouped->map(fn ($items) => (int) $items->sum('jumlah_petugas_p'))->values()->all(),
                ],
            ],
        ];
    }
}
