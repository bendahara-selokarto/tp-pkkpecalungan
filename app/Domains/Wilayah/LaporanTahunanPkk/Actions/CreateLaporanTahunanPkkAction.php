<?php

namespace App\Domains\Wilayah\LaporanTahunanPkk\Actions;

use App\Domains\Wilayah\LaporanTahunanPkk\Models\LaporanTahunanPkkReport;
use App\Domains\Wilayah\LaporanTahunanPkk\Repositories\LaporanTahunanPkkRepositoryInterface;
use App\Domains\Wilayah\LaporanTahunanPkk\Services\LaporanTahunanPkkScopeService;

class CreateLaporanTahunanPkkAction
{
    public function __construct(
        private readonly LaporanTahunanPkkRepositoryInterface $repository,
        private readonly LaporanTahunanPkkScopeService $scopeService
    ) {
    }

    public function execute(array $payload, string $level): LaporanTahunanPkkReport
    {
        $areaId = $this->scopeService->requireUserAreaId();
        $createdBy = (int) auth()->id();
        $tahunLaporan = (int) ($payload['tahun_laporan'] ?? now()->year);

        $report = $this->repository->storeReport([
            'judul_laporan' => (string) ($payload['judul_laporan'] ?? config('laporan_tahunan_pkk.module.label')),
            'tahun_laporan' => $tahunLaporan,
            'pendahuluan' => $payload['pendahuluan'] ?? null,
            'keberhasilan' => $payload['keberhasilan'] ?? null,
            'hambatan' => $payload['hambatan'] ?? null,
            'kesimpulan' => $payload['kesimpulan'] ?? null,
            'penutup' => $payload['penutup'] ?? null,
            'disusun_oleh' => $payload['disusun_oleh'] ?? null,
            'jabatan_penanda_tangan' => $payload['jabatan_penanda_tangan'] ?? null,
            'nama_penanda_tangan' => $payload['nama_penanda_tangan'] ?? null,
            'level' => $level,
            'area_id' => $areaId,
            'created_by' => $createdBy,
        ]);

        $this->repository->replaceManualEntries(
            $report,
            is_array($payload['manual_entries'] ?? null) ? $payload['manual_entries'] : [],
            $level,
            $areaId,
            $createdBy
        );

        return $report->fresh(['entries']) ?? $report->load('entries');
    }
}

