<?php

namespace App\Domains\Wilayah\LiterasiWarga\UseCases;

use App\Domains\Wilayah\LiterasiWarga\Models\LiterasiWarga;
use App\Domains\Wilayah\LiterasiWarga\Repositories\LiterasiWargaRepositoryInterface;
use App\Domains\Wilayah\LiterasiWarga\Services\LiterasiWargaScopeService;
use App\Domains\Wilayah\Services\ActiveBudgetYearContextService;

class GetScopedLiterasiWargaUseCase
{
    public function __construct(
        private readonly LiterasiWargaRepositoryInterface $literasiWargaRepository,
        private readonly LiterasiWargaScopeService $literasiWargaScopeService,
        private readonly ActiveBudgetYearContextService $activeBudgetYearContextService
    ) {
    }

    public function execute(int $id, string $level): LiterasiWarga
    {
        $literasiWarga = $this->literasiWargaRepository->find($id);
        $areaId = $this->literasiWargaScopeService->requireUserAreaId();
        $tahunAnggaran = $this->activeBudgetYearContextService->resolveForUser(auth()->user());

        return $this->literasiWargaScopeService->authorizeSameLevelAreaAndBudgetYear($literasiWarga, $level, $areaId, $tahunAnggaran);
    }
}
