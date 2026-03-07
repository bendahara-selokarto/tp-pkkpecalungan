<?php

namespace App\Domains\Wilayah\AgendaSurat\UseCases;

use App\Domains\Wilayah\AgendaSurat\Models\AgendaSurat;
use App\Domains\Wilayah\AgendaSurat\Repositories\AgendaSuratRepositoryInterface;
use App\Domains\Wilayah\AgendaSurat\Services\AgendaSuratScopeService;
use App\Domains\Wilayah\Services\ActiveBudgetYearContextService;

class GetScopedAgendaSuratUseCase
{
    public function __construct(
        private readonly AgendaSuratRepositoryInterface $agendaSuratRepository,
        private readonly AgendaSuratScopeService $agendaSuratScopeService,
        private readonly ActiveBudgetYearContextService $activeBudgetYearContextService
    ) {
    }

    public function execute(int $id, string $level): AgendaSurat
    {
        $agendaSurat = $this->agendaSuratRepository->find($id);
        $areaId = $this->agendaSuratScopeService->requireUserAreaId();
        $tahunAnggaran = $this->activeBudgetYearContextService->requireForAuthenticatedUser();

        return $this->agendaSuratScopeService->authorizeSameLevelAreaAndBudgetYear($agendaSurat, $level, $areaId, $tahunAnggaran);
    }
}
