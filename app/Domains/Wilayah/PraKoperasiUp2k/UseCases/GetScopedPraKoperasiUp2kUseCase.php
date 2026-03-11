<?php

namespace App\Domains\Wilayah\PraKoperasiUp2k\UseCases;

use App\Domains\Wilayah\PraKoperasiUp2k\Models\PraKoperasiUp2k;
use App\Domains\Wilayah\PraKoperasiUp2k\Repositories\PraKoperasiUp2kRepositoryInterface;
use App\Domains\Wilayah\PraKoperasiUp2k\Services\PraKoperasiUp2kScopeService;
use App\Domains\Wilayah\Services\ActiveBudgetYearContextService;

class GetScopedPraKoperasiUp2kUseCase
{
    public function __construct(
        private readonly PraKoperasiUp2kRepositoryInterface $praKoperasiUp2kRepository,
        private readonly PraKoperasiUp2kScopeService $praKoperasiUp2kScopeService,
        private readonly ActiveBudgetYearContextService $activeBudgetYearContextService
    ) {
    }

    public function execute(int $id, string $level): PraKoperasiUp2k
    {
        $praKoperasiUp2k = $this->praKoperasiUp2kRepository->find($id);
        $areaId = $this->praKoperasiUp2kScopeService->requireUserAreaId();
        $tahunAnggaran = $this->activeBudgetYearContextService->resolveForUser(auth()->user());

        return $this->praKoperasiUp2kScopeService->authorizeSameLevelAreaAndBudgetYear($praKoperasiUp2k, $level, $areaId, $tahunAnggaran);
    }
}
