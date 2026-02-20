<?php

namespace App\Domains\Wilayah\Posyandu\UseCases;

use App\Domains\Wilayah\Posyandu\Repositories\PosyanduRepositoryInterface;
use App\Domains\Wilayah\Posyandu\Services\PosyanduScopeService;

class ListScopedPosyanduUseCase
{
    public function __construct(
        private readonly PosyanduRepositoryInterface $posyanduRepository,
        private readonly PosyanduScopeService $posyanduScopeService
    ) {
    }

    public function execute(string $level)
    {
        $areaId = $this->posyanduScopeService->requireUserAreaId();

        return $this->posyanduRepository->getByLevelAndArea($level, $areaId);
    }
}





