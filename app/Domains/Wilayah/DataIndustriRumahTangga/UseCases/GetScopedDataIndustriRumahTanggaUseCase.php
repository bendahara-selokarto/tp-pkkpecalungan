<?php

namespace App\Domains\Wilayah\DataIndustriRumahTangga\UseCases;

use App\Domains\Wilayah\DataIndustriRumahTangga\Models\DataIndustriRumahTangga;
use App\Domains\Wilayah\DataIndustriRumahTangga\Repositories\DataIndustriRumahTanggaRepositoryInterface;
use App\Domains\Wilayah\DataIndustriRumahTangga\Services\DataIndustriRumahTanggaScopeService;
use App\Domains\Wilayah\Services\ActiveBudgetYearContextService;

class GetScopedDataIndustriRumahTanggaUseCase
{
    public function __construct(
        private readonly DataIndustriRumahTanggaRepositoryInterface $dataIndustriRumahTanggaRepository,
        private readonly DataIndustriRumahTanggaScopeService $dataIndustriRumahTanggaScopeService,
        private readonly ActiveBudgetYearContextService $activeBudgetYearContextService
    ) {}

    public function execute(int $id, string $level): DataIndustriRumahTangga
    {
        $dataIndustriRumahTangga = $this->dataIndustriRumahTanggaRepository->find($id);
        $areaId = $this->dataIndustriRumahTanggaScopeService->requireUserAreaId();
        $tahunAnggaran = $this->activeBudgetYearContextService->requireForAuthenticatedUser();

        return $this->dataIndustriRumahTanggaScopeService->authorizeSameLevelAreaAndBudgetYear($dataIndustriRumahTangga, $level, $areaId, $tahunAnggaran);
    }
}
