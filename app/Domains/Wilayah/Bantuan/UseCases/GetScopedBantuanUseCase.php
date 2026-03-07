<?php

namespace App\Domains\Wilayah\Bantuan\UseCases;

use App\Domains\Wilayah\Bantuan\Models\Bantuan;
use App\Domains\Wilayah\Bantuan\Repositories\BantuanRepositoryInterface;
use App\Domains\Wilayah\Bantuan\Services\BantuanScopeService;
use App\Domains\Wilayah\Services\ActiveBudgetYearContextService;

class GetScopedBantuanUseCase
{
    public function __construct(
        private readonly BantuanRepositoryInterface $bantuanRepository,
        private readonly BantuanScopeService $bantuanScopeService,
        private readonly ActiveBudgetYearContextService $activeBudgetYearContextService
    ) {}

    public function execute(int $id, string $level): Bantuan
    {
        $bantuan = $this->bantuanRepository->find($id);
        $areaId = $this->bantuanScopeService->requireUserAreaId();
        $tahunAnggaran = $this->activeBudgetYearContextService->requireForAuthenticatedUser();

        return $this->bantuanScopeService->authorizeSameLevelAreaAndBudgetYear($bantuan, $level, $areaId, $tahunAnggaran);
    }
}
