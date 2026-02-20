<?php

namespace App\Domains\Wilayah\WarungPkk\UseCases;

use App\Domains\Wilayah\WarungPkk\Models\WarungPkk;
use App\Domains\Wilayah\WarungPkk\Repositories\WarungPkkRepositoryInterface;
use App\Domains\Wilayah\WarungPkk\Services\WarungPkkScopeService;

class GetScopedWarungPkkUseCase
{
    public function __construct(
        private readonly WarungPkkRepositoryInterface $warungPkkRepository,
        private readonly WarungPkkScopeService $warungPkkScopeService
    ) {
    }

    public function execute(int $id, string $level): WarungPkk
    {
        $warungPkk = $this->warungPkkRepository->find($id);
        $areaId = $this->warungPkkScopeService->requireUserAreaId();

        return $this->warungPkkScopeService->authorizeSameLevelAndArea($warungPkk, $level, $areaId);
    }
}
