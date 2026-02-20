<?php

namespace App\Domains\Wilayah\PrestasiLomba\UseCases;

use App\Domains\Wilayah\PrestasiLomba\Models\PrestasiLomba;
use App\Domains\Wilayah\PrestasiLomba\Repositories\PrestasiLombaRepositoryInterface;
use App\Domains\Wilayah\PrestasiLomba\Services\PrestasiLombaScopeService;

class GetScopedPrestasiLombaUseCase
{
    public function __construct(
        private readonly PrestasiLombaRepositoryInterface $prestasiLombaRepository,
        private readonly PrestasiLombaScopeService $prestasiLombaScopeService
    ) {
    }

    public function execute(int $id, string $level): PrestasiLomba
    {
        $prestasiLomba = $this->prestasiLombaRepository->find($id);
        $areaId = $this->prestasiLombaScopeService->requireUserAreaId();

        return $this->prestasiLombaScopeService->authorizeSameLevelAndArea($prestasiLomba, $level, $areaId);
    }
}
