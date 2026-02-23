<?php

namespace App\Domains\Wilayah\BukuKeuangan\UseCases;

use App\Domains\Wilayah\BukuKeuangan\Models\BukuKeuangan;
use App\Domains\Wilayah\BukuKeuangan\Repositories\BukuKeuanganRepositoryInterface;
use App\Domains\Wilayah\BukuKeuangan\Services\BukuKeuanganScopeService;

class GetScopedBukuKeuanganUseCase
{
    public function __construct(
        private readonly BukuKeuanganRepositoryInterface $bukuKeuanganRepository,
        private readonly BukuKeuanganScopeService $bukuKeuanganScopeService
    ) {
    }

    public function execute(int $id, string $level): BukuKeuangan
    {
        $bukuKeuangan = $this->bukuKeuanganRepository->find($id);
        $areaId = $this->bukuKeuanganScopeService->requireUserAreaId();

        return $this->bukuKeuanganScopeService->authorizeSameLevelAndArea($bukuKeuangan, $level, $areaId);
    }
}
