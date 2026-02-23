<?php

namespace App\Domains\Wilayah\Dashboard\Repositories;

use App\Domains\Wilayah\Activities\Models\Activity;
use App\Domains\Wilayah\AgendaSurat\Models\AgendaSurat;
use App\Domains\Wilayah\AnggotaTimPenggerak\Models\AnggotaTimPenggerak;
use App\Domains\Wilayah\BukuKeuangan\Models\BukuKeuangan;
use App\Domains\Wilayah\DataIndustriRumahTangga\Models\DataIndustriRumahTangga;
use App\Domains\Wilayah\DataKegiatanWarga\Models\DataKegiatanWarga;
use App\Domains\Wilayah\DataKeluarga\Models\DataKeluarga;
use App\Domains\Wilayah\DataPelatihanKader\Models\DataPelatihanKader;
use App\Domains\Wilayah\DataPemanfaatanTanahPekaranganHatinyaPkk\Models\DataPemanfaatanTanahPekaranganHatinyaPkk;
use App\Domains\Wilayah\DataWarga\Models\DataWarga;
use App\Domains\Wilayah\Enums\ScopeLevel;
use App\Domains\Wilayah\Inventaris\Models\Inventaris;
use App\Domains\Wilayah\KaderKhusus\Models\KaderKhusus;
use App\Domains\Wilayah\KejarPaket\Models\KejarPaket;
use App\Domains\Wilayah\Koperasi\Models\Koperasi;
use App\Domains\Wilayah\Posyandu\Models\Posyandu;
use App\Domains\Wilayah\Repositories\AreaRepositoryInterface;
use App\Domains\Wilayah\SimulasiPenyuluhan\Models\SimulasiPenyuluhan;
use App\Domains\Wilayah\TamanBacaan\Models\TamanBacaan;
use App\Domains\Wilayah\WarungPkk\Models\WarungPkk;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class DashboardDocumentCoverageRepository implements DashboardDocumentCoverageRepositoryInterface
{
    public function __construct(
        private readonly AreaRepositoryInterface $areaRepository
    ) {
    }

    public function buildForUser(User $user): array
    {
        if (! is_numeric($user->area_id)) {
            return $this->emptyPayload();
        }

        $areaId = (int) $user->area_id;
        $scope = $this->resolveEffectiveScope($user, $areaId);

        if (! is_string($scope)) {
            return $this->emptyPayload();
        }

        $desaIds = $scope === ScopeLevel::KECAMATAN->value
            ? $this->areaRepository->getDesaByKecamatan($areaId)->pluck('id')->map(static fn ($id): int => (int) $id)->values()
            : collect();

        $moduleItems = [];
        $modelTotals = [];
        $totalEntries = 0;
        $totalDesa = 0;
        $totalKecamatan = 0;

        foreach ($this->moduleDefinitions() as $module) {
            $modelClass = $module['model'];
            $includeDescendantForKecamatan = $module['include_descendant_for_kecamatan'];
            $modelCacheKey = sprintf(
                '%s|%s',
                $modelClass,
                $includeDescendantForKecamatan ? 'with_desc' : 'without_desc'
            );

            if (! array_key_exists($modelCacheKey, $modelTotals)) {
                $modelTotals[$modelCacheKey] = $this->countModelByScope(
                    $modelClass,
                    $scope,
                    $areaId,
                    $desaIds,
                    $includeDescendantForKecamatan
                );
            }

            $totals = $modelTotals[$modelCacheKey];
            $moduleItems[] = [
                'lampiran' => $module['lampiran'],
                'lampiran_group' => $module['lampiran_group'],
                'slug' => $module['slug'],
                'label' => $module['label'],
                'desa' => $totals['desa'],
                'kecamatan' => $totals['kecamatan'],
                'total' => $totals['total'],
            ];

            $totalEntries += $totals['total'];
            $totalDesa += $totals['desa'];
            $totalKecamatan += $totals['kecamatan'];
        }

        $bukuTerisi = collect($moduleItems)->filter(
            static fn (array $item): bool => $item['total'] > 0
        )->count();

        $totalBuku = count($moduleItems);
        $lampiranItems = $this->buildLampiranItems($moduleItems);

        return [
            'stats' => [
                'total_buku_tracked' => $totalBuku,
                'buku_terisi' => $bukuTerisi,
                'buku_belum_terisi' => $totalBuku - $bukuTerisi,
                'total_entri_buku' => $totalEntries,
            ],
            'charts' => [
                'coverage_per_buku' => [
                    'labels' => array_map(
                        static fn (array $item): string => $item['slug'],
                        $moduleItems
                    ),
                    'values' => array_map(
                        static fn (array $item): int => $item['total'],
                        $moduleItems
                    ),
                    'items' => $moduleItems,
                ],
                'coverage_per_lampiran' => [
                    'labels' => array_map(
                        static fn (array $item): string => $item['lampiran_group'],
                        $lampiranItems
                    ),
                    'values' => array_map(
                        static fn (array $item): int => $item['total'],
                        $lampiranItems
                    ),
                    'items' => $lampiranItems,
                ],
                'level_distribution' => [
                    'labels' => ['Desa', 'Kecamatan'],
                    'values' => [$totalDesa, $totalKecamatan],
                ],
            ],
        ];
    }

    /**
     * @param array<int, array<string, mixed>> $moduleItems
     * @return array<int, array{lampiran_group: string, total: int}>
     */
    private function buildLampiranItems(array $moduleItems): array
    {
        $groups = [
            '4.9' => 0,
            '4.10' => 0,
            '4.11' => 0,
            '4.12' => 0,
            '4.13' => 0,
            '4.14' => 0,
            '4.15' => 0,
        ];

        foreach ($moduleItems as $moduleItem) {
            $group = $moduleItem['lampiran_group'];
            if (! array_key_exists($group, $groups)) {
                continue;
            }

            $groups[$group] += (int) $moduleItem['total'];
        }

        return collect($groups)
            ->map(
                static fn (int $total, string $group): array => [
                    'lampiran_group' => $group,
                    'total' => $total,
                ]
            )
            ->values()
            ->all();
    }

    private function resolveEffectiveScope(User $user, int $areaId): ?string
    {
        $areaLevel = $user->relationLoaded('area')
            ? $user->area?->level
            : $this->areaRepository->getLevelById($areaId);

        if (! is_string($areaLevel)) {
            return null;
        }

        if (
            $areaLevel === ScopeLevel::DESA->value
            && $user->hasRoleForScope(ScopeLevel::DESA->value)
        ) {
            return ScopeLevel::DESA->value;
        }

        if (
            $areaLevel === ScopeLevel::KECAMATAN->value
            && $user->hasRoleForScope(ScopeLevel::KECAMATAN->value)
        ) {
            return ScopeLevel::KECAMATAN->value;
        }

        return null;
    }

    /**
     * @param class-string<Model> $modelClass
     * @param Collection<int, int> $desaIds
     * @return array{desa: int, kecamatan: int, total: int}
     */
    private function countModelByScope(
        string $modelClass,
        string $scope,
        int $areaId,
        Collection $desaIds,
        bool $includeDescendantForKecamatan
    ): array {
        $query = $modelClass::query();

        if ($scope === ScopeLevel::DESA->value) {
            $query
                ->where('level', ScopeLevel::DESA->value)
                ->where('area_id', $areaId);
        } elseif (! $includeDescendantForKecamatan) {
            $query
                ->where('level', ScopeLevel::KECAMATAN->value)
                ->where('area_id', $areaId);
        } else {
            $query->where(function (Builder $scopedQuery) use ($areaId, $desaIds) {
                $scopedQuery->where(function (Builder $kecamatanQuery) use ($areaId) {
                    $kecamatanQuery
                        ->where('level', ScopeLevel::KECAMATAN->value)
                        ->where('area_id', $areaId);
                })->orWhere(function (Builder $desaQuery) use ($desaIds) {
                    if ($desaIds->isEmpty()) {
                        $desaQuery->whereRaw('1 = 0');

                        return;
                    }

                    $desaQuery
                        ->where('level', ScopeLevel::DESA->value)
                        ->whereIn('area_id', $desaIds->all());
                });
            });
        }

        $grouped = $query
            ->selectRaw('level, COUNT(*) as total')
            ->groupBy('level')
            ->pluck('total', 'level');

        $desa = (int) ($grouped[ScopeLevel::DESA->value] ?? 0);
        $kecamatan = (int) ($grouped[ScopeLevel::KECAMATAN->value] ?? 0);

        return [
            'desa' => $desa,
            'kecamatan' => $kecamatan,
            'total' => $desa + $kecamatan,
        ];
    }

    /**
     * @return array<int, array{
     *     lampiran: string,
     *     lampiran_group: string,
     *     slug: string,
     *     label: string,
     *     model: class-string<Model>,
     *     include_descendant_for_kecamatan: bool
     * }>
     */
    private function moduleDefinitions(): array
    {
        return [
            [
                'lampiran' => '4.9a',
                'lampiran_group' => '4.9',
                'slug' => 'anggota-tim-penggerak',
                'label' => 'Buku Daftar Anggota Tim Penggerak PKK',
                'model' => AnggotaTimPenggerak::class,
                'include_descendant_for_kecamatan' => false,
            ],
            [
                'lampiran' => '4.9b',
                'lampiran_group' => '4.9',
                'slug' => 'kader-khusus',
                'label' => 'Buku Daftar Kader Tim Penggerak PKK',
                'model' => KaderKhusus::class,
                'include_descendant_for_kecamatan' => false,
            ],
            [
                'lampiran' => '4.10',
                'lampiran_group' => '4.10',
                'slug' => 'agenda-surat',
                'label' => 'Buku Agenda Surat Masuk/Keluar',
                'model' => AgendaSurat::class,
                'include_descendant_for_kecamatan' => false,
            ],
            [
                'lampiran' => '4.11',
                'lampiran_group' => '4.11',
                'slug' => 'buku-keuangan',
                'label' => 'Buku Keuangan',
                'model' => BukuKeuangan::class,
                'include_descendant_for_kecamatan' => false,
            ],
            [
                'lampiran' => '4.12',
                'lampiran_group' => '4.12',
                'slug' => 'inventaris',
                'label' => 'Buku Inventaris',
                'model' => Inventaris::class,
                'include_descendant_for_kecamatan' => false,
            ],
            [
                'lampiran' => '4.13',
                'lampiran_group' => '4.13',
                'slug' => 'activities',
                'label' => 'Buku Kegiatan',
                'model' => Activity::class,
                'include_descendant_for_kecamatan' => true,
            ],
            [
                'lampiran' => '4.14.1a',
                'lampiran_group' => '4.14',
                'slug' => 'data-warga',
                'label' => 'Data Warga',
                'model' => DataWarga::class,
                'include_descendant_for_kecamatan' => false,
            ],
            [
                'lampiran' => '4.14.1b',
                'lampiran_group' => '4.14',
                'slug' => 'data-kegiatan-warga',
                'label' => 'Data Kegiatan Warga',
                'model' => DataKegiatanWarga::class,
                'include_descendant_for_kecamatan' => false,
            ],
            [
                'lampiran' => '4.14.2a',
                'lampiran_group' => '4.14',
                'slug' => 'data-keluarga',
                'label' => 'Data Keluarga',
                'model' => DataKeluarga::class,
                'include_descendant_for_kecamatan' => false,
            ],
            [
                'lampiran' => '4.14.2b',
                'lampiran_group' => '4.14',
                'slug' => 'data-pemanfaatan-tanah-pekarangan-hatinya-pkk',
                'label' => 'Data Pemanfaatan Tanah Pekarangan/HATINYA PKK',
                'model' => DataPemanfaatanTanahPekaranganHatinyaPkk::class,
                'include_descendant_for_kecamatan' => false,
            ],
            [
                'lampiran' => '4.14.2c',
                'lampiran_group' => '4.14',
                'slug' => 'data-industri-rumah-tangga',
                'label' => 'Data Industri Rumah Tangga',
                'model' => DataIndustriRumahTangga::class,
                'include_descendant_for_kecamatan' => false,
            ],
            [
                'lampiran' => '4.14.3',
                'lampiran_group' => '4.14',
                'slug' => 'data-pelatihan-kader',
                'label' => 'Data Pelatihan Kader',
                'model' => DataPelatihanKader::class,
                'include_descendant_for_kecamatan' => false,
            ],
            [
                'lampiran' => '4.14.4a',
                'lampiran_group' => '4.14',
                'slug' => 'warung-pkk',
                'label' => 'Data Aset (Sarana) Desa/Kelurahan',
                'model' => WarungPkk::class,
                'include_descendant_for_kecamatan' => false,
            ],
            [
                'lampiran' => '4.14.4b',
                'lampiran_group' => '4.14',
                'slug' => 'taman-bacaan',
                'label' => 'Data Isian Taman Bacaan/Perpustakaan',
                'model' => TamanBacaan::class,
                'include_descendant_for_kecamatan' => false,
            ],
            [
                'lampiran' => '4.14.4c',
                'lampiran_group' => '4.14',
                'slug' => 'koperasi',
                'label' => 'Data Isian Koperasi',
                'model' => Koperasi::class,
                'include_descendant_for_kecamatan' => false,
            ],
            [
                'lampiran' => '4.14.4d',
                'lampiran_group' => '4.14',
                'slug' => 'kejar-paket',
                'label' => 'Data Isian Kejar Paket/KF/PAUD',
                'model' => KejarPaket::class,
                'include_descendant_for_kecamatan' => false,
            ],
            [
                'lampiran' => '4.14.4e',
                'lampiran_group' => '4.14',
                'slug' => 'posyandu',
                'label' => 'Data Isian Posyandu oleh TP PKK',
                'model' => Posyandu::class,
                'include_descendant_for_kecamatan' => false,
            ],
            [
                'lampiran' => '4.14.4f',
                'lampiran_group' => '4.14',
                'slug' => 'simulasi-penyuluhan',
                'label' => 'Data Isian Kelompok Simulasi dan Penyuluhan',
                'model' => SimulasiPenyuluhan::class,
                'include_descendant_for_kecamatan' => false,
            ],
            [
                'lampiran' => '4.15',
                'lampiran_group' => '4.15',
                'slug' => 'catatan-keluarga',
                'label' => 'Catatan Keluarga',
                'model' => DataWarga::class,
                'include_descendant_for_kecamatan' => false,
            ],
        ];
    }

    private function emptyPayload(): array
    {
        $moduleItems = array_map(
            static fn (array $module): array => [
                'lampiran' => $module['lampiran'],
                'lampiran_group' => $module['lampiran_group'],
                'slug' => $module['slug'],
                'label' => $module['label'],
                'desa' => 0,
                'kecamatan' => 0,
                'total' => 0,
            ],
            $this->moduleDefinitions()
        );

        $lampiranItems = $this->buildLampiranItems($moduleItems);

        return [
            'stats' => [
                'total_buku_tracked' => count($moduleItems),
                'buku_terisi' => 0,
                'buku_belum_terisi' => count($moduleItems),
                'total_entri_buku' => 0,
            ],
            'charts' => [
                'coverage_per_buku' => [
                    'labels' => array_map(
                        static fn (array $item): string => $item['slug'],
                        $moduleItems
                    ),
                    'values' => array_fill(0, count($moduleItems), 0),
                    'items' => $moduleItems,
                ],
                'coverage_per_lampiran' => [
                    'labels' => array_map(
                        static fn (array $item): string => $item['lampiran_group'],
                        $lampiranItems
                    ),
                    'values' => array_fill(0, count($lampiranItems), 0),
                    'items' => $lampiranItems,
                ],
                'level_distribution' => [
                    'labels' => ['Desa', 'Kecamatan'],
                    'values' => [0, 0],
                ],
            ],
        ];
    }
}
