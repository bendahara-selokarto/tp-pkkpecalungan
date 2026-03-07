<?php

namespace App\Domains\Wilayah\AnggotaPokja\UseCases;

use App\Domains\Wilayah\AnggotaPokja\Models\AnggotaPokja;
use App\Domains\Wilayah\AnggotaPokja\Repositories\AnggotaPokjaRepositoryInterface;
use App\Domains\Wilayah\AnggotaPokja\Services\AnggotaPokjaScopeService;
use App\Domains\Wilayah\Services\ActiveBudgetYearContextService;

class GetScopedAnggotaPokjaUseCase
{
    public function __construct(
        private readonly AnggotaPokjaRepositoryInterface $anggotaPokjaRepository,
        private readonly AnggotaPokjaScopeService $anggotaPokjaScopeService,
        private readonly ActiveBudgetYearContextService $activeBudgetYearContextService
    ) {}

    public function execute(int $id, string $level): AnggotaPokja
    {
        $anggotaPokja = $this->anggotaPokjaRepository->find($id);
        $areaId = $this->anggotaPokjaScopeService->requireUserAreaId();
        $tahunAnggaran = $this->activeBudgetYearContextService->requireForAuthenticatedUser();

        return $this->anggotaPokjaScopeService->authorizeSameLevelAreaAndBudgetYear($anggotaPokja, $level, $areaId, $tahunAnggaran);
    }
}
