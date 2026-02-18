<?php

namespace App\Domains\Wilayah\AnggotaPokja\UseCases;

use App\Domains\Wilayah\AnggotaPokja\Models\AnggotaPokja;
use App\Domains\Wilayah\AnggotaPokja\Repositories\AnggotaPokjaRepositoryInterface;
use App\Domains\Wilayah\AnggotaPokja\Services\AnggotaPokjaScopeService;

class GetScopedAnggotaPokjaUseCase
{
    public function __construct(
        private readonly AnggotaPokjaRepositoryInterface $anggotaPokjaRepository,
        private readonly AnggotaPokjaScopeService $anggotaPokjaScopeService
    ) {
    }

    public function execute(int $id, string $level): AnggotaPokja
    {
        $anggotaPokja = $this->anggotaPokjaRepository->find($id);
        $areaId = $this->anggotaPokjaScopeService->requireUserAreaId();

        return $this->anggotaPokjaScopeService->authorizeSameLevelAndArea($anggotaPokja, $level, $areaId);
    }
}

