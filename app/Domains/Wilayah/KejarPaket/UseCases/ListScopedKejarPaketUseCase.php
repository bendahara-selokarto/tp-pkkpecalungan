<?php

namespace App\Domains\Wilayah\KejarPaket\UseCases;

use App\Domains\Wilayah\KejarPaket\Repositories\KejarPaketRepositoryInterface;
use App\Domains\Wilayah\KejarPaket\Services\KejarPaketScopeService;

class ListScopedKejarPaketUseCase
{
    public function __construct(
        private readonly KejarPaketRepositoryInterface $kejarPaketRepository,
        private readonly KejarPaketScopeService $kejarPaketScopeService
    ) {
    }

    public function execute(string $level)
    {
        $areaId = $this->kejarPaketScopeService->requireUserAreaId();

        return $this->kejarPaketRepository->getByLevelAndArea($level, $areaId);
    }
}





