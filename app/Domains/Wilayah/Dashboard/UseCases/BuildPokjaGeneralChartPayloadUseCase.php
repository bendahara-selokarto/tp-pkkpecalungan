<?php

namespace App\Domains\Wilayah\Dashboard\UseCases;

use App\Domains\Wilayah\CatatanKeluarga\Repositories\CatatanKeluargaRepositoryInterface;
use App\Domains\Wilayah\Enums\ScopeLevel;
use App\Models\User;
use Illuminate\Support\Collection;

class BuildPokjaGeneralChartPayloadUseCase
{
    public function __construct(
        private readonly CatatanKeluargaRepositoryInterface $catatanKeluargaRepository
    ) {}

    /**
     * @return array<string, array{title: string, labels: list<string>, series: array<string, list<int>>}>
     */
    public function execute(User $user, string $pokja): array
    {
        $scope = $user->scope;
        $areaId = (int) $user->area_id;

        if ($scope === ScopeLevel::KECAMATAN->value) {
            $data = $this->fetchData($scope, $areaId, $pokja);
            $labelKey = 'nama_desa_kelurahan';
            $titleSuffix = ' per Wilayah';
        } elseif ($scope === ScopeLevel::DESA->value) {
            $data = $this->fetchData($scope, $areaId, $pokja);
            $labelKey = 'nama_dusun_lingkungan_atau_sebutan_lain';
            $titleSuffix = ' per Dusun';
        } else {
            return [];
        }

        if ($data->isEmpty()) {
            return [];
        }

        $labels = $data->pluck($labelKey)->map(fn ($l) => (string) $l)->all();

        if ($pokja === 'pokja-i') {
            return $this->formatPokjaI($data, $labels, $titleSuffix);
        }

        if ($pokja === 'pokja-iii') {
            return $this->formatPokjaIII($data, $labels, $titleSuffix);
        }

        return [];
    }

    private function fetchData(string $scope, int $areaId, string $pokja): Collection
    {
        if ($pokja === 'pokja-i') {
            return $scope === ScopeLevel::KECAMATAN->value
                ? $this->catatanKeluargaRepository->getDataKegiatanPkkPokjaIByLevelAndArea(ScopeLevel::DESA->value, $areaId)
                : $this->catatanKeluargaRepository->getDataKegiatanPkkPokjaIByLevelAndArea(ScopeLevel::DESA->value, $areaId);
        }

        if ($pokja === 'pokja-iii') {
            return $scope === ScopeLevel::KECAMATAN->value
                ? $this->catatanKeluargaRepository->getDataKegiatanPkkPokjaIiiByLevelAndArea(ScopeLevel::DESA->value, $areaId)
                : $this->catatanKeluargaRepository->getDataKegiatanPkkPokjaIiiByLevelAndArea(ScopeLevel::DESA->value, $areaId);
        }

        return collect();
    }

    private function formatPokjaI(Collection $data, array $labels, string $suffix): array
    {
        return [
            'pancasila_stats' => [
                'title' => 'Statistik Penghayatan & Pengamalan Pancasila' . $suffix,
                'labels' => $labels,
                'series' => [
                    'PKBN' => $data->map(fn ($item) => (int) ($item['pkbn_kegiatan'] ?? 0))->all(),
                    'KDRT' => $data->map(fn ($item) => (int) ($item['ktiat_kegiatan'] ?? 0))->all(),
                    'Pola Asuh' => $data->map(fn ($item) => (int) ($item['kisah_kegiatan'] ?? 0))->all(),
                    'Lansia' => $data->map(fn ($item) => (int) ($item['kilas_kegiatan'] ?? 0))->all(),
                ],
            ],
        ];
    }

    private function formatPokjaIII(Collection $data, array $labels, string $suffix): array
    {
        return [
            'pangan_stats' => [
                'title' => 'Ketahanan Pangan & Rumah Sehat' . $suffix,
                'labels' => $labels,
                'series' => [
                    'Beras' => $data->map(fn ($item) => (int) ($item['jumlah_keluarga_beras'] ?? 0))->all(),
                    'Non Beras' => $data->map(fn ($item) => (int) ($item['jumlah_keluarga_non_beras'] ?? 0))->all(),
                    'Rumah Sehat' => $data->map(fn ($item) => (int) ($item['jumlah_rumah_sehat_layak_huni'] ?? 0))->all(),
                ],
            ],
            'hatinya_pkk' => [
                'title' => 'Pemanfaatan Tanah Pekarangan (HATINYA PKK)' . $suffix,
                'labels' => $labels,
                'series' => [
                    'Peternakan' => $data->map(fn ($item) => (int) ($item['jumlah_peternakan'] ?? 0))->all(),
                    'Perikanan' => $data->map(fn ($item) => (int) ($item['jumlah_perikanan'] ?? 0))->all(),
                    'Warung Hidup' => $data->map(fn ($item) => (int) ($item['jumlah_warung_hidup'] ?? 0))->all(),
                    'TOGA' => $data->map(fn ($item) => (int) ($item['jumlah_toga'] ?? 0))->all(),
                ],
            ],
        ];
    }
}
