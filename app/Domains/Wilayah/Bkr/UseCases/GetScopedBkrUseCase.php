<?php

namespace App\Domains\Wilayah\Bkr\UseCases;

use App\Domains\Wilayah\Bkr\Models\Bkr;
use App\Domains\Wilayah\Bkr\Repositories\BkrRepositoryInterface;
use App\Domains\Wilayah\Bkr\Services\BkrScopeService;

class GetScopedBkrUseCase
{
    public function __construct(
        private readonly BkrRepositoryInterface $bkrRepository,
        private readonly BkrScopeService $bkrScopeService
    ) {
    }

    public function execute(int $id, string $level): Bkr
    {
        $bkr = $this->bkrRepository->find($id);
        $areaId = $this->bkrScopeService->requireUserAreaId();

        return $this->bkrScopeService->authorizeSameLevelAndArea($bkr, $level, $areaId);
    }
}


