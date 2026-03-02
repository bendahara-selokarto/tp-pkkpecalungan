<?php

namespace App\Domains\Wilayah\Koperasi\UseCases;

use App\Domains\Wilayah\Koperasi\Repositories\KoperasiRepositoryInterface;
use App\Domains\Wilayah\Koperasi\Services\KoperasiScopeService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class ListScopedKoperasiUseCase
{
    public function __construct(
        private readonly KoperasiRepositoryInterface $koperasiRepository,
        private readonly KoperasiScopeService $koperasiScopeService
    ) {
    }

    public function execute(string $level, int $perPage): LengthAwarePaginator
    {
        $areaId = $this->koperasiScopeService->requireUserAreaId();

        return $this->koperasiRepository->paginateByLevelAndArea($level, $areaId, $perPage);
    }

    public function executeAll(string $level): Collection
    {
        $areaId = $this->koperasiScopeService->requireUserAreaId();

        return $this->koperasiRepository->getByLevelAndArea($level, $areaId);
    }
}