<?php

namespace App\Domains\Wilayah\KaderKhusus\UseCases;

use App\Domains\Wilayah\KaderKhusus\Models\KaderKhusus;
use App\Domains\Wilayah\KaderKhusus\Repositories\KaderKhususRepositoryInterface;
use App\Domains\Wilayah\KaderKhusus\Services\KaderKhususScopeService;
use App\Domains\Wilayah\Services\ActiveBudgetYearContextService;

class GetScopedKaderKhususUseCase
{
    public function __construct(
        private readonly KaderKhususRepositoryInterface $kaderKhususRepository,
        private readonly KaderKhususScopeService $kaderKhususScopeService,
        private readonly ActiveBudgetYearContextService $activeBudgetYearContextService
    ) {
    }

    public function execute(int $id, string $level): KaderKhusus
    {
        $kaderKhusus = $this->kaderKhususRepository->find($id);
        $areaId = $this->kaderKhususScopeService->requireUserAreaId();
        $tahunAnggaran = $this->activeBudgetYearContextService->requireForAuthenticatedUser();

        return $this->kaderKhususScopeService->authorizeSameLevelAreaAndBudgetYear($kaderKhusus, $level, $areaId, $tahunAnggaran);
    }
}
