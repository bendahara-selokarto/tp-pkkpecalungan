<?php

namespace App\Domains\Wilayah\KaderKhusus\UseCases;

use App\Domains\Wilayah\KaderKhusus\Repositories\KaderKhususRepositoryInterface;
use App\Domains\Wilayah\KaderKhusus\Services\KaderKhususScopeService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class ListScopedKaderKhususUseCase
{
    public function __construct(
        private readonly KaderKhususRepositoryInterface $kaderKhususRepository,
        private readonly KaderKhususScopeService $kaderKhususScopeService
    ) {
    }

    public function execute(string $level, int $perPage): LengthAwarePaginator
    {
        $areaId = $this->kaderKhususScopeService->requireUserAreaId();

        return $this->kaderKhususRepository->paginateByLevelAndArea($level, $areaId, $perPage);
    }

    public function executeAll(string $level): Collection
    {
        $areaId = $this->kaderKhususScopeService->requireUserAreaId();

        return $this->kaderKhususRepository->getByLevelAndArea($level, $areaId);
    }
}
