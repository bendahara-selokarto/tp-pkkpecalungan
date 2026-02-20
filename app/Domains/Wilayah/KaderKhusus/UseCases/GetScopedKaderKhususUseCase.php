<?php

namespace App\Domains\Wilayah\KaderKhusus\UseCases;

use App\Domains\Wilayah\KaderKhusus\Models\KaderKhusus;
use App\Domains\Wilayah\KaderKhusus\Repositories\KaderKhususRepositoryInterface;
use App\Domains\Wilayah\KaderKhusus\Services\KaderKhususScopeService;

class GetScopedKaderKhususUseCase
{
    public function __construct(
        private readonly KaderKhususRepositoryInterface $kaderKhususRepository,
        private readonly KaderKhususScopeService $kaderKhususScopeService
    ) {
    }

    public function execute(int $id, string $level): KaderKhusus
    {
        $kaderKhusus = $this->kaderKhususRepository->find($id);
        $areaId = $this->kaderKhususScopeService->requireUserAreaId();

        return $this->kaderKhususScopeService->authorizeSameLevelAndArea($kaderKhusus, $level, $areaId);
    }
}
