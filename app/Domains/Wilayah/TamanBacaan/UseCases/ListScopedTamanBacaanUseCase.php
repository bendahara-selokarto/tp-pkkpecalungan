<?php

namespace App\Domains\Wilayah\TamanBacaan\UseCases;

use App\Domains\Wilayah\TamanBacaan\Repositories\TamanBacaanRepositoryInterface;
use App\Domains\Wilayah\TamanBacaan\Services\TamanBacaanScopeService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class ListScopedTamanBacaanUseCase
{
    public function __construct(
        private readonly TamanBacaanRepositoryInterface $tamanBacaanRepository,
        private readonly TamanBacaanScopeService $tamanBacaanScopeService
    ) {
    }

    public function execute(string $level, int $perPage): LengthAwarePaginator
    {
        $areaId = $this->tamanBacaanScopeService->requireUserAreaId();

        return $this->tamanBacaanRepository->paginateByLevelAndArea($level, $areaId, $perPage);
    }

    public function executeAll(string $level): Collection
    {
        $areaId = $this->tamanBacaanScopeService->requireUserAreaId();

        return $this->tamanBacaanRepository->getByLevelAndArea($level, $areaId);
    }
}

