<?php

namespace App\Domains\Wilayah\CatatanKeluarga\UseCases;

use App\Domains\Wilayah\CatatanKeluarga\Repositories\CatatanKeluargaRepositoryInterface;
use App\Domains\Wilayah\CatatanKeluarga\Services\CatatanKeluargaScopeService;
use Illuminate\Support\Collection;

class ListScopedRekapIbuHamilPkkRwUseCase
{
    public function __construct(
        private readonly CatatanKeluargaRepositoryInterface $catatanKeluargaRepository,
        private readonly CatatanKeluargaScopeService $catatanKeluargaScopeService
    ) {
    }

    public function execute(string $level): Collection
    {
        $areaId = $this->catatanKeluargaScopeService->requireUserAreaId();

        return $this->catatanKeluargaRepository->getRekapIbuHamilPkkRwByLevelAndArea($level, $areaId);
    }
}
