<?php

namespace App\Domains\Wilayah\BukuKeuangan\UseCases;

use App\Domains\Wilayah\BukuKeuangan\Repositories\BukuKeuanganRepositoryInterface;
use App\Domains\Wilayah\BukuKeuangan\Services\BukuKeuanganScopeService;

class ListScopedBukuKeuanganUseCase
{
    public function __construct(
        private readonly BukuKeuanganRepositoryInterface $bukuKeuanganRepository,
        private readonly BukuKeuanganScopeService $bukuKeuanganScopeService
    ) {
    }

    public function execute(string $level)
    {
        $areaId = $this->bukuKeuanganScopeService->requireUserAreaId();

        return $this->bukuKeuanganRepository->getByLevelAndArea($level, $areaId);
    }
}
