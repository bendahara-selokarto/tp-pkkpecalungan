<?php

namespace App\Domains\Wilayah\TamanBacaan\UseCases;

use App\Domains\Wilayah\TamanBacaan\Models\TamanBacaan;
use App\Domains\Wilayah\TamanBacaan\Repositories\TamanBacaanRepositoryInterface;
use App\Domains\Wilayah\TamanBacaan\Services\TamanBacaanScopeService;

class GetScopedTamanBacaanUseCase
{
    public function __construct(
        private readonly TamanBacaanRepositoryInterface $tamanBacaanRepository,
        private readonly TamanBacaanScopeService $tamanBacaanScopeService
    ) {
    }

    public function execute(int $id, string $level): TamanBacaan
    {
        $tamanBacaan = $this->tamanBacaanRepository->find($id);
        $areaId = $this->tamanBacaanScopeService->requireUserAreaId();

        return $this->tamanBacaanScopeService->authorizeSameLevelAndArea($tamanBacaan, $level, $areaId);
    }
}


