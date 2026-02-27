<?php

namespace App\Domains\Wilayah\BukuTamu\UseCases;

use App\Domains\Wilayah\BukuTamu\Repositories\BukuTamuRepositoryInterface;
use App\Domains\Wilayah\BukuTamu\Services\BukuTamuScopeService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class ListScopedBukuTamuUseCase
{
    public function __construct(
        private readonly BukuTamuRepositoryInterface $bukuTamuRepository,
        private readonly BukuTamuScopeService $bukuTamuScopeService
    ) {
    }

    public function execute(string $level, int $perPage): LengthAwarePaginator
    {
        $areaId = $this->bukuTamuScopeService->requireUserAreaId();

        return $this->bukuTamuRepository->paginateByLevelAndArea($level, $areaId, $perPage);
    }

    public function executeAll(string $level): Collection
    {
        $areaId = $this->bukuTamuScopeService->requireUserAreaId();

        return $this->bukuTamuRepository->getByLevelAndArea($level, $areaId);
    }
}
