<?php

namespace App\Domains\Wilayah\KejarPaket\UseCases;

use App\Domains\Wilayah\KejarPaket\Models\KejarPaket;
use App\Domains\Wilayah\KejarPaket\Repositories\KejarPaketRepositoryInterface;
use App\Domains\Wilayah\KejarPaket\Services\KejarPaketScopeService;

class GetScopedKejarPaketUseCase
{
    public function __construct(
        private readonly KejarPaketRepositoryInterface $kejarPaketRepository,
        private readonly KejarPaketScopeService $kejarPaketScopeService
    ) {
    }

    public function execute(int $id, string $level): KejarPaket
    {
        $kejarPaket = $this->kejarPaketRepository->find($id);
        $areaId = $this->kejarPaketScopeService->requireUserAreaId();

        return $this->kejarPaketScopeService->authorizeSameLevelAndArea($kejarPaket, $level, $areaId);
    }
}





