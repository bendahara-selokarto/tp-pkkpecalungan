<?php

namespace App\Domains\Wilayah\DataKeluarga\UseCases;

use App\Domains\Wilayah\DataKeluarga\Repositories\DataKeluargaRepositoryInterface;
use App\Domains\Wilayah\DataKeluarga\Services\DataKeluargaScopeService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class ListScopedDataKeluargaUseCase
{
    public function __construct(
        private readonly DataKeluargaRepositoryInterface $dataKeluargaRepository,
        private readonly DataKeluargaScopeService $dataKeluargaScopeService
    ) {
    }

    public function execute(string $level, int $perPage): LengthAwarePaginator
    {
        $areaId = $this->dataKeluargaScopeService->requireUserAreaId();

        return $this->dataKeluargaRepository->paginateByLevelAndArea($level, $areaId, $perPage);
    }

    public function executeAll(string $level): Collection
    {
        $areaId = $this->dataKeluargaScopeService->requireUserAreaId();

        return $this->dataKeluargaRepository->getByLevelAndArea($level, $areaId);
    }
}
