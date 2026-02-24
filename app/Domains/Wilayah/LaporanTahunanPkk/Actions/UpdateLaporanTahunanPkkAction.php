<?php

namespace App\Domains\Wilayah\LaporanTahunanPkk\Actions;

use App\Domains\Wilayah\LaporanTahunanPkk\Models\LaporanTahunanPkkReport;
use App\Domains\Wilayah\LaporanTahunanPkk\Repositories\LaporanTahunanPkkRepositoryInterface;

class UpdateLaporanTahunanPkkAction
{
    public function __construct(
        private readonly LaporanTahunanPkkRepositoryInterface $repository
    ) {
    }

    public function execute(LaporanTahunanPkkReport $report, array $payload): LaporanTahunanPkkReport
    {
        $updated = $this->repository->updateReport($report, [
            'judul_laporan' => (string) ($payload['judul_laporan'] ?? $report->judul_laporan),
            'tahun_laporan' => (int) ($payload['tahun_laporan'] ?? $report->tahun_laporan),
            'pendahuluan' => $payload['pendahuluan'] ?? null,
            'keberhasilan' => $payload['keberhasilan'] ?? null,
            'hambatan' => $payload['hambatan'] ?? null,
            'kesimpulan' => $payload['kesimpulan'] ?? null,
            'penutup' => $payload['penutup'] ?? null,
            'disusun_oleh' => $payload['disusun_oleh'] ?? null,
            'jabatan_penanda_tangan' => $payload['jabatan_penanda_tangan'] ?? null,
            'nama_penanda_tangan' => $payload['nama_penanda_tangan'] ?? null,
        ]);

        if (is_array($payload['manual_entries'] ?? null)) {
            $this->repository->replaceManualEntries(
                $updated,
                $payload['manual_entries'],
                (string) $updated->level,
                (int) $updated->area_id,
                (int) $updated->created_by
            );
        }

        return $updated->fresh(['entries']) ?? $updated->load('entries');
    }
}

