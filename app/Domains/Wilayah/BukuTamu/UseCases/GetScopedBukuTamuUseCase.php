<?php

namespace App\Domains\Wilayah\BukuTamu\UseCases;

use App\Domains\Wilayah\BukuTamu\Models\BukuTamu;
use App\Domains\Wilayah\BukuTamu\Repositories\BukuTamuRepositoryInterface;
use App\Domains\Wilayah\BukuTamu\Services\BukuTamuScopeService;

class GetScopedBukuTamuUseCase
{
    public function __construct(
        private readonly BukuTamuRepositoryInterface $bukuTamuRepository,
        private readonly BukuTamuScopeService $bukuTamuScopeService
    ) {
    }

    public function execute(int $id, string $level): BukuTamu
    {
        $item = $this->bukuTamuRepository->find($id);
        $areaId = $this->bukuTamuScopeService->requireUserAreaId();

        return $this->bukuTamuScopeService->authorizeSameLevelAndArea($item, $level, $areaId);
    }
}
