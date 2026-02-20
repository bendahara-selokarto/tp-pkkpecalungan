<?php

namespace App\Domains\Wilayah\Bkl\UseCases;

use App\Domains\Wilayah\Bkl\Repositories\BklRepositoryInterface;
use App\Domains\Wilayah\Bkl\Services\BklScopeService;

class ListScopedBklUseCase
{
    public function __construct(
        private readonly BklRepositoryInterface $bklRepository,
        private readonly BklScopeService $bklScopeService
    ) {
    }

    public function execute(string $level)
    {
        $areaId = $this->bklScopeService->requireUserAreaId();

        return $this->bklRepository->getByLevelAndArea($level, $areaId);
    }
}

