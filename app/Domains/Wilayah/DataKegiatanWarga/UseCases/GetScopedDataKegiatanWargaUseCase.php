<?php

namespace App\Domains\Wilayah\DataKegiatanWarga\UseCases;

use App\Domains\Wilayah\DataKegiatanWarga\Models\DataKegiatanWarga;
use App\Domains\Wilayah\DataKegiatanWarga\Repositories\DataKegiatanWargaRepositoryInterface;
use App\Domains\Wilayah\DataKegiatanWarga\Services\DataKegiatanWargaScopeService;
use App\Domains\Wilayah\Services\ActiveBudgetYearContextService;

class GetScopedDataKegiatanWargaUseCase
{
    public function __construct(
        private readonly DataKegiatanWargaRepositoryInterface $dataKegiatanWargaRepository,
        private readonly DataKegiatanWargaScopeService $dataKegiatanWargaScopeService,
        private readonly ActiveBudgetYearContextService $activeBudgetYearContextService
    ) {}

    public function execute(int $id, string $level): DataKegiatanWarga
    {
        $dataKegiatanWarga = $this->dataKegiatanWargaRepository->find($id);
        $areaId = $this->dataKegiatanWargaScopeService->requireUserAreaId();
        $tahunAnggaran = $this->activeBudgetYearContextService->requireForAuthenticatedUser();

        return $this->dataKegiatanWargaScopeService->authorizeSameLevelAreaAndBudgetYear($dataKegiatanWarga, $level, $areaId, $tahunAnggaran);
    }
}
