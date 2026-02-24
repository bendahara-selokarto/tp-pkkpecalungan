<?php

namespace App\Domains\Wilayah\Paar\UseCases;

use App\Domains\Wilayah\Paar\Models\Paar;
use App\Domains\Wilayah\Paar\Repositories\PaarRepositoryInterface;
use App\Domains\Wilayah\Paar\Services\PaarScopeService;

class GetScopedPaarUseCase
{
    public function __construct(
        private readonly PaarRepositoryInterface $paarRepository,
        private readonly PaarScopeService $paarScopeService
    ) {
    }

    public function execute(int $id, string $level): Paar
    {
        $paar = $this->paarRepository->find($id);
        $areaId = $this->paarScopeService->requireUserAreaId();

        return $this->paarScopeService->authorizeSameLevelAndArea($paar, $level, $areaId);
    }
}