<?php

namespace App\Domains\Wilayah\KaderKhusus\UseCases;

use App\Domains\Wilayah\KaderKhusus\Repositories\KaderKhususRepositoryInterface;
use App\Domains\Wilayah\KaderKhusus\Services\KaderKhususScopeService;

class ListScopedKaderKhususUseCase
{
    public function __construct(
        private readonly KaderKhususRepositoryInterface $kaderKhususRepository,
        private readonly KaderKhususScopeService $kaderKhususScopeService
    ) {
    }

    public function execute(string $level)
    {
        $areaId = $this->kaderKhususScopeService->requireUserAreaId();

        return $this->kaderKhususRepository->getByLevelAndArea($level, $areaId);
    }
}
