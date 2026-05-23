<?php

namespace App\Domains\Wilayah\BukuKonsultasi\UseCases;

use App\Domains\Wilayah\BukuKonsultasi\Repositories\BukuKonsultasiRepositoryInterface;
use App\Domains\Wilayah\BukuKonsultasi\Services\BukuKonsultasiScopeService;
use App\Domains\Wilayah\Services\ActiveBudgetYearContextService;
use App\Domains\Wilayah\Services\UserAreaContextService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ListScopedBukuKonsultasiesUseCase
{
    public function __construct(
        private readonly BukuKonsultasiRepositoryInterface $bukuKonsultasiRepository,
        private readonly BukuKonsultasiScopeService $bukuKonsultasiScopeService,
        private readonly UserAreaContextService $userAreaContextService,
        private readonly ActiveBudgetYearContextService $activeBudgetYearContextService
    ) {
    }

    public function execute(string $level, int $perPage): LengthAwarePaginator
    {
        $user = auth()->user();
        $areaId = $this->userAreaContextService->requireUserAreaId($user);
        $tahunAnggaran = $this->activeBudgetYearContextService->resolveForUser($user);

        $group = null;
        if ($this->bukuKonsultasiScopeService->requiresGroupFilter($user)) {
            $groups = $this->bukuKonsultasiScopeService->resolveGroupsForUser($user);
            $group = $groups[0] ?? null;
        }

        return $this->bukuKonsultasiRepository->listScoped($level, $areaId, $tahunAnggaran, $perPage, $group);
    }
}
