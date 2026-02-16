<?php

namespace App\Domains\Wilayah\Inventaris\UseCases;

use App\Domains\Wilayah\Inventaris\Repositories\InventarisRepository;
use App\Domains\Wilayah\Inventaris\Services\InventarisScopeService;

class ListScopedInventarisUseCase
{
    public function __construct(
        private readonly InventarisRepository $inventarisRepository,
        private readonly InventarisScopeService $inventarisScopeService
    ) {
    }

    public function execute(string $level)
    {
        $areaId = $this->inventarisScopeService->requireUserAreaId();

        return $this->inventarisRepository->getByLevelAndArea($level, $areaId);
    }
}
