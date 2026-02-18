<?php

namespace App\Domains\Wilayah\AnggotaPokja\UseCases;

use App\Domains\Wilayah\AnggotaPokja\Repositories\AnggotaPokjaRepositoryInterface;
use App\Domains\Wilayah\AnggotaPokja\Services\AnggotaPokjaScopeService;

class ListScopedAnggotaPokjaUseCase
{
    public function __construct(
        private readonly AnggotaPokjaRepositoryInterface $anggotaPokjaRepository,
        private readonly AnggotaPokjaScopeService $anggotaPokjaScopeService
    ) {
    }

    public function execute(string $level)
    {
        $areaId = $this->anggotaPokjaScopeService->requireUserAreaId();

        return $this->anggotaPokjaRepository->getByLevelAndArea($level, $areaId);
    }
}

