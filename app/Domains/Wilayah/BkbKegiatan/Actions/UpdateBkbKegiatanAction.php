<?php

namespace App\Domains\Wilayah\BkbKegiatan\Actions;

use App\Domains\Wilayah\BkbKegiatan\DTOs\BkbKegiatanData;
use App\Domains\Wilayah\BkbKegiatan\Models\BkbKegiatan;
use App\Domains\Wilayah\BkbKegiatan\Repositories\BkbKegiatanRepositoryInterface;
use App\Domains\Wilayah\Services\ActiveBudgetYearContextService;

class UpdateBkbKegiatanAction
{
    public function __construct(
        private readonly BkbKegiatanRepositoryInterface $bkbKegiatanRepository,
        private readonly ActiveBudgetYearContextService $activeBudgetYearContextService
    ) {
    }

    public function execute(BkbKegiatan $bkbKegiatan, array $payload): BkbKegiatan
    {
        $tahunAnggaran = $this->activeBudgetYearContextService->resolveForUser(auth()->user());

        $data = BkbKegiatanData::fromArray([
            'jumlah_kelompok' => $payload['jumlah_kelompok'],
            'jumlah_ibu_peserta' => $payload['jumlah_ibu_peserta'],
            'jumlah_ape_set' => $payload['jumlah_ape_set'],
            'jumlah_kelompok_simulasi' => $payload['jumlah_kelompok_simulasi'],
            'keterangan' => $payload['keterangan'] ?? null,
            'tahun_anggaran' => $tahunAnggaran,
            'level' => $bkbKegiatan->level,
            'area_id' => (int) $bkbKegiatan->area_id,
            'created_by' => (int) $bkbKegiatan->created_by,
        ]);

        return $this->bkbKegiatanRepository->update($bkbKegiatan, $data);
    }
}
