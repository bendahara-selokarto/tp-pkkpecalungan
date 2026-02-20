<?php

namespace App\Domains\Wilayah\Bkr\UseCases;

use App\Domains\Wilayah\Bkr\Repositories\BkrRepositoryInterface;
use App\Domains\Wilayah\Bkr\Services\BkrScopeService;

class ListScopedBkrUseCase
{
    public function __construct(
        private readonly BkrRepositoryInterface $bkrRepository,
        private readonly BkrScopeService $bkrScopeService
    ) {
    }

    public function execute(string $level)
    {
        $areaId = $this->bkrScopeService->requireUserAreaId();

        return $this->bkrRepository->getByLevelAndArea($level, $areaId);
    }
}


