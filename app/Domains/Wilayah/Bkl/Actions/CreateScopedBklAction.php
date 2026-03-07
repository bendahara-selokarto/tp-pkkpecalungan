<?php

namespace App\Domains\Wilayah\Bkl\Actions;

use App\Domains\Wilayah\Bkl\DTOs\BklData;
use App\Domains\Wilayah\Bkl\Models\Bkl;
use App\Domains\Wilayah\Bkl\Repositories\BklRepositoryInterface;
use App\Domains\Wilayah\Bkl\Services\BklScopeService;
use App\Domains\Wilayah\Services\ActiveBudgetYearContextService;

class CreateScopedBklAction
{
    public function __construct(
        private readonly BklRepositoryInterface $bklRepository,
        private readonly BklScopeService $bklScopeService,
        private readonly ActiveBudgetYearContextService $activeBudgetYearContextService
    ) {
    }

    public function execute(array $payload, string $level): Bkl
    {
        $data = BklData::fromArray([
            'desa' => $payload['desa'],
            'nama_bkl' => $payload['nama_bkl'],
            'no_tgl_sk' => $payload['no_tgl_sk'],
            'nama_ketua_kelompok' => $payload['nama_ketua_kelompok'],
            'jumlah_anggota' => $payload['jumlah_anggota'],
            'kegiatan' => $payload['kegiatan'],
            'tahun_anggaran' => $this->activeBudgetYearContextService->requireForAuthenticatedUser(),
            'level' => $level,
            'area_id' => $this->bklScopeService->requireUserAreaId(),
            'created_by' => auth()->id(),
        ]);

        return $this->bklRepository->store($data);
    }
}
