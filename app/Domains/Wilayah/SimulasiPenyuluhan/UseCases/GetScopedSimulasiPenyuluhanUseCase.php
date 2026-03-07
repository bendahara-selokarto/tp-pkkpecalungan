<?php

namespace App\Domains\Wilayah\SimulasiPenyuluhan\UseCases;

use App\Domains\Wilayah\Services\ActiveBudgetYearContextService;
use App\Domains\Wilayah\SimulasiPenyuluhan\Models\SimulasiPenyuluhan;
use App\Domains\Wilayah\SimulasiPenyuluhan\Repositories\SimulasiPenyuluhanRepositoryInterface;
use App\Domains\Wilayah\SimulasiPenyuluhan\Services\SimulasiPenyuluhanScopeService;

class GetScopedSimulasiPenyuluhanUseCase
{
    public function __construct(
        private readonly SimulasiPenyuluhanRepositoryInterface $simulasiPenyuluhanRepository,
        private readonly SimulasiPenyuluhanScopeService $simulasiPenyuluhanScopeService,
        private readonly ActiveBudgetYearContextService $activeBudgetYearContextService
    ) {}

    public function execute(int $id, string $level): SimulasiPenyuluhan
    {
        $simulasiPenyuluhan = $this->simulasiPenyuluhanRepository->find($id);
        $areaId = $this->simulasiPenyuluhanScopeService->requireUserAreaId();
        $tahunAnggaran = $this->activeBudgetYearContextService->requireForAuthenticatedUser();

        return $this->simulasiPenyuluhanScopeService->authorizeSameLevelAreaAndBudgetYear($simulasiPenyuluhan, $level, $areaId, $tahunAnggaran);
    }
}
