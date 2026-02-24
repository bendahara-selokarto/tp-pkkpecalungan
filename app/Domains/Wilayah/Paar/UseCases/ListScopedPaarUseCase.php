<?php

namespace App\Domains\Wilayah\Paar\UseCases;

use App\Domains\Wilayah\Paar\Repositories\PaarRepositoryInterface;
use App\Domains\Wilayah\Paar\Services\PaarScopeService;

class ListScopedPaarUseCase
{
    public function __construct(
        private readonly PaarRepositoryInterface $paarRepository,
        private readonly PaarScopeService $paarScopeService
    ) {
    }

    public function execute(string $level)
    {
        $areaId = $this->paarScopeService->requireUserAreaId();

        return $this->paarRepository->getByLevelAndArea($level, $areaId);
    }
}