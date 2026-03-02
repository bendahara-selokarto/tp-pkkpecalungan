<?php

namespace App\Domains\Wilayah\BukuNotulenRapat\UseCases;

use App\Domains\Wilayah\BukuNotulenRapat\Models\BukuNotulenRapat;
use App\Domains\Wilayah\BukuNotulenRapat\Repositories\BukuNotulenRapatRepositoryInterface;
use App\Domains\Wilayah\BukuNotulenRapat\Services\BukuNotulenRapatScopeService;

class GetScopedBukuNotulenRapatUseCase
{
    public function __construct(
        private readonly BukuNotulenRapatRepositoryInterface $bukuNotulenRapatRepository,
        private readonly BukuNotulenRapatScopeService $bukuNotulenRapatScopeService
    ) {
    }

    public function execute(int $id, string $level): BukuNotulenRapat
    {
        $item = $this->bukuNotulenRapatRepository->find($id);
        $areaId = $this->bukuNotulenRapatScopeService->requireUserAreaId();

        return $this->bukuNotulenRapatScopeService->authorizeSameLevelAndArea($item, $level, $areaId);
    }
}
