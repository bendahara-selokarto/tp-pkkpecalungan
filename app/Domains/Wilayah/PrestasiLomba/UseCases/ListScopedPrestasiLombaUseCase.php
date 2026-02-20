<?php

namespace App\Domains\Wilayah\PrestasiLomba\UseCases;

use App\Domains\Wilayah\PrestasiLomba\Repositories\PrestasiLombaRepositoryInterface;
use App\Domains\Wilayah\PrestasiLomba\Services\PrestasiLombaScopeService;

class ListScopedPrestasiLombaUseCase
{
    public function __construct(
        private readonly PrestasiLombaRepositoryInterface $prestasiLombaRepository,
        private readonly PrestasiLombaScopeService $prestasiLombaScopeService
    ) {
    }

    public function execute(string $level)
    {
        $areaId = $this->prestasiLombaScopeService->requireUserAreaId();

        return $this->prestasiLombaRepository->getByLevelAndArea($level, $areaId);
    }
}
