<?php

namespace App\Domains\Wilayah\Dashboard\UseCases;

use App\Domains\Wilayah\PraKoperasiUp2k\Models\PraKoperasiUp2k;
use App\Domains\Wilayah\Enums\ScopeLevel;
use App\Models\User;
use Illuminate\Support\Collection;

class BuildUp2kChartPayloadUseCase
{
    /**
     * @return array<string, array{title: string, labels: list<string>, series: array<string, list<int>>}>
     */
    public function execute(User $user): array
    {
        $scope = $user->scope;
        $areaId = (int) $user->area_id;

        $query = PraKoperasiUp2k::query()
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
            'up2k_classification' => [
                'title' => 'Klasifikasi Kelompok UP2K per ' . $labelKey,
                'labels' => $labels,
                'series' => [
                    'Pemula' => $grouped->map(fn ($items) => $items->where('tingkat', 'Pemula')->count())->values()->all(),
                    'Madya' => $grouped->map(fn ($items) => $items->where('tingkat', 'Madya')->count())->values()->all(),
                    'Utama' => $grouped->map(fn ($items) => $items->where('tingkat', 'Utama')->count())->values()->all(),
                    'Mandiri' => $grouped->map(fn ($items) => $items->where('tingkat', 'Mandiri')->count())->values()->all(),
                ],
            ],
        ];
    }
}
