<?php

namespace App\Domains\Wilayah\BukuKeuangan\UseCases;

use App\Domains\Wilayah\BukuKeuangan\Models\BukuKeuangan;
use App\Domains\Wilayah\BukuKeuangan\Repositories\BukuKeuanganRepositoryInterface;
use App\Domains\Wilayah\BukuKeuangan\Services\BukuKeuanganScopeService;
use App\Domains\Wilayah\Services\ActiveBudgetYearContextService;

class GetScopedBukuKeuanganUseCase
{
    public function __construct(
        private readonly BukuKeuanganRepositoryInterface $bukuKeuanganRepository,
        private readonly BukuKeuanganScopeService $bukuKeuanganScopeService,
        private readonly ActiveBudgetYearContextService $activeBudgetYearContextService
    ) {}

    public function execute(int $id, string $level): BukuKeuangan
    {
        $bukuKeuangan = $this->bukuKeuanganRepository->find($id);
        $areaId = $this->bukuKeuanganScopeService->requireUserAreaId();
        $tahunAnggaran = $this->activeBudgetYearContextService->requireForAuthenticatedUser();

        return $this->bukuKeuanganScopeService->authorizeSameLevelAreaAndBudgetYear($bukuKeuangan, $level, $areaId, $tahunAnggaran);
    }
}
