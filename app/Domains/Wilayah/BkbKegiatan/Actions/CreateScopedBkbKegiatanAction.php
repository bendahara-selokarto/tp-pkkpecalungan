<?php

namespace App\Domains\Wilayah\BkbKegiatan\Actions;

use App\Domains\Wilayah\BkbKegiatan\DTOs\BkbKegiatanData;
use App\Domains\Wilayah\BkbKegiatan\Models\BkbKegiatan;
use App\Domains\Wilayah\BkbKegiatan\Repositories\BkbKegiatanRepositoryInterface;
use App\Domains\Wilayah\BkbKegiatan\Services\BkbKegiatanScopeService;
use App\Domains\Wilayah\Services\ActiveBudgetYearContextService;

class CreateScopedBkbKegiatanAction
{
    public function __construct(
        private readonly BkbKegiatanRepositoryInterface $bkbKegiatanRepository,
        private readonly BkbKegiatanScopeService $bkbKegiatanScopeService,
        private readonly ActiveBudgetYearContextService $activeBudgetYearContextService
    ) {
    }

    public function execute(array $payload, string $level): BkbKegiatan
    {
        $tahunAnggaran = $this->activeBudgetYearContextService->resolveForUser(auth()->user());

        $data = BkbKegiatanData::fromArray([
            'jumlah_kelompok' => $payload['jumlah_kelompok'],
            'jumlah_ibu_peserta' => $payload['jumlah_ibu_peserta'],
            'jumlah_ape_set' => $payload['jumlah_ape_set'],
            'jumlah_kelompok_simulasi' => $payload['jumlah_kelompok_simulasi'],
            'keterangan' => $payload['keterangan'] ?? null,
            'tahun_anggaran' => $tahunAnggaran,
            'level' => $level,
            'area_id' => $this->bkbKegiatanScopeService->requireUserAreaId(),
            'created_by' => (int) auth()->id(),
        ]);

        return $this->bkbKegiatanRepository->store($data);
    }
}
