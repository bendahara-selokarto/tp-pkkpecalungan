<?php

namespace App\Domains\Wilayah\WarungPkk\UseCases;

use App\Domains\Wilayah\WarungPkk\Repositories\WarungPkkRepositoryInterface;
use App\Domains\Wilayah\WarungPkk\Services\WarungPkkScopeService;

class ListScopedWarungPkkUseCase
{
    public function __construct(
        private readonly WarungPkkRepositoryInterface $warungPkkRepository,
        private readonly WarungPkkScopeService $warungPkkScopeService
    ) {
    }

    public function execute(string $level)
    {
        $areaId = $this->warungPkkScopeService->requireUserAreaId();

        return $this->warungPkkRepository->getByLevelAndArea($level, $areaId);
    }
}
