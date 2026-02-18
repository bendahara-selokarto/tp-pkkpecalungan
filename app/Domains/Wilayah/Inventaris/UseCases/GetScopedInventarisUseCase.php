<?php

namespace App\Domains\Wilayah\Inventaris\UseCases;

use App\Domains\Wilayah\Inventaris\Models\Inventaris;
use App\Domains\Wilayah\Inventaris\Repositories\InventarisRepositoryInterface;
use App\Domains\Wilayah\Inventaris\Services\InventarisScopeService;

class GetScopedInventarisUseCase
{
    public function __construct(
        private readonly InventarisRepositoryInterface $inventarisRepository,
        private readonly InventarisScopeService $inventarisScopeService
    ) {
    }

    public function execute(int $id, string $level): Inventaris
    {
        $inventaris = $this->inventarisRepository->find($id);
        $areaId = $this->inventarisScopeService->requireUserAreaId();

        return $this->inventarisScopeService->authorizeSameLevelAndArea($inventaris, $level, $areaId);
    }
}

