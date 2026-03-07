<?php

namespace App\Domains\Wilayah\WarungPkk\Actions;

use App\Domains\Wilayah\WarungPkk\DTOs\WarungPkkData;
use App\Domains\Wilayah\WarungPkk\Models\WarungPkk;
use App\Domains\Wilayah\WarungPkk\Repositories\WarungPkkRepositoryInterface;
use App\Domains\Wilayah\WarungPkk\Services\WarungPkkScopeService;
use App\Domains\Wilayah\Services\ActiveBudgetYearContextService;

class CreateScopedWarungPkkAction
{
    public function __construct(
        private readonly WarungPkkRepositoryInterface $warungPkkRepository,
        private readonly WarungPkkScopeService $warungPkkScopeService,
        private readonly ActiveBudgetYearContextService $activeBudgetYearContextService
    ) {
    }

    public function execute(array $payload, string $level): WarungPkk
    {
        $tahunAnggaran = $this->activeBudgetYearContextService->resolveForUser(auth()->user());

        $data = WarungPkkData::fromArray([
            'nama_warung_pkk' => $payload['nama_warung_pkk'],
            'nama_pengelola' => $payload['nama_pengelola'],
            'komoditi' => $payload['komoditi'],
            'kategori' => $payload['kategori'],
            'volume' => $payload['volume'],
            'tahun_anggaran' => $tahunAnggaran,
            'level' => $level,
            'area_id' => $this->warungPkkScopeService->requireUserAreaId(),
            'created_by' => (int) auth()->id(),
        ]);

        return $this->warungPkkRepository->store($data);
    }
}
