<?php

namespace App\Domains\Wilayah\DataWarga\UseCases;

use App\Domains\Wilayah\DataWarga\Models\DataWarga;
use App\Domains\Wilayah\DataWarga\Repositories\DataWargaRepositoryInterface;
use App\Domains\Wilayah\DataWarga\Services\DataWargaScopeService;
use App\Domains\Wilayah\Services\ActiveBudgetYearContextService;

class GetScopedDataWargaUseCase
{
    public function __construct(
        private readonly DataWargaRepositoryInterface $dataWargaRepository,
        private readonly DataWargaScopeService $dataWargaScopeService,
        private readonly ActiveBudgetYearContextService $activeBudgetYearContextService
    ) {}

    public function execute(int $id, string $level): DataWarga
    {
        $dataWarga = $this->dataWargaRepository->find($id);
        $areaId = $this->dataWargaScopeService->requireUserAreaId();
        $tahunAnggaran = $this->activeBudgetYearContextService->requireForAuthenticatedUser();

        return $this->dataWargaScopeService->authorizeSameLevelAreaAndBudgetYear($dataWarga, $level, $areaId, $tahunAnggaran);
    }
}
