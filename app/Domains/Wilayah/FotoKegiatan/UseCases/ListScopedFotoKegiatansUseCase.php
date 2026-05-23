<?php

namespace App\Domains\Wilayah\FotoKegiatan\UseCases;

use App\Domains\Wilayah\FotoKegiatan\Repositories\FotoKegiatanRepositoryInterface;
use App\Domains\Wilayah\FotoKegiatan\Services\FotoKegiatanScopeService;
use App\Domains\Wilayah\Services\ActiveBudgetYearContextService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ListScopedFotoKegiatansUseCase
{
    public function __construct(
        private readonly FotoKegiatanRepositoryInterface $fotoKegiatanRepository,
        private readonly FotoKegiatanScopeService $fotoKegiatanScopeService,
        private readonly ActiveBudgetYearContextService $activeBudgetYearContextService
    ) {
    }

    public function execute(string $level, int $perPage): LengthAwarePaginator
    {
        $user = auth()->user();
        $areaId = $this->fotoKegiatanScopeService->requireUserAreaId();
        $tahunAnggaran = $this->activeBudgetYearContextService->resolveForUser($user);
        
        $group = null;
        if ($this->fotoKegiatanScopeService->requiresGroupFilter($user)) {
            $groups = $this->fotoKegiatanScopeService->resolveGroupsForUser($user);
            $group = $groups[0] ?? null;
        }

        return $this->fotoKegiatanRepository->listScoped($level, $areaId, $tahunAnggaran, $perPage, $group);
    }
}
