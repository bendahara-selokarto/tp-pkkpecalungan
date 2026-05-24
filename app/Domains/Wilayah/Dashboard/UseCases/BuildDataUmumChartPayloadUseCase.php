<?php

namespace App\Domains\Wilayah\Dashboard\UseCases;

use App\Domains\Wilayah\CatatanKeluarga\Repositories\CatatanKeluargaRepositoryInterface;
use App\Domains\Wilayah\Enums\ScopeLevel;
use App\Models\User;
use Illuminate\Support\Collection;

class BuildDataUmumChartPayloadUseCase
{
    public function __construct(
        private readonly CatatanKeluargaRepositoryInterface $catatanKeluargaRepository
    ) {}

    /**
     * @return array<string, array{title: string, labels: list<string>, series: array<string, list<int>>}>
     */
    public function execute(User $user): array
    {
        $scope = $user->scope;
        $areaId = (int) $user->area_id;

        if ($scope === ScopeLevel::KECAMATAN->value) {
            $data = $this->catatanKeluargaRepository->getDataUmumPkkKecamatanByLevelAndArea(ScopeLevel::DESA->value, $areaId);
            $labelKey = 'nama_desa_kelurahan';
        } elseif ($scope === ScopeLevel::DESA->value) {
            $data = $this->catatanKeluargaRepository->getDataUmumPkkByLevelAndArea(ScopeLevel::DESA->value, $areaId);
            $labelKey = 'nama_dusun_lingkungan_atau_sebutan_lain';
        } else {
            return [];
        }

        if ($data->isEmpty()) {
            return [];
        }

        $labels = $data->pluck($labelKey)->map(fn ($l) => (string) $l)->all();

        $charts = [
            'kelompok' => [
                'title' => 'Jumlah Kelompok',
                'labels' => $labels,
                'series' => [
                    'PKK RW' => $data->map(fn ($item) => (int) ($item['jumlah_pkk_rw'] ?? 0))->all(),
                    'PKK RT' => $data->map(fn ($item) => (int) ($item['jumlah_pkk_rt'] ?? 0))->all(),
                    'Dasa Wisma' => $data->map(fn ($item) => (int) ($item['jumlah_dasa_wisma'] ?? 0))->all(),
                ],
            ],
            'rumah_tangga' => [
                'title' => 'Jumlah Rumah Tangga',
                'labels' => $labels,
                'series' => [
                    'KRT' => $data->map(fn ($item) => (int) ($item['jumlah_krt'] ?? 0))->all(),
                    'KK' => $data->map(fn ($item) => (int) ($item['jumlah_kk'] ?? 0))->all(),
                ],
            ],
            'jiwa' => [
                'title' => 'Jumlah Jiwa',
                'labels' => $labels,
                'series' => [
                    'Laki-laki (L)' => $data->map(fn ($item) => (int) ($item['jumlah_jiwa_l'] ?? 0))->all(),
                    'Perempuan (P)' => $data->map(fn ($item) => (int) ($item['jumlah_jiwa_p'] ?? 0))->all(),
                ],
            ],
            'kader' => [
                'title' => 'Jumlah Kader',
                'labels' => $labels,
                'series' => [
                    'Anggota TP PKK (L)' => $data->map(fn ($item) => (int) ($item['jumlah_kader_anggota_tp_pkk_l'] ?? 0))->all(),
                    'Anggota TP PKK (P)' => $data->map(fn ($item) => (int) ($item['jumlah_kader_anggota_tp_pkk_p'] ?? 0))->all(),
                    'Kader Umum (L)' => $data->map(fn ($item) => (int) ($item['jumlah_kader_umum_l'] ?? 0))->all(),
                    'Kader Umum (P)' => $data->map(fn ($item) => (int) ($item['jumlah_kader_umum_p'] ?? 0))->all(),
                    'Kader Khusus (L)' => $data->map(fn ($item) => (int) ($item['jumlah_kader_khusus_l'] ?? 0))->all(),
                    'Kader Khusus (P)' => $data->map(fn ($item) => (int) ($item['jumlah_kader_khusus_p'] ?? 0))->all(),
                ],
            ],
            'sekretariat' => [
                'title' => 'Tenaga Sekretariat',
                'labels' => $labels,
                'series' => [
                    'Honorer (L)' => $data->map(fn ($item) => (int) ($item['jumlah_tenaga_sekretariat_honorer_l'] ?? 0))->all(),
                    'Honorer (P)' => $data->map(fn ($item) => (int) ($item['jumlah_tenaga_sekretariat_honorer_p'] ?? 0))->all(),
                    'Bantuan (L)' => $data->map(fn ($item) => (int) ($item['jumlah_tenaga_sekretariat_bantuan_l'] ?? 0))->all(),
                    'Bantuan (P)' => $data->map(fn ($item) => (int) ($item['jumlah_tenaga_sekretariat_bantuan_p'] ?? 0))->all(),
                ],
            ],
        ];

        if ($scope === ScopeLevel::KECAMATAN->value) {
            $charts['kelompok']['series'] = array_merge(
                ['Dusun/Lingkungan' => $data->map(fn ($item) => (int) ($item['jumlah_dusun_lingkungan'] ?? 0))->all()],
                $charts['kelompok']['series']
            );
        }

        return $charts;
    }
}
