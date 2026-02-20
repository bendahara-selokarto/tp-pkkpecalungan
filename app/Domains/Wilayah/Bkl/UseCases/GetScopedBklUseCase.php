<?php

namespace App\Domains\Wilayah\Bkl\UseCases;

use App\Domains\Wilayah\Bkl\Models\Bkl;
use App\Domains\Wilayah\Bkl\Repositories\BklRepositoryInterface;
use App\Domains\Wilayah\Bkl\Services\BklScopeService;

class GetScopedBklUseCase
{
    public function __construct(
        private readonly BklRepositoryInterface $bklRepository,
        private readonly BklScopeService $bklScopeService
    ) {
    }

    public function execute(int $id, string $level): Bkl
    {
        $bkl = $this->bklRepository->find($id);
        $areaId = $this->bklScopeService->requireUserAreaId();

        return $this->bklScopeService->authorizeSameLevelAndArea($bkl, $level, $areaId);
    }
}

