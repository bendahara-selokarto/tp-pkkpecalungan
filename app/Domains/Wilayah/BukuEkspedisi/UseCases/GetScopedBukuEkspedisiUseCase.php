<?php

namespace App\Domains\Wilayah\BukuEkspedisi\UseCases;

use App\Domains\Wilayah\BukuEkspedisi\Models\BukuEkspedisi;
use App\Domains\Wilayah\BukuEkspedisi\Repositories\BukuEkspedisiRepositoryInterface;
use App\Domains\Wilayah\BukuEkspedisi\Services\BukuEkspedisiScopeService;
use App\Domains\Wilayah\Services\ActiveBudgetYearContextService;

class GetScopedBukuEkspedisiUseCase
{
    public function __construct(
        private readonly BukuEkspedisiRepositoryInterface $bukuEkspedisiRepository,
        private readonly BukuEkspedisiScopeService $bukuEkspedisiScopeService,
        private readonly ActiveBudgetYearContextService $activeBudgetYearContextService
    ) {
    }

    public function execute(int $id, string $level): BukuEkspedisi
    {
        $areaId = $this->bukuEkspedisiScopeService->requireUserAreaId();
        $tahunAnggaran = $this->activeBudgetYearContextService->requireForAuthenticatedUser();
        $bukuEkspedisi = $this->bukuEkspedisiRepository->find($id);

        return $this->bukuEkspedisiScopeService->authorizeSameLevelAreaAndBudgetYear($bukuEkspedisi, $level, $areaId, $tahunAnggaran);
    }
}
