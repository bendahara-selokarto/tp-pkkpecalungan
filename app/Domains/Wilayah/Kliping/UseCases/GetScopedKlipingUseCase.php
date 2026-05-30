<?php

namespace App\Domains\Wilayah\Kliping\UseCases;

use App\Domains\Wilayah\Kliping\Models\Kliping;
use App\Domains\Wilayah\Kliping\Repositories\KlipingRepositoryInterface;
use App\Domains\Wilayah\Kliping\Services\KlipingScopeService;
use App\Domains\Wilayah\Services\ActiveBudgetYearContextService;

class GetScopedKlipingUseCase
{
    public function __construct(
        private readonly KlipingRepositoryInterface $klipingRepository,
        private readonly KlipingScopeService $klipingScopeService,
        private readonly ActiveBudgetYearContextService $activeBudgetYearContextService
    ) {
    }

    public function execute(int $id, string $level): Kliping
    {
        $item = $this->klipingRepository->find($id);
        $areaId = $this->klipingScopeService->requireUserAreaId();
        $tahunAnggaran = $this->activeBudgetYearContextService->requireForAuthenticatedUser();

        return $this->klipingScopeService->authorizeSameLevelAreaAndBudgetYear($item, $level, $areaId, $tahunAnggaran);
    }
}
