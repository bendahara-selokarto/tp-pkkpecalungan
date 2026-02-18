<?php

namespace App\Domains\Wilayah\AnggotaPokja\UseCases;

use App\Domains\Wilayah\AnggotaPokja\Repositories\AnggotaPokjaRepository;
use App\Domains\Wilayah\AnggotaPokja\Services\AnggotaPokjaScopeService;

class ListScopedAnggotaPokjaUseCase
{
    public function __construct(
        private readonly AnggotaPokjaRepository $anggotaPokjaRepository,
        private readonly AnggotaPokjaScopeService $anggotaPokjaScopeService
    ) {
    }

    public function execute(string $level)
    {
        $areaId = $this->anggotaPokjaScopeService->requireUserAreaId();

        return $this->anggotaPokjaRepository->getByLevelAndArea($level, $areaId);
    }
}
