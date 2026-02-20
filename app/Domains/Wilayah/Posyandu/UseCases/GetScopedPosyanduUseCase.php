<?php

namespace App\Domains\Wilayah\Posyandu\UseCases;

use App\Domains\Wilayah\Posyandu\Models\Posyandu;
use App\Domains\Wilayah\Posyandu\Repositories\PosyanduRepositoryInterface;
use App\Domains\Wilayah\Posyandu\Services\PosyanduScopeService;

class GetScopedPosyanduUseCase
{
    public function __construct(
        private readonly PosyanduRepositoryInterface $posyanduRepository,
        private readonly PosyanduScopeService $posyanduScopeService
    ) {
    }

    public function execute(int $id, string $level): Posyandu
    {
        $posyandu = $this->posyanduRepository->find($id);
        $areaId = $this->posyanduScopeService->requireUserAreaId();

        return $this->posyanduScopeService->authorizeSameLevelAndArea($posyandu, $level, $areaId);
    }
}





