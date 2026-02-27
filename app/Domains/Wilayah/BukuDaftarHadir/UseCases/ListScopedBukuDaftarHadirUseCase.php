<?php

namespace App\Domains\Wilayah\BukuDaftarHadir\UseCases;

use App\Domains\Wilayah\BukuDaftarHadir\Repositories\BukuDaftarHadirRepositoryInterface;
use App\Domains\Wilayah\BukuDaftarHadir\Services\BukuDaftarHadirScopeService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class ListScopedBukuDaftarHadirUseCase
{
    public function __construct(
        private readonly BukuDaftarHadirRepositoryInterface $bukuDaftarHadirRepository,
        private readonly BukuDaftarHadirScopeService $bukuDaftarHadirScopeService
    ) {
    }

    public function execute(string $level, int $perPage): LengthAwarePaginator
    {
        
        $areaId = $this->bukuDaftarHadirScopeService->requireUserAreaId();
        $creatorIdFilter = $this->bukuDaftarHadirScopeService->resolveCreatorIdFilterForList($level);

        return $this->bukuDaftarHadirRepository->paginateByLevelAndArea($level, $areaId, $perPage, $creatorIdFilter);
    }

    public function executeAll(string $level): Collection
    {
        
        $areaId = $this->bukuDaftarHadirScopeService->requireUserAreaId();
        $creatorIdFilter = $this->bukuDaftarHadirScopeService->resolveCreatorIdFilterForList($level);

        return $this->bukuDaftarHadirRepository->getByLevelAndArea($level, $areaId, $creatorIdFilter);
    }
}


