<?php

namespace App\Domains\Wilayah\AnggotaTimPenggerak\UseCases;

use App\Domains\Wilayah\AnggotaTimPenggerak\Repositories\AnggotaTimPenggerakRepositoryInterface;
use App\Domains\Wilayah\AnggotaTimPenggerak\Services\AnggotaTimPenggerakScopeService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class ListScopedAnggotaTimPenggerakUseCase
{
    public function __construct(
        private readonly AnggotaTimPenggerakRepositoryInterface $anggotaTimPenggerakRepository,
        private readonly AnggotaTimPenggerakScopeService $anggotaTimPenggerakScopeService
    ) {
    }

    public function execute(string $level, int $perPage): LengthAwarePaginator
    {
        
        $areaId = $this->anggotaTimPenggerakScopeService->requireUserAreaId();
        $creatorIdFilter = $this->anggotaTimPenggerakScopeService->resolveCreatorIdFilterForList($level);

        return $this->anggotaTimPenggerakRepository->paginateByLevelAndArea($level, $areaId, $perPage, $creatorIdFilter);
    }

    public function executeAll(string $level): Collection
    {
        
        $areaId = $this->anggotaTimPenggerakScopeService->requireUserAreaId();
        $creatorIdFilter = $this->anggotaTimPenggerakScopeService->resolveCreatorIdFilterForList($level);

        return $this->anggotaTimPenggerakRepository->getByLevelAndArea($level, $areaId, $creatorIdFilter);
    }
}



