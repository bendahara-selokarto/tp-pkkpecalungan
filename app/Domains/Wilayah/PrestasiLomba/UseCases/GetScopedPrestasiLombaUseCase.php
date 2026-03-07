<?php

namespace App\Domains\Wilayah\PrestasiLomba\UseCases;

use App\Domains\Wilayah\PrestasiLomba\Models\PrestasiLomba;
use App\Domains\Wilayah\PrestasiLomba\Repositories\PrestasiLombaRepositoryInterface;
use App\Domains\Wilayah\PrestasiLomba\Services\PrestasiLombaScopeService;
use App\Domains\Wilayah\Services\ActiveBudgetYearContextService;

class GetScopedPrestasiLombaUseCase
{
    public function __construct(
        private readonly PrestasiLombaRepositoryInterface $prestasiLombaRepository,
        private readonly PrestasiLombaScopeService $prestasiLombaScopeService,
        private readonly ActiveBudgetYearContextService $activeBudgetYearContextService
    ) {}

    public function execute(int $id, string $level): PrestasiLomba
    {
        $prestasiLomba = $this->prestasiLombaRepository->find($id);
        $areaId = $this->prestasiLombaScopeService->requireUserAreaId();
        $tahunAnggaran = $this->activeBudgetYearContextService->requireForAuthenticatedUser();

        return $this->prestasiLombaScopeService->authorizeSameLevelAreaAndBudgetYear($prestasiLomba, $level, $areaId, $tahunAnggaran);
    }
}
