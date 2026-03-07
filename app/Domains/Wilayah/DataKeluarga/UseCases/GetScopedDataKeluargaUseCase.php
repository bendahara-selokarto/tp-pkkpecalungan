<?php

namespace App\Domains\Wilayah\DataKeluarga\UseCases;

use App\Domains\Wilayah\DataKeluarga\Models\DataKeluarga;
use App\Domains\Wilayah\DataKeluarga\Repositories\DataKeluargaRepositoryInterface;
use App\Domains\Wilayah\DataKeluarga\Services\DataKeluargaScopeService;
use App\Domains\Wilayah\Services\ActiveBudgetYearContextService;

class GetScopedDataKeluargaUseCase
{
    public function __construct(
        private readonly DataKeluargaRepositoryInterface $dataKeluargaRepository,
        private readonly DataKeluargaScopeService $dataKeluargaScopeService,
        private readonly ActiveBudgetYearContextService $activeBudgetYearContextService
    ) {}

    public function execute(int $id, string $level): DataKeluarga
    {
        $dataKeluarga = $this->dataKeluargaRepository->find($id);
        $areaId = $this->dataKeluargaScopeService->requireUserAreaId();
        $tahunAnggaran = $this->activeBudgetYearContextService->requireForAuthenticatedUser();

        return $this->dataKeluargaScopeService->authorizeSameLevelAreaAndBudgetYear($dataKeluarga, $level, $areaId, $tahunAnggaran);
    }
}
