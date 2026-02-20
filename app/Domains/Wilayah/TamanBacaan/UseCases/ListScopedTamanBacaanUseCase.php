<?php

namespace App\Domains\Wilayah\TamanBacaan\UseCases;

use App\Domains\Wilayah\TamanBacaan\Repositories\TamanBacaanRepositoryInterface;
use App\Domains\Wilayah\TamanBacaan\Services\TamanBacaanScopeService;

class ListScopedTamanBacaanUseCase
{
    public function __construct(
        private readonly TamanBacaanRepositoryInterface $tamanBacaanRepository,
        private readonly TamanBacaanScopeService $tamanBacaanScopeService
    ) {
    }

    public function execute(string $level)
    {
        $areaId = $this->tamanBacaanScopeService->requireUserAreaId();

        return $this->tamanBacaanRepository->getByLevelAndArea($level, $areaId);
    }
}


